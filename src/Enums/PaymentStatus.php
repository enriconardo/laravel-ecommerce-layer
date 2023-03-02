<?php

namespace EcommerceLayer\Enums;

enum PaymentStatus: string
{
    /**
     * This is the initial status of a payment, it is applied to draft orders.
     */
    case UNPAID = 'unpaid';

    /**
     * A status applied when it is necessary time for gateway and/or 
     * manual actions in order to complete the payment.
     */
    case PENDING = 'pending';

    /**
     * A payment that has been validated by the gateway. 
     * The Authorized status is useful only if you capture payments manually 
     * and it is your cue to capture payments before the authorization period expires. 
     * The Authorized payment status is used with credit card and some other payment methods.
     */
    case AUTHORIZED = 'authorized';

    /**	
     * Payment wasn't captured before the date that was set by the payment gateway 
     * on an order that had the Authorized payment status.
     */
    case EXPIRED = 'expired';

    /**
     * Payment was automatically or manually captured, or the order was marked as paid.
     */
    case PAID = 'paid';

    /**
     * An unpaid payment was manually canceled.
     */
    case VOIDED = 'voided';

    /**
     * The full amount that the customer paid for an order was returned to the customer.
     */
    case REFUNDED = 'refunded';

    /**
     * This status is applied when the gateway refuses the payment for some reason.
     */
    case REFUSED = 'refused';
}
