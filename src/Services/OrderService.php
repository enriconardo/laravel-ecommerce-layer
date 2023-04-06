<?php

namespace EcommerceLayer\Services;

use EcommerceLayer\Enums\FulfillmentStatus;
use EcommerceLayer\Enums\OrderStatus;
use EcommerceLayer\Enums\PaymentStatus;
use EcommerceLayer\Events\Order\OrderPlaced;
use EcommerceLayer\Exceptions\InvalidEntityException;
use EcommerceLayer\ModelBuilders\OrderBuilder;
use EcommerceLayer\Models\Order;
use Illuminate\Support\Arr;
use EcommerceLayer\Events\Entity\EntityCreated;
use EcommerceLayer\Events\Entity\EntityDeleted;
use EcommerceLayer\Events\Entity\EntityUpdated;
use EcommerceLayer\Events\Order\OrderCompleted;
use EcommerceLayer\Events\Payment\PaymentUpdated;
use EcommerceLayer\Models\PaymentData;

class OrderService
{
    public function create(array $data): Order
    {
        // When you create a new order it is a cart (status = DRAFT)
        $data['status'] = Arr::get($data, 'status', OrderStatus::DRAFT);

        $order = $this->_createOrUpdate($data);

        EntityCreated::dispatch($order);

        return $order;
    }

    public function update(Order $order, array $data): Order
    {
        if (!$order->canBeUpdated()) {
            throw new InvalidEntityException("Order [{$order->id}] cannot be updated");
        }

        $order = $this->_createOrUpdate($data, $order);

        EntityUpdated::dispatch($order);

        return $order;
    }

    public function delete(Order $order)
    {
        if (!$order->canBeDeleted()) {
            throw new InvalidEntityException("Order [{$order->id}] cannot be deleted");
        }

        $order->delete();

        EntityDeleted::dispatch($order);
    }

    public function place(Order $order, array $data = [])
    {
        if (!$order->canBePlaced()) {
            throw new InvalidEntityException("Order [{$order->id}] cannot be placed");
        }

        // When you place an order it is transformed to an OPEN order from a cart (DRAFT order)
        $data['status'] = OrderStatus::OPEN;

        $order = $this->_createOrUpdate($data, $order);

        OrderPlaced::dispatch($order);

        $order = $this->pay($order, Arr::get($data, 'return_url'));

        return $order;
    }

    public function pay(Order $order, $returnUrl = null): Order
    {
        if (!$order->canBePaid()) {
            throw new InvalidEntityException("Order [{$order->id}] cannot be payed");
        }

        /** @var \EcommerceLayer\Models\Gateway $gateway */
        $gateway = $order->gateway;

        /** @var \EcommerceLayer\Gateways\GatewayProviderInterface $gatewayService */
        $gatewayService = gateway($gateway->identifier);

        /** @var \EcommerceLayer\Gateways\Models\Payment $gatewayPayment */
        $gatewayPayment = $gatewayService->payments()->createAndConfirm(
            $order->total,
            $order->currency->value,
            $order->payment_method,
            [
                'customer_key' => $order->customer->getGatewayKey($gateway->identifier),
                'return_url' => $returnUrl
            ]
        );

        // Manage statuses
        $newOrderStatus = $order->status;
        $newFulfillmentStatus = $order->fulfillment_status;

        switch ($gatewayPayment->status) {
            case PaymentStatus::VOIDED:
            case PaymentStatus::REFUSED:
                // $newOrderStatus = OrderStatus::CANCELED;
                // OrderCanceled::dispatch($order);
                break;
            case PaymentStatus::PAID:
                if (!$order->needFulfillment()) {
                    $newFulfillmentStatus = FulfillmentStatus::FULFILLED;
                    $newOrderStatus = OrderStatus::COMPLETED;
                }
                break;
            default:
                // Do nothing: since the payment is not completed nor manually canceled, order status doesn't change
        }
        // End of status management

        // Update the order with the new statuses
        $order = $this->_createOrUpdate([
            'status' => $newOrderStatus,
            'fulfillment_status' => $newFulfillmentStatus,
            'payment_status' => $gatewayPayment->status,
            'payment_data' => new PaymentData($gatewayPayment->key, $gatewayPayment->getData())
        ], $order);

        // Fire the events
        PaymentUpdated::dispatch($order);

        if ($order->status === OrderStatus::COMPLETED) {
            OrderCompleted::dispatch($order);
        }
        // End fo fire the events

        return $order;
    }

    protected function _createOrUpdate(array $data, Order|null $order = null): Order
    {
        $attributes = [
            'customer_id' => Arr::get($data, 'customer_id'),
            'gateway_id' => Arr::get($data, 'gateway_id'),
            'status' => Arr::get($data, 'status'),
            'fulfillment_status' => Arr::get($data, 'fulfillment_status'),
            'payment_status' => Arr::get($data, 'payment_status'),
            'payment_data' => Arr::get($data, 'payment_data'),
            'currency' => Arr::get($data, 'currency'),
            'metadata' => Arr::get($data, 'metadata'),
        ];

        $builder = $order !== null ? OrderBuilder::init($order) : OrderBuilder::init();

        $builder = $builder->fill($attributes);

        if (Arr::has($data, 'billing_address')) {
            $builder->withBillingAddress(Arr::get($data, 'billing_address'));
        }

        if (Arr::has($data, 'payment_method')) {
            $builder->withPaymentMethod(Arr::get($data, 'payment_method'));
        }

        return $builder->end();
    }
}
