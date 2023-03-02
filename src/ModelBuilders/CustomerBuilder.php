<?php

namespace EcommerceLayer\ModelBuilders;

use EcommerceLayer\Models\Customer;

class CustomerBuilder extends BaseBuilder
{
    public static function getModelClass(): string
    {
        return Customer::class;
    }
}