<?php

namespace Eyes360\VoomIntegrationSdk;

use Ramsey\Uuid\Uuid;

class Client
{

    const DEFAULT_BASE_URL = 'https://crm-integration.voomproject.com';

    const API_HELLO = '/api/client-api/v1/hello';
    const API_BULK_PUSH = '/api/client-api/v1/inventory/bulk-push';
    const API_GET_UNITS = '/api/client-api/v1/inventory/get-units';

    protected $baseUrl = Client::DEFAULT_BASE_URL;
    protected $clientId;
    protected $clientSecret;
    protected $basicAuth;
    protected $isBasicAuthEnabled = false;
    protected $guzzle;

    public function __construct(
        string $clientId,
        string $clientSecret,
        ?array $basicAuth = null
    ) {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->basicAuth = $basicAuth;
        $this->guzzle = new \GuzzleHttp\Client();
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function setBaseUrl(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    public function useBasicAuth(bool $enable = true): void
    {
        if ($enable && !$this->basicAuth) {
            throw new \Exception('Basic Auth credentials must be provided in the constructor to enable it.');
        }
        $this->isBasicAuthEnabled = $enable;
    }

    public function generateApiSignature(
        string $clientID,
        string $requestId,
        string $requestTimeString,
        string $clientSecret
    ): string {
        $stringToSign = $clientID . $requestId . $requestTimeString;
        $binary_signature = hash_hmac('sha256', $stringToSign, $clientSecret, true);
        return base64_encode($binary_signature);
    }


    public function callApi(string $method, string $path, array|null $data = null): array
    {
        $headers = [];
        $options = ['json' => $data];

        if (!$this->isBasicAuthEnabled) {
        $client_id = $this->clientId;
        $client_secret = $this->clientSecret;
        $request_id = Uuid::getFactory()->uuid4()->toString();
        $request_time_string = date(DATE_RFC3339);

        $signature = $this->generateApiSignature(
            $client_id,
            $request_id,
            $request_time_string,
            $client_secret
        );

            $headers['X-Client-Id'] = $client_id;
            $headers['X-Request-Id'] = $request_id;
            $headers['X-Request-Time'] = $request_time_string;
            $headers['X-Request-Signature'] = $signature;
        }

        if ($this->isBasicAuthEnabled && $this->basicAuth) {
            $options['auth'] = [$this->basicAuth['username'] ?? '', $this->basicAuth['password'] ?? ''];
        }

        if (!empty($headers)) {
            $options['headers'] = $headers;
        }

        $res = $this->guzzle->request($method, $this->baseUrl .  $path, $options);

        return json_decode($res->getBody()->getContents(), true);
    }

    /**
     * Summary of bulkPush
     * @param array $units
     * @return void
     */
    public function bulkPush(array $units): array
    {
        return $this->callApi('POST', self::API_BULK_PUSH, ['units' => $units]);
    }

    public function hello(): array
    {
        return $this->callApi('POST', self::API_HELLO);
    }

    public function getUnits(): array
    {
        return $this->callApi('POST', self::API_GET_UNITS);
    }
}