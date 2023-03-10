<?php

namespace EcommerceLayer\Models;

use EcommerceLayer\Traits\HasMetadata;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $code An internal identifier of the product. Could be the SKU for example.
 * @property string $name The name of the product.
 * @property bool $active Whether the product is currently available for purchase. Default `true`.
 * @property bool $shippable Whether this product is shipped (i.e., physical goods). Default `false`.
 * @property array $metadata Set of key-value pairs that you can attach to an object. This can be useful for storing additional information about the object in a structured format.
 */
class Product extends Model
{
    use SoftDeletes, HasMetadata;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'name',
        'active',
        'shippable'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'boolean',
        'shippable' => 'boolean'
    ];

    public function prices()
    {
        return $this->hasMany(Price::class);
    }

    public function canBeDeleted(): bool
    {
        $canBeDeleted = true;
        $prices = $this->prices;

        foreach ($prices as $price) {
            /** @var \EcommerceLayer\Models\Price $price */
            if (!$price->canBeDeleted()) {
                $canBeDeleted = false;
            }
        }

        return $canBeDeleted;
    }
}