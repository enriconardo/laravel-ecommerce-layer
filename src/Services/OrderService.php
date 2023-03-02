<?php

namespace EnricoNardo\EcommerceLayer\Services;

use EnricoNardo\EcommerceLayer\Enums\FulfillmentStatus;
use EnricoNardo\EcommerceLayer\Enums\OrderStatus;
use EnricoNardo\EcommerceLayer\Enums\PaymentStatus;
use EnricoNardo\EcommerceLayer\Events\Order\OrderPlaced;
use EnricoNardo\EcommerceLayer\Events\Order\OrderPlacing;
use EnricoNardo\EcommerceLayer\Exceptions\InvalidEntityException;
use EnricoNardo\EcommerceLayer\ModelBuilders\OrderBuilder;
use EnricoNardo\EcommerceLayer\Models\Order;
use Illuminate\Support\Arr;

class OrderService
{
    public function create(array $data): Order
    {
        $data['status'] = OrderStatus::DRAFT;
        $order = $this->_createOrUpdate($data);

        return $order;
    }

    public function update(Order $order, array $data): Order
    {
        if (!$order->canBeUpdated()) {
            throw new InvalidEntityException("Order [{$order->id}] cannot be updated");
        }

        $order = $this->_createOrUpdate($data, $order);

        return $order;
    }

    public function delete(Order $order)
    {
        if (!$order->canBeDeleted()) {
            throw new InvalidEntityException("Order [{$order->id}] cannot be deleted");
        }

        $order->delete();
    }

    public function place(Order $order, array $data = [])
    {
        if (!$order->canBePlaced()) {
            throw new InvalidEntityException("Order [{$order->id}] cannot be placed");
        }

        $data['status'] = OrderStatus::OPEN;
        $order = $this->_createOrUpdate($data, $order);

        OrderPlacing::dispatch($order);

        $order = $this->pay($order);

        OrderPlaced::dispatch($order);

        return $order;
    }

    public function pay(Order $order): Order
    {
        if (!$order->canBePaid()) {
            throw new InvalidEntityException("Order [{$order->id}] cannot be payed");
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

        // Manage statuses
        $newOrderStatus = $order->status;
        $newFulfillmentStatus = $order->fulfillment_status;

        switch ($payment->status) {
            case PaymentStatus::VOIDED:
                $newOrderStatus = OrderStatus::CANCELED;
                break;
            case PaymentStatus::PAID:
                if (!$order->needFulfillment()) {
                    $newOrderStatus = OrderStatus::COMPLETED;
                }

                if ($newOrderStatus === OrderStatus::COMPLETED) {
                    $newFulfillmentStatus = FulfillmentStatus::FULFILLED;
                }
                break;
            default:
                // Do nothing
        }
        // End of status management

        $order = $this->_createOrUpdate([
            'status' => $newOrderStatus,
            'fulfillment_status' => $newFulfillmentStatus,
            'payment_status' => $payment->status,
            'gateway_payment_identifier' => $payment->identifier
        ], OrderStatus::OPEN, $order);

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
