<?php

namespace EcommerceLayer\Enums;

enum SubscriptionStatus: string
{
    /**
     * This is a created subscription which requires further actions to became active.
     */
    case PENDING = 'pending';

    /**
     * An active subscription.
     */
    case ACTIVE = 'active';

    /**
     * A subscription that has been canceled. This is a final state.
     */
    case CANCELED = 'canceled';

    /**
     * After a renewal payment fails or miss, the subscriptions became unpaid.
     */
    case UNPAID = 'unpaid';
}
