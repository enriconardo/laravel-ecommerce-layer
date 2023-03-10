<?php

namespace EcommerceLayer\Services;

use EcommerceLayer\ModelBuilders\ProductBuilder;
use EcommerceLayer\Models\Product;
use EcommerceLayer\Events\Entity\EntityCreated;
use EcommerceLayer\Events\Entity\EntityDeleted;
use EcommerceLayer\Events\Entity\EntityUpdated;
use EcommerceLayer\Exceptions\InvalidEntityException;
use Illuminate\Support\Arr;

class ProductService
{
    public function create(array $data): Product
    {
        $attributes = [
            'code' => Arr::get($data, 'code'),
            'name' => Arr::get($data, 'name'),
            'active' => Arr::get($data, 'active'),
            'shippable' => Arr::get($data, 'shippable'),
            'metadata' => Arr::get($data, 'metadata'),
            'prices' => Arr::get($data, 'prices', [])
        ];

        $product = ProductBuilder::init()->fill($attributes)->end();

        EntityCreated::dispatch($product);

        foreach ($product->prices as $price) {
            EntityCreated::dispatch($price);
        }

        return $product;
    }

    public function update(Product $product, array $data): Product
    {
        $attributes = [
            'code' => Arr::get($data, 'code'),
            'name' => Arr::get($data, 'name'),
            'active' => Arr::get($data, 'active'),
            'shippable' => Arr::get($data, 'shippable'),
            'metadata' => Arr::get($data, 'metadata')
        ];

        $product = ProductBuilder::init($product)->fill($attributes)->end();

        EntityUpdated::dispatch($product);

        return $product;
    }

    public function delete(Product $product)
    {
        if (!$product->canBeUpdated()) {
            throw new InvalidEntityException("Product [{$product->id} cannot be deleted]");
        }

        $product->delete();

        EntityDeleted::dispatch($product);
    }
}