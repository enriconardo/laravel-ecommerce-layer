<?php

namespace EnricoNardo\EcommerceLayer\ModelBuilders;

use EnricoNardo\EcommerceLayer\Models\Customer;

class CustomerBuilder extends BaseBuilder
{
    public static function getModelClass(): string
    {
        return Customer::class;
    }
}