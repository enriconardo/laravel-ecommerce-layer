<?php

namespace EcommerceLayer\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * @property string $email
 * @property array $metadata Set of key-value pairs that you can attach to an object. This can be useful for storing additional information about the object in a structured format.
 * @property array $gateway_customer_identifiers Store the ID of the related customer object for each payment gateway.
 */
class Customer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'metadata',
        'gateway_customer_identifiers'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'metadata' => 'array',
        'gateway_customer_identifiers' => 'array'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function getGatewayCustomerIdentifier($identier) 
    {
        return Arr::get($this->gateway_customer_identifiers, $identier, null);
    }
}
