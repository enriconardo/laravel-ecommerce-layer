<?php

namespace EcommerceLayer\ModelBuilders;

use EcommerceLayer\Models\Gateway;

class GatewayBuilder extends BaseBuilder
{
    public static function getModelClass(): string
    {
        return Gateway::class;
    }
}