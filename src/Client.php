<?php

namespace Eyes360\VoomCrmIntegrationClient;

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

    protected $guzzle;

    public function __construct(
        string $clientId,
        string $clientSecret
    ) {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
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

        $res = $this->guzzle->request($method, $this->baseUrl .  $path, [
            'json' => $data,
            'headers' => [
                'X-Client-Id' => $client_id,
                'X-Request-Id' => $request_id,
                'X-Request-Time' => $request_time_string,
                'X-Request-Signature' => $signature
            ]
        ]);

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