<?php

namespace EcommerceLayer\Services;

use EcommerceLayer\Exceptions\InvalidEntityException;
use EcommerceLayer\ModelBuilders\PriceBuilder;
use EcommerceLayer\Models\Price;
use EcommerceLayer\Events\Entity\EntityCreated;
use EcommerceLayer\Events\Entity\EntityDeleted;
use EcommerceLayer\Events\Entity\EntityUpdated;
use Illuminate\Support\Arr;

class PriceService
{
    public function create(array $data): Price
    {
        $attributes = [
            'product_id' => Arr::get($data, 'product_id'),
            'unit_amount' => Arr::get($data, 'unit_amount'),
            'currency' => Arr::get($data, 'currency'),
            'description' => Arr::get($data, 'description'),
            'active' => Arr::get($data, 'active'),
            'default' => Arr::get($data, 'default'),
            'recurring' => Arr::get($data, 'recurring'),
            'plan' => Arr::get($data, 'plan'),
        ];

        $price = PriceBuilder::init()->fill($attributes)->end();

        EntityCreated::dispatch($price);

        return $price;
    }

    public function update(Price $price, array $data): Price
    {
        if (!$price->canBeUpdated()) {
            throw new InvalidEntityException("Price [{$price->id} cannot be updated]");
        }

        $attributes = [
            'unit_amount' => Arr::get($data, 'unit_amount'),
            'currency' => Arr::get($data, 'currency'),
            'description' => Arr::get($data, 'description'),
            'active' => Arr::get($data, 'active'),
            'default' => Arr::get($data, 'default'),
            'recurring' => Arr::get($data, 'recurring'),
            'plan' => Arr::get($data, 'plan'),
        ];

        $price = PriceBuilder::init($price)->fill($attributes)->end();

        EntityUpdated::dispatch($price);

        return $price;
    }

    public function delete(Price $price)
    {
        if (!$price->canBeUpdated()) {
            throw new InvalidEntityException("Price [{$price->id} cannot be deleted]");
        }

        $price->delete();

        EntityDeleted::dispatch($price);
    }
}