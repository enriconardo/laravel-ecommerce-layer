<?php

namespace EnricoNardo\EcommerceLayer\Models;

use Illuminate\Database\Eloquent\Model;

class Gateway extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', // Stripe, Paypal...
        'identifier' // stripe, paypal, some-slugified-name...
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}