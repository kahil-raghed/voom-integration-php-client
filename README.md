# Voom Integration Sdk

Used to update units

## Usage



```php

<?php

use Eyes360\VoomIntegrationSdk\Client;

$client = new Client($clientID, $clientSecret);

// test connection
$client->hello();

$client->bulkPush([
    
]);
```