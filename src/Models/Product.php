<?php

namespace EnricoNardo\EcommerceLayer\Models;

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
    use SoftDeletes;

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
        'metadata'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'boolean',
        'shippable' => 'boolean',
        'metadata' => 'array',
    ];

    public function prices()
    {
        return $this->hasMany(Price::class);
    }
}