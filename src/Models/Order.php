<?php

namespace EnricoNardo\EcommerceLayer\Models;

use Illuminate\Database\Eloquent\Model;
use EnricoNardo\EcommerceLayer\Casts\Address as AddressCast;
use EnricoNardo\EcommerceLayer\Casts\PaymentMethod as PaymentMethodCast;
use EnricoNardo\EcommerceLayer\Enums\OrderStatus;
use EnricoNardo\EcommerceLayer\Enums\PaymentStatus;
use EnricoNardo\EcommerceLayer\Enums\Currency;

/**
 * @property OrderStatus $status
 * @property PaymentStatus $payment_status
 * @property Currency $currency
 * @property AddressCast $billing_address
 * @property PaymentMethodCast $payment_method
 * @property array $metadata Set of key-value pairs that you can attach to an object. This can be useful for storing additional information about the object in a structured format.
 * @property string|null $gateway_payment_identifier The id of the payment related object returned by the payment gateway API.
 */
class Order extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status',
        'payment_status',
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

    public function getTotalAttribute()
    {
        // TODO
        return 100;
    }
}