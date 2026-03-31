<?php

use Eyes360\VoomIntegrationSdk\Client;
use Eyes360\VoomIntegrationSdk\Unit;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Middleware;

beforeEach(function () {
    $this->basicAuth = ['username' => 'test-user', 'password' => 'test-password'];
    $this->client = new Client('test-id', 'test-secret', $this->basicAuth);
    
    // We use Guzzle MockHandler to mock outgoing requests without hitting an actual API
    $this->mockHandler = new MockHandler();
    $this->container = [];
    $history = Middleware::history($this->container);
    
    $handlerStack = HandlerStack::create($this->mockHandler);
    $handlerStack->push($history);
    
    $this->guzzleMock = new GuzzleClient(['handler' => $handlerStack]);
    
    // Inject the mocked guzzle instance into the $client using Reflection
    $reflection = new \ReflectionClass($this->client);
    $property = $reflection->getProperty('guzzle');
    $property->setAccessible(true);
    $property->setValue($this->client, $this->guzzleMock);
});

test('it should be initialized with the correct base URL', function () {
    expect($this->client->getBaseUrl())->toBe(Client::DEFAULT_BASE_URL);
});

test('it should change the base URL when setBaseUrl is called', function () {
    $newUrl = 'https://new-api.example.com';
    $this->client->setBaseUrl($newUrl);
    expect($this->client->getBaseUrl())->toBe($newUrl);
});

test('it should generate signature and make bulk-push request', function () {
    $mockData = ['success' => true];
    
    // Setup a 200 OK mock response
    $this->mockHandler->append(
        new Response(200, [], json_encode($mockData))
    );

    $testUnit = Unit::make(
        'unit-123',
        'tenant-1',
        'project-1',
        'Sample Unit',
        'apartment',
        'A-101',
        'available',
        120.5,
        3,
        250000
    );
    $testUnits = [$testUnit];
    
    $result = $this->client->bulkPush($testUnits);

    expect($result)->toBe($mockData);
    
    // Extract request details to assert correct data and headers were sent
    expect(count($this->container))->toBe(1);
    $transaction = $this->container[0];
    $request = $transaction['request'];
    
    expect($request->getMethod())->toBe('POST');
    expect((string) $request->getUri())->toContain(Client::API_BULK_PUSH);
    
    $body = json_decode((string) $request->getBody(), true);
    expect($body)->toEqual(['units' => $testUnits]);
    
    expect($request->hasHeader('X-Client-Id'))->toBeTrue();
    expect($request->getHeaderLine('X-Client-Id'))->toBe('test-id');
    expect($request->hasHeader('X-Request-Id'))->toBeTrue();
    expect($request->hasHeader('X-Request-Time'))->toBeTrue();
    expect($request->hasHeader('X-Request-Signature'))->toBeTrue();
});

test('it should generate signature and make hello request', function () {
    $mockData = ['success' => true];
    
    $this->mockHandler->append(
        new Response(200, [], json_encode($mockData))
    );

    $result = $this->client->hello();

    expect($result)->toBe($mockData);
    
    expect(count($this->container))->toBe(1);
    $transaction = $this->container[0];
    $request = $transaction['request'];
    
    expect($request->getMethod())->toBe('POST');
    expect((string) $request->getUri())->toContain(Client::API_HELLO);
    
    expect($request->hasHeader('X-Client-Id'))->toBeTrue();
    expect($request->hasHeader('X-Request-Id'))->toBeTrue();
    expect($request->hasHeader('X-Request-Time'))->toBeTrue();
    expect($request->hasHeader('X-Request-Signature'))->toBeTrue();
});

test('it should generate signature and make get-units request', function () {
    $mockData = ['units' => []];
    
    $this->mockHandler->append(
        new Response(200, [], json_encode($mockData))
    );

    $result = $this->client->getUnits();

    expect($result)->toBe($mockData);
    
    expect(count($this->container))->toBe(1);
    $transaction = $this->container[0];
    $request = $transaction['request'];
    
    expect($request->getMethod())->toBe('POST');
    expect((string) $request->getUri())->toContain(Client::API_GET_UNITS);
    
    expect($request->hasHeader('X-Client-Id'))->toBeTrue();
    expect($request->hasHeader('X-Request-Id'))->toBeTrue();
    expect($request->hasHeader('X-Request-Time'))->toBeTrue();
    expect($request->hasHeader('X-Request-Signature'))->toBeTrue();
});

test('it should use basic auth when enabled and omit HMAC headers', function () {
    $mockData = ['success' => true];
    
    $this->mockHandler->append(
        new Response(200, [], json_encode($mockData))
    );

    $this->client->useBasicAuth(true);

    $result = $this->client->hello();

    expect($result)->toBe($mockData);

    // Assert that the signature headers were ommitted but the auth basic parameter is present
    expect(count($this->container))->toBe(1);
    $transaction = $this->container[0];
    $request = $transaction['request'];
    $options = $transaction['options'];
    
    expect($request->hasHeader('X-Client-Id'))->toBeFalse();
    expect($request->hasHeader('X-Request-Id'))->toBeFalse();
    expect($request->hasHeader('X-Request-Time'))->toBeFalse();
    expect($request->hasHeader('X-Request-Signature'))->toBeFalse();
    
    expect($options['auth'])->toBe(['test-user', 'test-password']);
});

test('it should use basic auth for get-units when enabled', function () {
    $mockData = ['units' => []];
    
    $this->mockHandler->append(
        new Response(200, [], json_encode($mockData))
    );

    $this->client->useBasicAuth(true);

    $result = $this->client->getUnits();

    expect($result)->toBe($mockData);

    expect(count($this->container))->toBe(1);
    $transaction = $this->container[0];
    $request = $transaction['request'];
    $options = $transaction['options'];
    
    expect($request->hasHeader('X-Client-Id'))->toBeFalse();
    expect($options['auth'])->toBe(['test-user', 'test-password']);
});
