<?php

namespace EnricoNardo\EcommerceLayer\ModelBuilders;

use EnricoNardo\EcommerceLayer\Models\LineItem;
use EnricoNardo\EcommerceLayer\Models\Order;
use EnricoNardo\EcommerceLayer\Models\Price;
use Exception;

class LineItemBuilder extends BaseBuilder
{
    public static function getModelClass(): string
    {
        return LineItem::class;
    }
}
