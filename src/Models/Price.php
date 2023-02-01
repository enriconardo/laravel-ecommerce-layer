<?php

namespace EnricoNardo\EcommerceLayer\Models;

use Illuminate\Database\Eloquent\Model;
use EnricoNardo\EcommerceLayer\Casts\Plan as PlanCast;
use EnricoNardo\EcommerceLayer\Enums\Currency;

/**
 * @property Currency $currency
 * @property string $description A brief description of the price, hidden from customers.
 * @property bool $active Whether the price can be used for new purchases. Default `true`.
 * @property bool $recurring Whether the price is for a subscription plan. Default `false`.
 * @property bool $discount Whether the price is a discounted price. Default `false`.
 * @property PlanCast|null $plan The recurring components of a price. Set only if $recurring is `true`.
 * @property mixed|null $start_at If the price is time limited, this is the starting datetime.
 * @property mixed|null $end_at If the price is time limited, this is the ending datetime. Leave `null` if it is endless.
 */
class Price extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'currency',
        'description',
        'active',
        'recurring',
        'discount',
        'plan',
        'start_at',
        'end_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'currency' => Currency::class,
        'active' => 'boolean',
        'recurring' => 'boolean',
        'discount' => 'boolean',
        'plan' => PlanCast::class,
        'start_at' => 'datetime',
        'end_at' => 'datetime'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
