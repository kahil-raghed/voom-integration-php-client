# Voom Integration Sdk

Used to update units

## Installation
```bash
composer require eyes360/voom-integration-sdk
```

## Usage

```php

<?php

use Eyes360\VoomIntegrationSdk\Client;
use Eyes360\VoomIntegrationSdk\Unit;

$client = new Client($clientID, $clientSecret);

// test connection
$client->hello();

$client->bulkPush([
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
    ),
]);
```
