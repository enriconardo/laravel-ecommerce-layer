<?php

namespace EcommerceLayer\ModelBuilders;

use EcommerceLayer\Models\Address;
use EcommerceLayer\Models\Order;
use EcommerceLayer\DomainModels\PaymentMethod;
use Exception;
use Illuminate\Support\Arr;

class OrderBuilder extends BaseBuilder
{
    public static function getModelClass(): string
    {
        return Order::class;
    }

    /**
     * @param Address|array $address
     * @return $this
     * @throws Exception
     */
    public function withBillingAddress(Address|array $address)
    {
        try {
            if (is_array($address)) {
                $address = new Address($address);
            }

            if (!$address instanceof Address) {
                throw new Exception('Invalid address');
            }

            $this->model->billing_address = $address;
        } catch (Exception $e) {
            $this->abort();
            throw $e;
        }

        return $this;
    }

    /**
     * @param PaymentMethod|array $method
     * @return $this
     * @throws Exception
     */
    public function withPaymentMethod(PaymentMethod|array $method)
    {
        try {
            if (is_array($method)) {
                $method = new PaymentMethod(
                    Arr::get($method, 'type'), 
                    Arr::get($method, 'data'),
                    Arr::get($method, 'gateway_id')
                );
            }

            if (!$method instanceof PaymentMethod) {
                throw new Exception('Invalid payment method');
            }

            $this->model->payment_method = $method;
        } catch (Exception $e) {
            $this->abort();
            throw $e;
        }

        return $this;
    }
}
