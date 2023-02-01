<?php

namespace EnricoNardo\EcommerceLayer\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $name
 * @property string $identifier A human readable and unique identifier for the gateway (e.g: stripe, paypal, some-slugified-name...)
 */
class Gateway extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'identifier'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}