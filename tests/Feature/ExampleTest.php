<?php

use Eyes360\VoomIntegrationSdk\Client;
use Eyes360\VoomIntegrationSdk\Unit;

function prepareClient()
{
    $base = getenv('BASE_URL') ?? "http://localhost:8080";
    $clientID = getenv('CLIENT_ID');
    $clientSecret = getenv('CLIENT_SECRET');
    $client = new Client($clientID, $clientSecret);
    $client->setBaseUrl($base);
    return $client;
}

test('test connection', function () {
    $client = prepareClient();
    try {
        $response = $client->hello();
    } catch (\Exception $e) {
        $this->markTestSkipped('Server not reachable: ' . $e->getMessage());
    }

    expect($response)->toBeArray();
    expect($response['success'])->toBeTrue();
});

test("test push", function () {
    $client = prepareClient();
    try {
        $response = $client->bulkPush([
            Unit::make(
                '1234',
                'tenant_2',
                'project_1',
                'Unit 123',
                'residential',
                '123',
                'available',
                120,
                2,
                10000,
            )
        ]);
    } catch (\Exception $e) {
        $this->markTestSkipped('Server not reachable: ' . $e->getMessage());
    }

    expect($response['success'])->toBeTrue();
});