<?php

namespace EcommerceLayer\Models;

use EcommerceLayer\Traits\HasMetadata;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * @property string $email
 * @property array $metadata Set of key-value pairs that you can attach to an object. This can be useful for storing additional information about the object in a structured format.
 * @property array $gateway_ids Store the ID of the related customer object for each payment gateway.
 */
class Customer extends Model
{
    use HasMetadata;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'gateway_ids'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'gateway_ids' => 'array'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function getGatewayId($gatewayIdentifier) 
    {
        return Arr::get($this->gateway_ids, $gatewayIdentifier, null);
    }
}
