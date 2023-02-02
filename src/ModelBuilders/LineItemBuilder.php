<?php

namespace EnricoNardo\EcommerceLayer\ModelBuilders;

use EnricoNardo\EcommerceLayer\Models\LineItem;
use EnricoNardo\EcommerceLayer\Models\Order;
use EnricoNardo\EcommerceLayer\Models\Price;

class LineItemBuilder extends BaseBuilder
{
    public static function getModelClass(): string
    {
        return LineItem::class;
    }

    /**
     * @param Price|string|int $price
     * @return $this
     */
    public function withPrice(Price|string|int $price)
    {
        /** @var LineItem $model */
        $model = $this->model;
        $model->save();

        if (is_string($price) || is_int($price)) {
            $price = Price::find($price);
        }

        if ($price instanceof Price) {
            $model->price()->associate($price);
        }

        $this->model = $model;

        return $this;
    }

    /**
     * @param Order|string|int $order
     * @return $this
     */
    public function withOrder(Order|string|int $order)
    {
        /** @var LineItem $model */
        $model = $this->model;
        $model->save();

        if (is_string($order) || is_int($order)) {
            $order = Order::find($order);
        }

        if ($order instanceof Order) {
            $order->order()->associate($order);
        }

        $this->model = $model;

        return $this;
    }
}
