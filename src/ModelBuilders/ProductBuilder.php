<?php

namespace EnricoNardo\EcommerceLayer\ModelBuilders;

use EnricoNardo\EcommerceLayer\Models\Price;
use EnricoNardo\EcommerceLayer\Models\Product;

class ProductBuilder extends BaseBuilder
{
    public static function getModelClass(): string
    {
        return Product::class;
    }

    /**
     * @param Price|string|int $customer
     * @return $this
     */
    public function withPrice(Price|string|int $price)
    {
        /** @var Product $model */
        $model = $this->model;

        if (is_string($price) || is_int($price)) {
            $price = Price::find($price);
        }

        $model->save();

        if ($price instanceof Price) {
            $model->prices()->save($price);
        }

        $this->model = $model;

        return $this;
    }
}