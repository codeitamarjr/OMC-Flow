<?php

namespace App\Services\Core;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class CroSearchService
{
    protected string $baseUrl = 'https://services.cro.ie/cws';

    /**
     * Creates the Authorization header value for the CRO API.
     *
     * @return string
     */
    protected function authHeader(): string
    {
        return base64_encode(config('services.cro.email') . ':' . config('services.cro.key'));
    }

    /**
     * Gets the headers for the CRO API.
     *
     * @return array<string,string>
     */
    protected function headers(): array
    {
        return [
            'Authorization' => 'Basic ' . $this->authHeader(),
            'Accept' => 'application/json',
        ];
    }


    /**
     * Searches the CRO by company number.
     *
     * @param string $number
     * @return array
     * @throws \Exception
     */
    public function searchByNumber(string $number): array
    {
        $response = Http::withHeaders($this->headers())
            ->get("{$this->baseUrl}/companies", [
                'company_num' => $number,
                'company_bus_ind' => 'C',
                'format' => 'json',
            ]);
        return $response->json();
    }

    /**
     * Looks up the details of a company by its number.
     *
     * @param string $number The company number to look up.
     * @return array The company details.
     * @throws \Exception if the lookup fails.
     */
    public function getCompanyDetails(string $number): array
    {
        $response = Http::withHeaders($this->headers())
            ->get("{$this->baseUrl}/company/{$number}/c", [
                'format' => 'json'
            ]);
        return $response->json();
    }

    /**
     * Gets the submissions associated with a company.
     *
     * @param string $number The company number to look up.
     * @return array The submissions.
     * @throws \Exception if the lookup fails.
     */
    public function getCompanySubmissions(string $number): array
    {
        $response = Http::withHeaders($this->headers())
            ->get("{$this->baseUrl}/submissions/{$number}/c", [
                'format' => 'json'
            ]);

        return $response->json();
    }


    /**
     * Searches for company submissions using the CRO API.
     *
     * Retrieves a list of submissions for a specific company number, paginated
     * by a maximum number of results per request. The function handles potential
     * rate limiting by Cloudflare and logs relevant warnings or errors. It ensures
     * uniqueness in results by filtering out duplicate submission types.
     *
     * @param string $companyNumber The company number for which to fetch submissions.
     * @param string $busIndicator The business indicator to use in the request (default is 'c').
     * @return array A list of unique submissions, sorted by the received date.
     */
    public function searchCompanySubmissions(string $companyNumber, string $busIndicator = 'c'): array
    {
        $results = [];
        $skip = 0;
        $max = 100;

        do {
            $response = Http::withHeaders($this->headers())
                ->timeout(15)
                ->get("{$this->baseUrl}/submissions", [
                    'company_bus_ind' => strtoupper($busIndicator),
                    'company_num' => $companyNumber,
                    'skip' => $skip,
                    'max' => $max,
                    'format' => 'json',
                ]);

            $body = trim($response->body());

            if ($body === 'error code: 1015') {
                Log::warning("Cloudflare 1015 rate limit for {$companyNumber}. Sleeping 10s and retrying...");
                sleep(10);
                continue;
            }

            $batch = json_decode($body, true);
            if (!is_array($batch)) {
                Log::error('Invalid CRO submission response', ['response' => $body]);
                break;
            }

            $results = array_merge($results, $batch);
            $skip += $max;

            usleep(750 * 1000);
        } while (count($batch) === $max);

        usort($results, fn($a, $b) => strcmp($b['sub_received_date'] ?? '', $a['sub_received_date'] ?? ''));
        $seen = [];
        $latestUnique = [];

        foreach ($results as $doc) {
            $type = $doc['sub_type_desc'] ?? null;

            if ($type && !isset($seen[$type])) {
                $latestUnique[] = $doc;
                $seen[$type] = true;
            }
        }

        return $latestUnique;
    }
}
