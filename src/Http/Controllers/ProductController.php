<?php

namespace EnricoNardo\EcommerceLayer\Http\Controllers;

use PrinsFrank\Standards\Currency\ISO4217_Alpha_3 as Currency;
use PrinsFrank\Standards\Http\HttpStatusCode;
use EnricoNardo\EcommerceLayer\Enums\PlanInterval;
use EnricoNardo\EcommerceLayer\Http\Resources\ProductResource;
use EnricoNardo\EcommerceLayer\ModelBuilders\ProductBuilder;
use EnricoNardo\EcommerceLayer\Models\Product;
use EnricoNardo\EcommerceLayer\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum as EnumValidation;
use Spatie\QueryBuilder\QueryBuilder;

class ProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function list(Request $request)
    {
        $products = QueryBuilder::for(Product::class)
            ->allowedFilters(['code', 'active', 'shippable'])
            ->allowedSorts('code', 'name')
            ->allowedIncludes('prices')
            ->paginate()
            ->appends($request->query());

        return ProductResource::collection($products);
    }

    public function find($id)
    {
        /** @var Product $product */
        $product = Product::findOrFail($id);

        // Load the necessary relationships to return
        $product->load('prices');

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
            'prices' => 'array',
            'prices.*.currency' => ['string', 'required_with:prices', new EnumValidation(Currency::class)],
            'prices.*.unit_amount' => 'integer|required_with:prices',
            'prices.*.description' => 'string|required_with:prices',
            'prices.*.active' => 'boolean',
            'prices.*.default' => 'boolean',
            'prices.*.recurring' => 'boolean',
            'prices.*.plan' => 'array:interval,interval_count|required_if:prices.*.recurring,true',
            'prices.*.plan.interval' => ['required_with:prices.*.plan', new EnumValidation(PlanInterval::class)],
            'prices.*.plan.interval_count' => 'required_with:prices.*.plan|integer'
        ]);

        $product = $this->productService->create($request->all());

        // Load the necessary relationships to return
        $product->load('prices');

        return ProductResource::make($product);
    }

    public function update($id, Request $request)
    {
        /** @var Product $product */
        $product = Product::findOrFail($id);

        $request->validate([
            'code' => 'string|unique:EnricoNardo\EcommerceLayer\Models\Product,code',
            'name' => 'string',
            'active' => 'boolean',
            'shippable' => 'boolean',
            'metadata' => 'array'
        ]);

        $product = $this->productService->update($product, $request->all());

        // Load the necessary relationships to return
        $product->load('prices');

        return ProductResource::make($product);
    }

    public function delete($id)
    {
        /** @var Product $product */
        $product = Product::findOrFail($id);

        $this->productService->delete($product);

        return response()->json([], HttpStatusCode::No_Content);
    }
}
