<?php

namespace EcommerceLayer\Http\Controllers;

use PrinsFrank\Standards\Currency\ISO4217_Alpha_3 as Currency;
use PrinsFrank\Standards\Http\HttpStatusCode;
use EcommerceLayer\Enums\PlanInterval;
use EcommerceLayer\Http\Resources\ProductResource;
use EcommerceLayer\Models\Product;
use EcommerceLayer\Services\ProductService;
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
        $this->_validateCreateRequest($request);

        $product = $this->productService->create($request->all());

        // Load the necessary relationships to return
        $product->load('prices');

        return ProductResource::make($product);
    }

    public function update($id, Request $request)
    {
        /** @var Product $product */
        $product = Product::findOrFail($id);

        $this->_validateUpdateRequest($request);

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

        return response()->json([], HttpStatusCode::No_Content->value);
    }

    protected function _validateCreateRequest(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:EcommerceLayer\Models\Product,code',
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
    }

    protected function _validateUpdateRequest(Request $request)
    {
        $request->validate([
            'code' => 'string|unique:EcommerceLayer\Models\Product,code',
            'name' => 'string',
            'active' => 'boolean',
            'shippable' => 'boolean',
            'metadata' => 'array'
        ]);
    }
}
