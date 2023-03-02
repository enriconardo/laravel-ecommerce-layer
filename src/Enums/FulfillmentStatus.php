<?php

namespace EnricoNardo\EcommerceLayer\Enums;

enum FulfillmentStatus: string
{
    case UNFULFILLED = 'unfulfilled';

    case IN_PROGRESS = 'in_progress';

    case FULFILLED = 'fulfilled';
}
