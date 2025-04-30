<?php

namespace Morningtrain\NETSEasy\Classes;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class NetsEasyClient
{
    private const TEST_URL = 'https://test.api.dibspayment.eu/';

    private const URL = 'https://api.dibspayment.eu/';

    private string $secretKey;

    private bool $isTest;

    public function __construct()
    {
        $this->secretKey = config('nets-easy.secret_key');
        $this->isTest = config('nets-easy.in_test_mode');
    }

    /**
     * @throws ConnectionException
     */
    public function get(string $endpoint): Response
    {
        return $this->defaultRequest()
            ->get($endpoint);
    }

    /**
     * @throws ConnectionException
     */
    public function post(string $endpoint, array|\JsonSerializable|null $body = null): Response
    {
        return $this->defaultRequest($body)
            ->post($endpoint);
    }

    /**
     * @throws ConnectionException
     */
    public function put(string $endpoint, array|\JsonSerializable|null $body = null): Response
    {
        return $this->defaultRequest($body)
            ->put($endpoint);
    }

    private function defaultRequest(array|\JsonSerializable|null $body = null): PendingRequest
    {
        return Http::withHeaders([
            'Authorization' => $this->secretKey,
            'Content-Type' => 'application/*+json',
        ])
            ->when(
                ! empty($body),
                fn (PendingRequest $request) => $request->withBody(json_encode($body))
            )
            ->baseUrl($this->isTest ? static::TEST_URL : static::URL);
    }
}
