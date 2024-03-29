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
use EcommerceLayer\Events\Payment\PaymentUpdated;
use EcommerceLayer\Models\Gateway;
use EcommerceLayer\Models\PaymentData;
use EcommerceLayer\Models\PaymentMethod;
use Exception;

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

    public function place(Order $order, $otherPaymentData = []): Order
    {
        if (!$order->canBePlaced()) {
            throw new InvalidEntityException("Order [{$order->id}] cannot be placed");
        }

        // Update the order's data
        $order = $this->_createOrUpdate([
            'status' => OrderStatus::OPEN, // When you place an order it is transformed to an OPEN order from a cart (DRAFT order)
        ], $order);

        // Fire the event
        OrderPlaced::dispatch($order);

        return $this->pay($order, $otherPaymentData);
    }

    public function pay(Order $order, $args = []): Order
    {
        /**
         * Possible $args values:
         * - return_url     The URL to redirect your customer back to after they authenticate or cancel their payment on the payment method’s app or site.
         * - off_session    Set to true to indicate that the customer is not in your checkout flow during this payment attempt
         * - success_url
         * - cancel_url
         * ...
         */

        if (!array_key_exists('off_session', $args)) {
            $args['off_session'] = $order;
        }

        if (!$order->canBePaid()) {
            throw new InvalidEntityException("Order [{$order->id}] cannot be payed");
        }

        /** @var \EcommerceLayer\Models\Gateway $gateway */
        $gateway = $order->gateway;

        /** @var \EcommerceLayer\Gateways\GatewayProviderInterface $gatewayProviderService */
        $gatewayProviderService = gateway($gateway->identifier);

        /** @var \EcommerceLayer\Gateways\Models\GatewayPaymentMethod $gatewayPaymentMethod */
        $gatewayPaymentMethod = $order->payment_method->gateway_id
            ? $gatewayProviderService->paymentMethods()->find($order->payment_method->gateway_id)
            : $gatewayProviderService->paymentMethods()->create(
                $order->payment_method->type,
                $order->payment_method->data
            );

        $customerGatewayId = $order->customer->getGatewayId($gateway->identifier);
        /** @var \EcommerceLayer\Gateways\Models\GatewayCustomer $gatewayCustomer */
        $gatewayCustomer = $customerGatewayId ? $gatewayProviderService->customers()->find($customerGatewayId): null;

        /** @var \EcommerceLayer\Gateways\Models\GatewayPayment $gatewayPayment */
        $gatewayPayment = $gatewayProviderService->payments()->create(
            $order->total,
            $order->currency->value,
            $gatewayPaymentMethod,
            $gatewayCustomer,
            [
                'order_id' => $order->id,
                ...$args
            ]
        );

        return $this->updatePayment($order, $gatewayPayment);
    }

    public function updatePayment(Order $order, \EcommerceLayer\Gateways\Models\GatewayPayment $gatewayPayment): Order
    {
        // Manage statuses
        $newOrderStatus = $order->status;
        $newFulfillmentStatus = $order->fulfillment_status;

        switch ($gatewayPayment->status) {
            case PaymentStatus::VOIDED:
            case PaymentStatus::REFUSED:
                $newOrderStatus = OrderStatus::CANCELED;
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
            'payment_data' => new PaymentData($gatewayPayment->id, $gatewayPayment->data())
        ], $order);

        // Fire the events
        PaymentUpdated::dispatch($order);

        switch ($order->status) {
            case OrderStatus::CANCELED:
                OrderCanceled::dispatch($order);
                break;
            case OrderStatus::COMPLETED:
                OrderCompleted::dispatch($order);
                break;
            default:
                // Do Nothing
        }
        // End fo fire the events

        return $order;
    }

    protected function _createOrUpdate(array $data, Order|null $order = null): Order
    {
        $attributes = attributes_filter($data, [
            'currency',
            'metadata',
            'billing_address',
            'customer_id',
            'gateway_id',
            'status',
            'payment_method',
            'fulfillment_status',
            'payment_status',
            'payment_data',
            'off_session'
        ]);

        $builder = $order !== null ? OrderBuilder::init($order) : OrderBuilder::init();

        if (Arr::has($attributes, 'billing_address')) {
            $builder->withBillingAddress(Arr::get($attributes, 'billing_address'));
            unset($attributes['billing_address']);
        }

        $builder = $builder->fill($attributes);

        return $builder->end();
    }
}
