<?php

namespace EcommerceLayer\Services;

use Carbon\Carbon;
use EcommerceLayer\Enums\SubscriptionStatus;
use EcommerceLayer\Events\Entity\EntityCreated;
use EcommerceLayer\Events\Entity\EntityDeleted;
use EcommerceLayer\Events\Entity\EntityUpdated;
use EcommerceLayer\Exceptions\InvalidEntityException;
use EcommerceLayer\ModelBuilders\SubscriptionBuilder;
use EcommerceLayer\Models\Price;
use EcommerceLayer\Models\Plan;
use EcommerceLayer\Models\Subscription;
use Illuminate\Support\Arr;

class SubscriptionService
{
    public function create(array $data): Subscription
    {
        /** @var Price $price */
        $price = Price::find(Arr::get($data, 'price_id'));

        if (!$price->recurring) {
            throw new InvalidEntityException("Subscription cannot be created because the chosen Price is not recurring]");
        }

        $startedAt = Arr::get($data, 'started_at', Carbon::now());

        $attributes = [
            'customer_id' => Arr::get($data, 'customer_id'),
            'price_id' => $price->id,
            'status' => Arr::get($data, 'status', SubscriptionStatus::PENDING),
            'started_at' => $startedAt,
            'expires_at' => Arr::get($data, 'expires_at'),
            'source_order_id' => Arr::get($data, 'source_order_id')
        ];

        $subscription = SubscriptionBuilder::init()->fill($attributes)->end();

        EntityCreated::dispatch($subscription);

        return $subscription;
    }

    public function update(Subscription $subscription, array $data): Subscription
    {
        $attributes = [
            'customer_id' => Arr::get($data, 'customer_id'),
            'status' => Arr::get($data, 'status'),
            'started_at' => Arr::get($data, 'started_at'),
            'expires_at' => Arr::get($data, 'expires_at'),
            'source_order_id' => Arr::get($data, 'source_order_id')
        ];

        $subscription = SubscriptionBuilder::init($subscription)->fill($attributes)->end();

        EntityUpdated::dispatch($subscription);

        return $subscription;
    }

    public function delete(Subscription $subscription)
    {
        if ($subscription->canBeDeleted()) {
            throw new InvalidEntityException("Subscription [{$subscription->id} cannot be deleted]");
        }

        $subscription->delete();

        EntityDeleted::dispatch($subscription);
    }
}