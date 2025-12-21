<?php

namespace Eyes360\VoomIntegrationSdk;

class Unit
{
    private function __construct() {}
    public static function make(
        string $unit_id,
        string $tenant_id,
        string $project_id,
        string $name,
        string $type,
        string $code,
        string $availability,
        float $area,
        int $bedrooms,
        float $price,
        array|object|null $data = null,
    ) {
        return compact(
            'unit_id',
            'tenant_id',
            'project_id',
            'name',
            'type',
            'code',
            'availability',
            'area',
            'bedrooms',
            'price',
        );
    }
}