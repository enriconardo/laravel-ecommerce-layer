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
use EcommerceLayer\Models\Gateway;
use EcommerceLayer\Models\PaymentData;
use EcommerceLayer\Models\PaymentMethod;

class OrderService
{
    public function create(array $data): Order
    {
        // When you create a new order it is a cart (status = DRAFT)
        $data['status'] = Arr::get($data, 'status', OrderStatus::DRAFT);

        $attributes = attributes_filter($data, [
            'status',
            'currency',
            'customer_id',
            'metadata',
            'billing_address',
            'gateway_id',
            'payment_method',
            'payment_status'
        ]);

        $order = $this->_createOrUpdate($attributes);

        EntityCreated::dispatch($order);

        return $order;
    }

    public function update(Order $order, array $data): Order
    {
        if (!$order->canBeUpdated()) {
            throw new InvalidEntityException("Order [{$order->id}] cannot be updated");
        }

        $attributes = attributes_filter($data, [
            'status',
            'currency',
            'customer_id',
            'metadata',
            'billing_address',
            'gateway_id',
            'payment_method',
            'payment_status'
        ]);

        $order = $this->_createOrUpdate($attributes, $order);

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

        // Create Gateway Payment Method instance
        /** @var \EcommerceLayer\Models\Gateway $gateway */
        $gateway = Gateway::find(Arr::get($data, 'gateway_id'));

        /** @var \EcommerceLayer\Gateways\GatewayProviderInterface $gatewayService */
        $gatewayService = gateway($gateway->identifier);

        /** @var \EcommerceLayer\Gateways\Models\GatewayPaymentMethod $gatewayPaymentMethod */
        $gatewayPaymentMethod = $gatewayService->paymentMethods()->create(
            Arr::get($data, 'payment_method.type'),
            Arr::get($data, 'payment_method.data')
        );

        $data['payment_method'] = new PaymentMethod(
            $gatewayPaymentMethod->type,
            in_array($gatewayPaymentMethod->type, config('ecommerce-layer.payment_methods.protected_types', []))
                ? []
                : $gatewayPaymentMethod->data,
            $gatewayPaymentMethod->id ? $gatewayPaymentMethod->id : null
        );
        // End of gateway payment method creation

        $attributes = attributes_filter($data, [
            'status',
            'currency',
            'gateway_id',
            'metadata',
            'billing_address',
            'payment_method',
            'return_url'
        ]);

        $order = $this->_createOrUpdate($attributes, $order);

        OrderPlaced::dispatch($order);

        $order = $this->pay($order, [
            'return_url' => Arr::get($data, 'return_url')
        ]);

        return $order;
    }

    public function pay(Order $order, $args = []): Order
    {
        /**
         * Possible $args values:
         * - return_url     The URL to redirect your customer back to after they authenticate or cancel their payment on the payment method’s app or site.
         * - off_session    Set to true to indicate that the customer is not in your checkout flow during this payment attempt
         */

        if (!$order->canBePaid()) {
            throw new InvalidEntityException("Order [{$order->id}] cannot be payed");
        }

        /** @var \EcommerceLayer\Models\Gateway $gateway */
        $gateway = $order->gateway;

        /** @var \EcommerceLayer\Gateways\GatewayProviderInterface $gatewayService */
        $gatewayService = gateway($gateway->identifier);

        /** @var \EcommerceLayer\Gateways\Models\GatewayPaymentMethod $gatewayPaymentMethod */
        $gatewayPaymentMethod = $order->payment_method->gateway_id
            ? $gatewayService->paymentMethods()->find($order->payment_method->gateway_id)
            : $gatewayService->paymentMethods()->create(
                $order->payment_method->type,
                $order->payment_method->data
            );

        /** @var \EcommerceLayer\Gateways\Models\GatewayPayment $gatewayPayment */
        $gatewayPayment = $gatewayService->payments()->createAndConfirm(
            $order->total,
            $order->currency->value,
            $gatewayPaymentMethod,
            [
                'customer_id' => $order->customer->getGatewayId($gateway->identifier),
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
            'payment_data' => new PaymentData($gatewayPayment->id, $gatewayPayment->data())
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
        $builder = $order !== null ? OrderBuilder::init($order) : OrderBuilder::init();

        if (Arr::has($data, 'billing_address')) {
            $builder->withBillingAddress(Arr::get($data, 'billing_address'));
            unset($data['billing_address']);
        }

        if (Arr::has($data, 'payment_method')) {
            $builder->withPaymentMethod(Arr::get($data, 'payment_method'));
            unset($data['payment_method']);
        }

        $builder = $builder->fill($data);

        return $builder->end();
    }
}
