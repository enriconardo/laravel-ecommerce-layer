<?php

namespace EnricoNardo\EcommerceLayer\Services;

use EnricoNardo\EcommerceLayer\Enums\OrderStatus;
use EnricoNardo\EcommerceLayer\Events\Order\OrderPlaced;
use EnricoNardo\EcommerceLayer\Events\Order\OrderPlacing;
use EnricoNardo\EcommerceLayer\Exceptions\InvalidOrderException;
use EnricoNardo\EcommerceLayer\ModelBuilders\OrderBuilder;
use EnricoNardo\EcommerceLayer\Models\Order;
use Illuminate\Support\Arr;

class OrderService
{
    public function create(array $data): Order
    {
        $order = $this->_createOrUpdate($data, OrderStatus::DRAFT);

        return $order;
    }

    public function update(Order $order, array $data): Order
    {
        if (!$order->canBeUpdated()) {
            throw new InvalidOrderException("Order [{$order->id}] cannot be updated");
        }

        $order = $this->_createOrUpdate($data, null, $order);

        return $order;
    }

    public function delete(Order $order)
    {
        if (!$order->canBeDeleted()) {
            throw new InvalidOrderException("Order [{$order->id}] cannot be deleted");
        }

        $order->delete();
    }

    public function place(Order $order, array $data = [])
    {
        if (!$order->canBePlaced()) {
            throw new InvalidOrderException("Order [{$order->id}] cannot be placed");
        }

        $order = $this->_createOrUpdate($data, OrderStatus::OPEN, $order);

        OrderPlacing::dispatch($order);

        $order = $this->pay($order);

        OrderPlaced::dispatch($order);

        return $order;
    }

    public function pay(Order $order): Order
    {
        if (!$order->canBePaid()) {
            throw new InvalidOrderException("Order [{$order->id}] cannot be payed");
        }

        /** @var \EnricoNardo\EcommerceLayer\Models\Gateway $gateway */
        $gateway = $order->gateway;

        /** @var \EnricoNardo\EcommerceLayer\Gateways\GatewayServiceInterface $gatewayService */
        $gatewayService = gateway($gateway->identifier);

        /** @var \EnricoNardo\EcommerceLayer\Gateways\Models\Payment $payment */
        $payment = $gatewayService->payments()->createAndConfirm(
            $order->total,
            $order->currency->value,
            $order->payment_method,
            $order->customer->getGatewayCustomerIdentifier($gateway->identifier)
        );

        $order = OrderBuilder::init($order)->fill([
            'payment_status' => $payment->status,
            'gateway_payment_identifier' => $payment->identifier
        ])->end();

        return $order;
    }

    protected function _createOrUpdate(array $data, $status = null, Order|null $order = null): Order
    {
        $attributes = [
            'customer_id' => Arr::get($data, 'customer_id'),
            'gateway_id' => Arr::get($data, 'gateway_id'),
            'status' => $status,
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
