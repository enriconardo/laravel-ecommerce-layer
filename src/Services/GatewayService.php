<?php

namespace EcommerceLayer\Services;

use EcommerceLayer\Events\Entity\EntityCreated;
use EcommerceLayer\Events\Entity\EntityDeleted;
use EcommerceLayer\Events\Entity\EntityUpdated;
use EcommerceLayer\ModelBuilders\GatewayBuilder;
use EcommerceLayer\Models\Gateway;
use Illuminate\Support\Arr;

class GatewayService
{
    public function create(array $data): Gateway
    {
        $attributes = [
            'name' => Arr::get($data, 'name'),
            'identifier' => Arr::get($data, 'identifier'),
        ];

        $gateway = GatewayBuilder::init()->fill($attributes)->end();

        EntityCreated::dispatch($gateway);

        return $gateway;
    }

    public function update(Gateway $gateway, array $data): Gateway
    {
        $attributes = [
            'name' => Arr::get($data, 'name'),
            'identifier' => Arr::get($data, 'identifier'),
        ];

        $gateway = GatewayBuilder::init($gateway)->fill($attributes)->end();

        EntityUpdated::dispatch($gateway);

        return $gateway;
    }

    public function delete(Gateway $gateway)
    {
        $gateway->delete();

        EntityDeleted::dispatch($gateway);
    }
}