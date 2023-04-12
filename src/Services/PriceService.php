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
        $attributes = attributes_filter($data, [
            'product_id',
            'unit_amount',
            'currency',
            'description',
            'active',
            'default',
            'recurring',
            'plan',
        ]);

        $price = PriceBuilder::init()->fill($attributes)->end();

        EntityCreated::dispatch($price);

        return $price;
    }

    public function update(Price $price, array $data): Price
    {
        if (!$price->canBeUpdated()) {
            throw new InvalidEntityException("Price [{$price->id} cannot be updated]");
        }

        $attributes = attributes_filter($data, [
            'unit_amount',
            'currency',
            'description',
            'active',
            'default',
            'recurring',
            'plan',
        ]);

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