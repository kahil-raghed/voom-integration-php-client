<?php

use Eyes360\VoomCrmIntegrationClient\Client;

function prepareClient() {
    $base = getenv('BASE_URL') ?? "http://localhost:8080";
    $clientID = getenv('CLIENT_ID');
    $clientSecret = getenv('CLIENT_SECRET');
    $client = new Client($clientID, $clientSecret);
    $client->setBaseUrl($base);
    return $client;
}

test('test connection', function () {
    $client = prepareClient();
    $response = $client->hello();

    expect($response)->toBeArray();
    expect($response['data'])->toBe('Hello');
});
