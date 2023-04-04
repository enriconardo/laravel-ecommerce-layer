<?php

namespace EcommerceLayer\ModelBuilders;

use EcommerceLayer\Models\Subscription;

class SubscriptionBuilder extends BaseBuilder
{
    public static function getModelClass(): string
    {
        return Subscription::class;
    }
}