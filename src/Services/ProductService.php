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
        $data['prices'] = Arr::get($data, 'prices', []);

        $attributes = attributes_filter($data, [
            'code',
            'name',
            'active',
            'shippable',
            'metadata',
            'prices',
        ]);

        $product = ProductBuilder::init()->fill($attributes)->end();

        EntityCreated::dispatch($product);

        foreach ($product->prices as $price) {
            EntityCreated::dispatch($price);
        }

        return $product;
    }

    public function update(Product $product, array $data): Product
    {
        $attributes = attributes_filter($data, [
            'code',
            'name',
            'active',
            'shippable',
            'metadata',
        ]);

        $product = ProductBuilder::init($product)->fill($attributes)->end();

        EntityUpdated::dispatch($product);

        return $product;
    }

    public function delete(Product $product)
    {
        if (!$product->canBeDeleted()) {
            throw new InvalidEntityException("Product [{$product->id} cannot be deleted]");
        }

        $product->delete();

        EntityDeleted::dispatch($product);
    }
}