<?php

namespace EnricoNardo\EcommerceLayer\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $email
 * @property array $metadata Set of key-value pairs that you can attach to an object. This can be useful for storing additional information about the object in a structured format.
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
        'metadata'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'metadata' => 'array',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
