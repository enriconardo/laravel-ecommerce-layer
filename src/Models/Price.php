<?php

namespace EcommerceLayer\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use EcommerceLayer\Casts\Plan as PlanCast;
use PrinsFrank\Standards\Currency\ISO4217_Alpha_3 as Currency;

/**
 * @property int $product_id The id of the product that the current price is associated.
 * @property Product $product The product that the current price is associated. 
 * @property Currency $currency
 * @property int $unit_amount The price of a single unit of product, represented as an integer.
 * @property string $description A brief description of the price, hidden from customers.
 * @property bool $active Whether the price can be used for new purchases. Default `true`.
 * @property bool $recurring Whether the price is for a subscription plan. Default `false`.
 * @property bool $default Whether the price is displayed by default to the user. Default to `false`.
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
        'product_id',
        'currency',
        'unit_amount',
        'description',
        'active',
        'recurring',
        'default',
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
        'default' => 'boolean',
        'plan' => PlanCast::class,
        'start_at' => 'datetime',
        'end_at' => 'datetime'
    ];

    /**
     * Scope a query to only include default prices.
     */
    public function scopeDefault(Builder $query): void
    {
        $query->where('default', true);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function lineItem()
    {
        return $this->hasMany(LineItem::class);
    }

    public function canBeUpdated(): bool
    {
        $subscriptionsCount = $this->subscriptions()->where('active', true)->count();

        return $subscriptionsCount > 0 ? false : true;
    }

    public function canBeDeleted(): bool
    {
        $subscriptionsCount = $this->subscriptions()->where('active', true)->count();

        return $subscriptionsCount > 0 ? false : true;
    }
}
