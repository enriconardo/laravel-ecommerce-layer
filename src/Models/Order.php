<?php

namespace EcommerceLayer\Models;

use Illuminate\Database\Eloquent\Model;
use EcommerceLayer\Casts\Address as AddressCast;
use EcommerceLayer\Casts\PaymentMethod as PaymentMethodCast;
use EcommerceLayer\Enums\FulfillmentStatus;
use EcommerceLayer\Enums\OrderStatus;
use EcommerceLayer\Enums\PaymentStatus;
use PrinsFrank\Standards\Currency\ISO4217_Alpha_3 as Currency;

/**
 * @property int $customer_id The id of the related customer.
 * @property int $gateway_id The id of the related payment gateway.
 * @property OrderStatus $status
 * @property PaymentStatus $payment_status
 * @property FulfillmentStatus $fulfillment_status
 * @property Currency $currency
 * @property AddressCast $billing_address
 * @property PaymentMethodCast $payment_method
 * @property array $metadata Set of key-value pairs that you can attach to an object. This can be useful for storing additional information about the object in a structured format.
 * @property string|null $gateway_payment_identifier The id of the payment related object returned by the payment gateway API.
 * @property int $subtotal The sum of the subtotals of all the line items in the order, in cents. No tax, discounts or other options are considered.
 * @property int $total Total amount of the order, which is the subtotal with all the options considered, like taxes, shipping costs or discounts.
 * @property \EcommerceLayer\Models\Customer $customer
 * @property \EcommerceLayer\Models\Gateway $gateway
 * @property \Illuminate\Support\Collection $line_items
 */
class Order extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'gateway_id',
        'status',
        'payment_status',
        'fulfillment_status',
        'currency',
        'billing_address',
        'payment_method',
        'metadata',
        'gateway_payment_identifier'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status' => OrderStatus::class,
        'payment_status' => PaymentStatus::class,
        'fulfillment_status' => FulfillmentStatus::class,
        'currency' => Currency::class,
        'billing_address' => AddressCast::class,
        'payment_method' => PaymentMethodCast::class,
        'metadata' => 'array',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function gateway()
    {
        return $this->belongsTo(Gateway::class);
    }

    public function lineItems()
    {
        return $this->hasMany(LineItem::class);
    }

    /**
     * @see lineItems() Alias
     */
    public function line_items()
    {
        return $this->lineItems();
    }

    /**
     * Get the sum of the subtotals of all the line items in the order, in cents.
     * No tax, discounts or other options are considered.
     * 
     * @return int
     */
    public function getSubtotalAttribute()
    {
        $lineItems = $this->line_items;
        return $lineItems->sum(function ($lineItem) {
            /** @var \EcommerceLayer\Models\LineItem $lineItem */
            return $lineItem->subtotal;
        });
    }

    /**
     * Get the total amount of the order, which is the subtotal with all the options 
     * considered, like taxes, shipping costs or discounts.
     * 
     * @return int
     */
    public function getTotalAttribute()
    {
        // TODO apply taxes, shipping costs, discounts or other stuff.
        return $this->subtotal;
    }

    public function isPaid(): bool
    {
        return $this->payment_status === PaymentStatus::PAID;
    }

    public function canBeUpdated(): bool
    {
        // Only cart (draft order) can be updated
        return $this->status !== OrderStatus::DRAFT ? false : true;
    }

    public function canBePlaced(): bool
    {
        // Only cart (draft order) and non empty order can be placed
        return $this->status !== OrderStatus::DRAFT || $this->line_items->count() === 0 ? false : true;
    }

    public function canBeDeleted(): bool
    {
        // Only cart (draft orders) can be deleted
        return $this->status === OrderStatus::DRAFT ? true : false;
    }

    public function canBePaid(): bool
    {
        $isOpen = $this->status === OrderStatus::OPEN;

        $repeatablePaymentStatus = $this->payment_status === PaymentStatus::UNPAID 
            || $this->payment_status === PaymentStatus::REFUSED 
            || $this->payment_status === PaymentStatus::EXPIRED;

        return ($isOpen && $repeatablePaymentStatus) ? true : false;
    }

    public function needFulfillment(): bool
    {
        if ($this->fulfillment_status === FulfillmentStatus::FULFILLED) {
            return false;
        }

        $needFulfillment = false;
        foreach ($this->line_items as $lineItem) {
            if ($lineItem->product->shippable) {
                $needFulfillment = true;
            }
        }

        return $needFulfillment;
    }
}
