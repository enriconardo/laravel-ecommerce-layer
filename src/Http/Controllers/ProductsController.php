<?php

namespace EnricoNardo\EcommerceLayer\Http\Controllers;

use EnricoNardo\EcommerceLayer\Enums\PlanInterval;
use EnricoNardo\EcommerceLayer\Http\Resources\ProductResource;
use EnricoNardo\EcommerceLayer\ModelBuilders\PriceBuilder;
use EnricoNardo\EcommerceLayer\ModelBuilders\ProductBuilder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum as EnumValidation;

class ProductsController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:EnricoNardo\EcommerceLayer\Models\Product,code',
            'name' => 'required|string',
            'active' => 'boolean',
            'shippable' => 'boolean',
            'price' => 'array:currency,description,active,recurring,plan|required',
            'price.currency' => 'string|required_with:price',
            'price.description' => 'string|required_with:price',
            'price.active' => 'boolean',
            'price.recurring' => 'boolean',
            'price.plan' => 'array:interval,interval_count|required_if:price.recurring,true',
            'price.plan.interval' => ['required_with:price.plan', new EnumValidation(PlanInterval::class)],
            'price.plan.interval_count' => 'required_with:price.plan|integer'
        ]);

        $data = [
            'code' => $request->input('code'),
            'name' => $request->input('name'),
            'active' => $request->input('active'),
            'shippable' => $request->input('shippable'),
        ];

        $price = PriceBuilder::init(null, false)->fill($request->input('price'))->getModel();

        $product = ProductBuilder::init()->fill($data)->withPrice($price)->end();

        return ProductResource::make($product);
    }
}
