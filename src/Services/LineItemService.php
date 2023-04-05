<?php

namespace EcommerceLayer\Services;

use EcommerceLayer\Events\Entity\EntityCreated;
use EcommerceLayer\Events\Entity\EntityDeleted;
use EcommerceLayer\Events\Entity\EntityUpdated;
use EcommerceLayer\Exceptions\InvalidEntityException;
use EcommerceLayer\ModelBuilders\LineItemBuilder;
use EcommerceLayer\Models\LineItem;
use EcommerceLayer\Models\Order;
use EcommerceLayer\Models\Price;
use Illuminate\Support\Arr;

class LineItemService
{
    public function create(array $data, Order $order): LineItem
    {
        /** @var Price $price */
        $price = Price::find(Arr::get($data, 'price_id'));

        $lineItem = LineItem::where('price_id', $price->id)->where('order_id', $order->id)->first();

        if ($lineItem) {
            // The same line item already exists, so update it instead of create a new one
            return $this->update($lineItem, $data);
        }

        if (!$order->canBeUpdated()) {
            throw new InvalidEntityException("Order [{$order->id}] cannot be updated");
        }

        $quantity = Arr::get($data, 'quantity');

        if ($price->recurring && $quantity > 1) {
            throw new InvalidEntityException("You cannot add a recurring product more than once in a order");
        }

        $attributes = [
            'quantity' => $quantity,
            'price_id' => $price->id,
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

        $quantity = Arr::get($data, 'quantity');

        if ($lineItem->price->recurring && $quantity > 1) {
            throw new InvalidEntityException("You cannot add a recurring product more than once in a order");
        }

        $attributes = [
            'quantity' => $quantity,
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