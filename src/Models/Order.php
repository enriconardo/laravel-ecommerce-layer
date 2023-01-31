<?php

namespace EnricoNardo\EcommerceLayer\Models;

use Illuminate\Database\Eloquent\Model;
use EnricoNardo\EcommerceLayer\Casts\Address as AddressCast;
use EnricoNardo\EcommerceLayer\Casts\PaymentMethod as PaymentMethodCast;
use EnricoNardo\EcommerceLayer\Enums\OrderStatus;
use EnricoNardo\EcommerceLayer\Enums\PaymentStatus;

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
        'gateway_payment_identifier' // An identifier of the gateway payment object
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status' => OrderStatus::class,
        'payment_status' => PaymentStatus::class,
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