<?php

namespace EnricoNardo\EcommerceLayer\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $code An internal identifier of the product. Could be the SKU for example.
 * @property string $name The name of the product.
 * @property bool $active Whether the product is currently available for purchase. Default `true`.
 * @property bool $shippable Whether this product is shipped (i.e., physical goods). Default `false`.
 */
class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'name',
        'active',
        'shippable',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'boolean',
        'shippable' => 'boolean',
    ];

    public function lineItems()
    {
        return $this->belongsToMany(LineItem::class);
    }

    public function prices()
    {
        return $this->hasMany(Price::class);
    }
}