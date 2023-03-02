<?php

namespace EcommerceLayer\Http\Controllers;

use EcommerceLayer\Models\Price;
use Illuminate\Http\Request;
use PrinsFrank\Standards\Http\HttpStatusCode;
use Illuminate\Validation\Rules\Enum as EnumValidation;
use PrinsFrank\Standards\Currency\ISO4217_Alpha_3 as Currency;
use EcommerceLayer\Enums\PlanInterval;
use EcommerceLayer\Http\Resources\PriceResource;
use EcommerceLayer\Services\PriceService;

class PriceController extends Controller
{
    protected PriceService $priceService;

    public function __construct(PriceService $priceService)
    {
        $this->priceService = $priceService;
    }

    public function create(Request $request)
    {
        $request->validate([
            'product_id' => 'string|required|exists:EcommerceLayer\Models\Product,id',
            'unit_amount' => 'required|integer',
            'currency' => ['string', 'required', new EnumValidation(Currency::class)],
            'description' => 'string',
            'active' => 'boolean',
            'default' => 'boolean',
            'recurring' => 'boolean',
            'plan' => 'array:interval,interval_count|required_if:recurring,true',
            'plan.interval' => ['required_with:plan', new EnumValidation(PlanInterval::class)],
            'plan.interval_count' => 'required_with:plan|integer'
        ]);

        $price = $this->priceService->create($request->all());

        return PriceResource::make($price);
    }

    public function update($id, Request $request)
    {
        /** @var Price $price */
        $price = Price::findOrFail($id);

        $request->validate([
            'currency' => ['string', new EnumValidation(Currency::class)],
            'unit_amount' => 'integer',
            'description' => 'string',
            'active' => 'boolean',
            'default' => 'boolean',
            'recurring' => 'boolean',
            'plan' => 'array:interval,interval_count|required_if:recurring,true',
            'plan.interval' => ['required_with:plan', new EnumValidation(PlanInterval::class)],
            'plan.interval_count' => 'required_with:plan|integer'
        ]);

        $price = $this->priceService->update($price, $request->all());

        return PriceResource::make($price);
    }

    public function delete($id)
    {
        /** @var Price $price */
        $price = Price::findOrFail($id);

        $this->priceService->delete($price);

        return response()->json([], HttpStatusCode::No_Content);
    }
}
