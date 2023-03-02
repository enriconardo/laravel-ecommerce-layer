<?php

namespace EcommerceLayer\Services;

use EcommerceLayer\Events\Entity\EntityCreated;
use EcommerceLayer\Events\Entity\EntityDeleted;
use EcommerceLayer\Events\Entity\EntityUpdated;
use EcommerceLayer\Exceptions\InvalidEntityException;
use EcommerceLayer\ModelBuilders\LineItemBuilder;
use EcommerceLayer\Models\LineItem;
use EcommerceLayer\Models\Order;
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

        EntityCreated::dispatch($lineItem);

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

        EntityUpdated::dispatch($lineItem);

        return $lineItem;
    }

    public function delete(LineItem $lineItem)
    {
        if (!$lineItem->order->canBeUpdated()) {
            throw new InvalidEntityException("Order [{$lineItem->order->id}] cannot be updated");
        }

        $lineItem->delete();

        EntityDeleted::dispatch($lineItem);
    }
}