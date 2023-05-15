<?php

namespace EcommerceLayer\ModelBuilders;

use EcommerceLayer\Models\Address;
use EcommerceLayer\Models\Order;
use Exception;
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
}
