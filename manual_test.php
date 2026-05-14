<?php

require_once __DIR__ . '/vendor/autoload.php';

use Eyes360\VoomIntegrationSdk\Client;

// Credentials provided by the user
$clientId = 'test client';
$clientSecret = 'test secret';

echo "Initializing Voom Integration SDK Client...\n";
$client = new Client($clientId, $clientSecret);

// Optional: Set a different base URL if needed
// $client->setBaseUrl('https://crm-integration.voomproject.com');

try {
    echo "\n--- Testing Hello Endpoint ---\n";
    $helloResponse = $client->hello();
    echo "Response: " . json_encode($helloResponse, JSON_PRETTY_PRINT) . "\n";

    echo "\n--- Testing Get Units Endpoint ---\n";
    $unitsResponse = $client->getUnits();
    echo "Response: " . json_encode($unitsResponse, JSON_PRETTY_PRINT) . "\n";

    echo "\n--- Testing Bulk Push Endpoint ---\n";
    $units = [
        [
            'unit_id' => 'SDK-TEST-' . time(),
            'tenant_id' => 'test-tenant',
            'project_id' => 'test-project',
            'name' => 'Manual Test Unit',
            'type' => 'apartment',
            'code' => 'TST-001',
            'availability' => 'available',
            'area' => 120.5,
            'bedrooms' => 3,
            'price' => 500000.00,
        ]
    ];
    $pushResponse = $client->bulkPush($units);
    echo "Response: " . json_encode($pushResponse, JSON_PRETTY_PRINT) . "\n";


} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    if (method_exists($e, 'getResponse') && $e->getResponse()) {
        echo "Response Body: " . $e->getResponse()->getBody()->getContents() . "\n";
    }
}
