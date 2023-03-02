<?php

namespace EcommerceLayer\Models;

use EcommerceLayer\Enums\SubscriptionStatus;
use Illuminate\Database\Eloquent\Model;

/**
 * @property SubscriptionStatus $status
 * @property mixed|null started_at The datetime the subscription started (became active for the first time).
 * @property mixed|null renewed_at The datetime of the last successful renewal. The first occurrence of the subscription matches the started_at datetime. 
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
        'renewed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status' => SubscriptionStatus::class,
        'started_at' => 'datetime',
        'renewed_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function price()
    {
        return $this->belongsTo(Price::class);
    }
}
