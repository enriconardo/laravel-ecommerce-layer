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
     * @param Price|array $price
     * @return $this
     */
    public function withPrice(Price|array $price)
    {
        /** @var Product $model */
        $model = $this->model;
        $model->save();

        if (is_array($price)) {
            $price = PriceBuilder::init(null, false)->fill($price)->getModel();
        }

        if ($price instanceof Price) {
            $model->prices()->save($price);
        }

        $this->model = $model;

        return $this;
    }

    /**
     * @param array $prices
     * @return $this
     */
    public function withPrices(array $prices)
    {
        /** @var Product $model */
        $model = $this->model;
        $model->save();

        $pricesToSave = [];

        foreach ($prices as $price) {
            if (is_array($price)) {
                $price = PriceBuilder::init(null, false)->fill($price)->getModel();
            }
    
            if ($price instanceof Price) {
                $pricesToSave[] = $price;
            }
        }

        if (count($pricesToSave)) {
            $model->prices()->saveMany($pricesToSave);
        }

        $this->model = $model;

        return $this;
    }
}
