<?php

namespace EnricoNardo\EcommerceLayer\Services;

use EnricoNardo\EcommerceLayer\Exceptions\InvalidEntityException;
use EnricoNardo\EcommerceLayer\ModelBuilders\PriceBuilder;
use EnricoNardo\EcommerceLayer\Models\Price;
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

        return $price;
    }

    public function delete(Price $price)
    {
        if (!$price->canBeUpdated()) {
            throw new InvalidEntityException("Price [{$price->id} cannot be deleted]");
        }

        $price->delete();
    }
}