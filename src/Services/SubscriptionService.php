<?php

namespace EcommerceLayer\Services;

use Carbon\Carbon;
use EcommerceLayer\Enums\OrderStatus;
use EcommerceLayer\Enums\PaymentStatus;
use EcommerceLayer\Enums\SubscriptionStatus;
use EcommerceLayer\Events\Entity\EntityCreated;
use EcommerceLayer\Events\Entity\EntityDeleted;
use EcommerceLayer\Events\Entity\EntityUpdated;
use EcommerceLayer\Exceptions\InvalidEntityException;
use EcommerceLayer\ModelBuilders\SubscriptionBuilder;
use EcommerceLayer\Models\Order;
use EcommerceLayer\Models\Price;
use EcommerceLayer\Models\Subscription;
use Exception;
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

        $data['status'] = Arr::get($data, 'status', SubscriptionStatus::PENDING);
        $data['started_at'] = Arr::get($data, 'started_at', Carbon::now());

        $attributes = attributes_filter($data, [
            'customer_id',
            'price_id',
            'status',
            'started_at',
            'expires_at',
            'source_order_id',
        ]);

        $subscription = SubscriptionBuilder::init()->fill($attributes)->end();

        EntityCreated::dispatch($subscription);

        return $subscription;
    }

    public function update(Subscription $subscription, array $data): Subscription
    {
        $attributes = attributes_filter($data, [
            'customer_id',
            'status',
            'started_at',
            'expires_at',
            'source_order_id'
        ]);

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

    public function renew(Subscription $subscription)
    {
        /** @var OrderService $orderService */
        $orderService = resolve(OrderService::class);

        /** @var LineItemService $lineItemService */
        $lineItemService = resolve(LineItemService::class);

        /** @var Order $order */
        $sourceOrder = $subscription->source_order;

        try {
            // Generate the new order for the renew
            /** @var Order $newOrder */
            $newOrder = $orderService->create([
                'gateway_id' => $sourceOrder->gateway_id,
                'customer_id' => $sourceOrder->customer_id,
                'currency' => $sourceOrder->currency,
                'billing_address' => $sourceOrder->billing_address,
                'payment_method' => $sourceOrder->payment_method,
                'payment_status' => PaymentStatus::UNPAID
            ]);

            // Add the line item to the new order
            $lineItemService->create([
                'price_id' => $subscription->price_id,
                'quantity' => 1
            ], $newOrder);

            // Open the new order (otherwise it can't be paid)
            $newOrder = $orderService->update($newOrder, [
                'status' => OrderStatus::OPEN,
            ]);

            // Update the source order of the subscription with the new order
            $this->update($subscription, [
                'source_order_id' => $newOrder->id,
            ]);

            // Pay the order
            $newOrder = $orderService->pay($newOrder, ['off_session' => true]);

            // The status of the subscription will be handled based on the status of the payment.
        } catch (Exception $e) {
            // As an error has thrown, set subscription status as pending until the error has been fixed
            $this->update($subscription, [
                'status' => SubscriptionStatus::PENDING,
            ]);

            throw $e;
        }
    }
}
