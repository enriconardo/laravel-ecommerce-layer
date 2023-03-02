<?php

namespace EnricoNardo\EcommerceLayer\Services;

use EnricoNardo\EcommerceLayer\Exceptions\InvalidEntityException;
use EnricoNardo\EcommerceLayer\ModelBuilders\LineItemBuilder;
use EnricoNardo\EcommerceLayer\Models\LineItem;
use EnricoNardo\EcommerceLayer\Models\Order;
use Illuminate\Support\Arr;

class LineItemService
{
    public function create(array $data, Order $order): LineItem
    {
        if (!$order->canBeUpdated()) {
            throw new InvalidEntityException("Order [{$order->id}] cannot be updated");
        }

        $attributes = [
            'quantity' => Arr::get($data, 'quantity'),
            'price_id' => Arr::get($data, 'price_id'),
            'order_id' => $order->id,
        ];

        $lineItem = LineItemBuilder::init()->fill($attributes)->end();

        return $lineItem;
    }

    public function update(LineItem $lineItem, array $data): LineItem
    {
        if (!$lineItem->order->canBeUpdated()) {
            throw new InvalidEntityException("Order [{$lineItem->order->id}] cannot be updated");
        }

        $attributes = [
            'quantity' => Arr::get($data, 'quantity'),
        ];

        $lineItem = LineItemBuilder::init($lineItem)->fill($attributes)->end();

        return $lineItem;
    }

    public function delete(LineItem $lineItem)
    {
        if (!$lineItem->order->canBeUpdated()) {
            throw new InvalidEntityException("Order [{$lineItem->order->id}] cannot be updated");
        }

        $lineItem->delete();
    }
}