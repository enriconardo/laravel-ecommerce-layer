<?php

namespace EcommerceLayer\ModelBuilders;

use EcommerceLayer\Models\LineItem;
use EcommerceLayer\Models\Order;
use EcommerceLayer\Models\Price;
use Exception;

class LineItemBuilder extends BaseBuilder
{
    public static function getModelClass(): string
    {
        return LineItem::class;
    }
}
