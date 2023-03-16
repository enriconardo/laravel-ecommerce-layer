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
use EcommerceLayer\Events\Order\OrderCanceled;
use EcommerceLayer\Events\Order\OrderCompleted;

class OrderService
{
    public function create(array $data): Order
    {
        // When you create a new order it is a cart (status = DRAFT)
        $data['status'] = OrderStatus::DRAFT;

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

        $order = $this->pay($order);

        return $order;
    }

    public function pay(Order $order): Order
    {
        if (!$order->canBePaid()) {
            throw new InvalidEntityException("Order [{$order->id}] cannot be payed");
        }

        /** @var \EcommerceLayer\Models\Gateway $gateway */
        $gateway = $order->gateway;

        /** @var \EcommerceLayer\Gateways\GatewayProviderInterface $gatewayService */
        $gatewayService = gateway($gateway->identifier);

        /** @var \EcommerceLayer\Gateways\Models\Payment $payment */
        $payment = $gatewayService->payments()->createAndConfirm(
            $order->total,
            $order->currency->value,
            $order->payment_method,
            $order->customer->getGatewayCustomerIdentifier($gateway->identifier)
        );

        // Manage statuses
        $newOrderStatus = $order->status;
        $newFulfillmentStatus = $order->fulfillment_status;

        switch ($payment->status) {
            case PaymentStatus::VOIDED:
                $newOrderStatus = OrderStatus::CANCELED;
                OrderCanceled::dispatch($order);
                break;
            case PaymentStatus::PAID:
                if (!$order->needFulfillment()) {
                    $newFulfillmentStatus = FulfillmentStatus::FULFILLED;
                    $newOrderStatus = OrderStatus::COMPLETED;
                    OrderCompleted::dispatch($order);
                }
                break;
            default:
                // Do nothing: since the payment is not completed nor manually canceled, order status doesn't change
        }
        // End of status management

        $order = $this->_createOrUpdate([
            'status' => $newOrderStatus,
            'fulfillment_status' => $newFulfillmentStatus,
            'payment_status' => $payment->status,
            'gateway_payment_identifier' => $payment->identifier
        ], $order);

        return $order;
    }

    protected function _createOrUpdate(array $data, Order|null $order = null): Order
    {
        $attributes = [
            'customer_id' => Arr::get($data, 'customer_id'),
            'gateway_id' => Arr::get($data, 'gateway_id'),
            'status' => Arr::get($data, 'status'),
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
