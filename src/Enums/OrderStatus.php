<?php

namespace EcommerceLayer\Enums;

enum OrderStatus: string
{
    /**
     * Draft order is a cart. It hasn't been placed yet.
     */
    case DRAFT = 'draft';

    /**
     * An open order has been placed but needs further actions, like payment or fulfillment.
     */
    case OPEN = 'open';

    /**
     * This is a final state. A completed order has been payed and fulfilled succesfully.
     * Even a refunded order can be in this state (see the payment statuses).
     */
    case COMPLETED = 'completed';

    /**
     * This is a final state. The order is failed for some reason.
     * Even a refunded order can be in this state (see the payment statuses).
     */
    case CANCELED = 'canceled';
}
