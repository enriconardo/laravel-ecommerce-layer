<?php

namespace EcommerceLayer\Models;

use EcommerceLayer\Enums\SubscriptionStatus;
use Illuminate\Database\Eloquent\Model;

/**
 * @property SubscriptionStatus $status
 * @property mixed|null started_at The datetime the subscription started (became active for the first time).
 * @property mixed|null expires_at The due date of the subscription. If null is endless.
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

    public function canBeDeleted()
    {
        return $this->status === SubscriptionStatus::ACTIVE || $this->status === SubscriptionStatus::PENDING 
            ? false 
            : true;
    }
}
