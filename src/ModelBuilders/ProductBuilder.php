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
        parent::fill($attributes);

        try {
            $this->savePrices($attributes);
        } catch (Exception $e) {
            $this->abort();
            throw $e;
        }

        return $this;
    }

    protected function savePrices($attributes)
    {
        /** @var Product $model */
        $model = $this->model;
        $model->save();

        $prices = Arr::get($attributes, 'prices', []);

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
