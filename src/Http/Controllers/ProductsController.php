<?php

namespace EnricoNardo\EcommerceLayer\Http\Controllers;

use EnricoNardo\EcommerceLayer\Enums\Currency;
use EnricoNardo\EcommerceLayer\Enums\PlanInterval;
use EnricoNardo\EcommerceLayer\Http\Resources\ProductResource;
use EnricoNardo\EcommerceLayer\ModelBuilders\ProductBuilder;
use EnricoNardo\EcommerceLayer\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum as EnumValidation;
use Spatie\QueryBuilder\QueryBuilder;

class ProductsController extends Controller
{
    public function list(Request $request)
    {
        $products = QueryBuilder::for(Product::class)
            ->allowedFilters(['code', 'active', 'shippable'])
            ->allowedSorts('code', 'name')
            ->paginate()
            ->appends($request->query());

        return ProductResource::collection($products);
    }

    public function find($id)
    {
        /** @var Product $product */
        $product = Product::findOrFail($id);

        return ProductResource::make($product);
    }

    public function create(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:EnricoNardo\EcommerceLayer\Models\Product,code',
            'name' => 'required|string',
            'active' => 'boolean',
            'shippable' => 'boolean',
            'metadata' => 'array',
            'prices' => 'array|required',
            'prices.*.currency' => ['string', 'required_with:prices', new EnumValidation(Currency::class)],
            'prices.*.description' => 'string|required_with:prices',
            'prices.*.active' => 'boolean',
            'prices.*.recurring' => 'boolean',
            'prices.*.plan' => 'array:interval,interval_count|required_if:prices.*.recurring,true',
            'prices.*.plan.interval' => ['required_with:prices.*.plan', new EnumValidation(PlanInterval::class)],
            'prices.*.plan.interval_count' => 'required_with:prices.*.plan|integer'
        ]);

        $data = [
            'code' => $request->input('code'),
            'name' => $request->input('name'),
            'active' => $request->input('active'),
            'shippable' => $request->input('shippable'),
            'metadata' => $request->input('metadata'),
        ];

        $product = ProductBuilder::init()
            ->fill($data)
            ->withPrices($request->input('prices', []))
            ->end();

        return ProductResource::make($product);
    }

    public function update($id, Request $request)
    {
        /** @var Product $product */
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'string',
            'active' => 'boolean',
            'shippable' => 'boolean',
            'metadata' => 'array',
            'prices' => 'array',
            'prices.*.currency' => ['string', new EnumValidation(Currency::class)],
            'prices.*.description' => 'string',
            'prices.*.active' => 'boolean',
            'prices.*.recurring' => 'boolean',
            'prices.*.plan' => 'array:interval,interval_count|required_if:prices.*.recurring,true',
            'prices.*.plan.interval' => ['required_with:prices.*.plan', new EnumValidation(PlanInterval::class)],
            'prices.*.plan.interval_count' => 'required_with:prices.*.plan|integer'
        ]);

        $data = [
            'name' => $request->input('name'),
            'active' => $request->input('active'),
            'shippable' => $request->input('shippable'),
            'metadata' => $request->input('metadata'),
        ];

        $product = ProductBuilder::init($product)
            ->fill($data)
            ->withPrices($request->input('prices', []))
            ->end();

        return ProductResource::make($product);
    }

    public function delete($id)
    {
        /** @var Product $product */
        $product = Product::findOrFail($id);

        $product->delete();

        return response()->json([], 204);
    }
}
