<?php

namespace Eyes360\VoomCrmIntegrationClient;

class UnitHelper
{
    public static function createUnit(
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