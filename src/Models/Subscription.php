<?php

namespace EcommerceLayer\Models;

use EcommerceLayer\Enums\SubscriptionStatus;
use Illuminate\Database\Eloquent\Model;
use EcommerceLayer\Models\Customer;

/**
 * @property SubscriptionStatus $status
 * @property mixed|null $started_at The datetime the subscription started (became active for the first time).
 * @property mixed|null $expires_at The due date of the subscription. If null is endless.
 * @property Customer $customer The customer who own the subscription.
 * @property Price $price The entity for which the subscription is created.
 * @property $source_order The order that has originated the subscription the first time.
 */
class Subscription extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status',
        'started_at',
        'expires_at',
        'customer_id',
        'price_id',
        'source_order_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status' => SubscriptionStatus::class,
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function price()
    {
        return $this->belongsTo(Price::class);
    }

    public function sourceOrder()
    {
        return $this->belongsTo(Order::class, 'source_order_id');
    }

    /**
     * @see sourceOrder() Alias
     */
    public function source_order()
    {
        return $this->sourceOrder();
    }

    public function canBeDeleted()
    {
        return $this->status === SubscriptionStatus::ACTIVE || $this->status === SubscriptionStatus::PENDING 
            ? false 
            : true;
    }
}
