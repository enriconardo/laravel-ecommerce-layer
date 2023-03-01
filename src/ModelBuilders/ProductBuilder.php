<?php

namespace EnricoNardo\EcommerceLayer\ModelBuilders;

use EnricoNardo\EcommerceLayer\Models\Price;
use EnricoNardo\EcommerceLayer\Models\Product;
use Exception;
use Illuminate\Support\Arr;

class ProductBuilder extends BaseBuilder
{
    public static function getModelClass(): string
    {
        return Product::class;
    }

    /**
     * @param array $attributes
     * @return $this
     * @throws Exception
     */
    public function fill(array $attributes)
    {
        $prices = null;
        if (Arr::has($attributes, 'prices')) {
            $prices = Arr::pull($attributes, 'prices');
        }

        parent::fill($attributes);

        if ($prices) {
            try {
                $this->savePrices($prices);
            } catch (Exception $e) {
                $this->abort();
                throw $e;
            }
        }

        return $this;
    }

    protected function savePrices($prices)
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
    }
}
