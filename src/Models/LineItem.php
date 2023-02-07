<?php

namespace EnricoNardo\EcommerceLayer\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $quantity The quantity of a single product added to the order.
 * @property \EnricoNardo\EcommerceLayer\Models\Order $order The parent order of the line item.
 * @property \EnricoNardo\EcommerceLayer\Models\Price $price This is the price of the product added to the order.
 * @property \EnricoNardo\EcommerceLayer\Models\Product $product
 * @property int $subtotal Total price calculated as quantity x unit price of the product, in cents. No tax, discounts or other options are considered.
 */
class LineItem extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'quantity'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function price()
    {
        return $this->hasOne(Price::class);
    }

    /**
     * @return \EnricoNardo\EcommerceLayer\Models\Product
     */
    public function getProductAttribute()
    {
        return $this->price->product;
    }

    /**
     * Get the total price calculated as quantity x unit price of the product, in cents. 
     * No tax, discounts or other options are considered.
     * 
     * @return int
     */
    public function getSubtotalAttribute()
    {
        return $this->price->unit_amount * $this->quantity;
    }
}