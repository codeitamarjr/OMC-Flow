<?php

namespace App\Services\Core;

use Codeitamarjr\LaravelCroApi\CroApiClient;
use Illuminate\Support\Facades\Log;
use Throwable;

class CroSearchService
{
    public function __construct(private readonly CroApiClient $client)
    {
    }

    public function searchByNumber(string $number): array
    {
        return $this->client->searchByNumber($number);
    }

    public function getCompanyDetails(string $number): array
    {
        return $this->client->getCompanyDetails($number);
    }

    public function getCompanySubmissions(string $number): array
    {
        try {
            $result = $this->client->getCompanySubmissions($number);

            return is_array($result) ? $result : [];
        } catch (Throwable $e) {
            Log::warning('CRO getCompanySubmissions failed; returning empty result', [
                'company_number' => $number,
                'message' => $e->getMessage(),
            ]);

            return [];
        }
    }

    public function searchCompanySubmissions(string $companyNumber, string $busIndicator = 'c'): array
    {
        try {
            $result = $this->client->searchCompanySubmissions($companyNumber, $busIndicator);

            return is_array($result) ? $result : [];
        } catch (Throwable $e) {
            Log::warning('CRO searchCompanySubmissions failed; returning empty result', [
                'company_number' => $companyNumber,
                'bus_indicator' => $busIndicator,
                'message' => $e->getMessage(),
            ]);

            return [];
        }
    }
}
