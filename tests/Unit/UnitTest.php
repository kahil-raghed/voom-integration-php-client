<?php

use Eyes360\VoomIntegrationSdk\Unit;

test('it should create a unit correctly with all provided properties', function () {
    $unit = Unit::make(
        'U123',
        'T456',
        'P789',
        'Green Apartment',
        'Apartment',
        'GA-01',
        'Available',
        120.5,
        3,
        500000
    );

    expect($unit)->toBe([
        'unit_id' => 'U123',
        'tenant_id' => 'T456',
        'project_id' => 'P789',
        'name' => 'Green Apartment',
        'type' => 'Apartment',
        'code' => 'GA-01',
        'availability' => 'Available',
        'area' => 120.5,
        'bedrooms' => 3,
        'price' => 500000.0,
    ]);
});

test('it should handle floating point area and price', function () {
    $unit = Unit::make(
        'id', 'tid', 'pid', 'name', 'type', 'code', 'avail',
        99.99, 1, 1000.50
    );

    expect($unit['area'])->toBe(99.99);
    expect($unit['price'])->toBe(1000.50);
});
