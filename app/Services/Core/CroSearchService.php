<?php

namespace App\Services\Core;

use Codeitamarjr\LaravelCroApi\CroApiClient;

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
        return $this->client->getCompanySubmissions($number);
    }

    public function searchCompanySubmissions(string $companyNumber, string $busIndicator = 'c'): array
    {
        return $this->client->searchCompanySubmissions($companyNumber, $busIndicator);
    }
}
