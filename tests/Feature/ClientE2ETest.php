<?php

use Eyes360\VoomIntegrationSdk\Client;

beforeEach(function () {
    // Using credentials from previous conversations for valid E2E testing
    $clientId = 'test client';
    $clientSecret = 'test secret';
    
    $this->client = new Client($clientId, $clientSecret);
});

test('it can call the hello endpoint successfully', function () {
    $response = $this->client->hello();
    
    expect($response)->toBeArray();
    expect($response)->toHaveKey('success', true);
});

test('it can fetch units successfully', function () {
    $response = $this->client->getUnits();
    
    expect($response)->toBeArray();
    expect($response)->toHaveKey('success', true);
    expect($response)->toHaveKey('data');
    expect($response['data'])->toBeArray(); // The response contains an array of units
});

test('it can perform a bulk push of units successfully', function () {
    $units = [
        [
            'unit_id' => 'SDK-E2E-TEST-' . time(),
            'tenant_id' => 'test-tenant',
            'project_id' => 'test-project',
            'name' => 'E2E Test Unit',
            'type' => 'apartment',
            'code' => 'TST-001',
            'availability' => 'available',
            'area' => 120.5,
            'bedrooms' => 3,
            'price' => 500000.00,
        ]
    ];
    
    $response = $this->client->bulkPush($units);
    
    expect($response)->toBeArray();
    expect($response)->toHaveKey('success', true);
});
