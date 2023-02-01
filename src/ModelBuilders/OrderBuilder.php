<?php

namespace EnricoNardo\EcommerceLayer\ModelBuilders;

use EnricoNardo\EcommerceLayer\Models\Order;
use EnricoNardo\EcommerceLayer\Models\Gateway;
use EnricoNardo\EcommerceLayer\Models\Customer;

class OrderBuilder extends BaseBuilder
{
    public static function getModelClass(): string
    {
        return Order::class;
    }

    /**
     * @param Customer|string|int $customer
     * @return $this
     */
    public function withCustomer(Customer|string|int $customer)
    {
        /** @var Order $model */
        $model = $this->model;

        if (is_string($customer) || is_int($customer)) {
            $customer = Customer::find($customer);
        }

        if ($customer instanceof Customer) {
            $model->customer()->associate($customer);
        }

        $model->save();

        $this->model = $model;

        return $this;
    }

    /**
     * @param Gateway|string|int $gateway
     * @return $this
     */
    public function withGateway(Gateway|string|int $gateway)
    {
        /** @var Order $model */
        $model = $this->model;

        if (is_string($gateway) || is_int($gateway)) {
            $gateway = Gateway::find($gateway);
        }

        if ($gateway instanceof Gateway) {
            $model->gateway()->associate($gateway);
        }

        $model->save();

        $this->model = $model;

        return $this;
    }
}