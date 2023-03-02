<?php

namespace EnricoNardo\EcommerceLayer\Services;

use EnricoNardo\EcommerceLayer\ModelBuilders\ProductBuilder;
use EnricoNardo\EcommerceLayer\Models\Product;
use EnricoNardo\EcommerceLayer\Events\Entity\EntityCreated;
use EnricoNardo\EcommerceLayer\Events\Entity\EntityDeleted;
use EnricoNardo\EcommerceLayer\Events\Entity\EntityUpdated;
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
        $product->delete();

        EntityDeleted::dispatch($product);
    }
}