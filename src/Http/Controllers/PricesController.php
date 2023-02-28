<?php

namespace EnricoNardo\EcommerceLayer\Http\Controllers;

use EnricoNardo\EcommerceLayer\Models\Price;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use PrinsFrank\Standards\Http\HttpStatusCode;
use Illuminate\Validation\Rules\Enum as EnumValidation;
use PrinsFrank\Standards\Currency\ISO4217_Alpha_3 as Currency;
use EnricoNardo\EcommerceLayer\Enums\PlanInterval;
use EnricoNardo\EcommerceLayer\Http\Resources\PriceResource;
use EnricoNardo\EcommerceLayer\ModelBuilders\PriceBuilder;

class PricesController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'product_id' => 'string|required|exists:EnricoNardo\EcommerceLayer\Models\Product,id',
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

        $data = [
            'product_id' => $request->input('product_id'),
            'unit_amount' => $request->input('unit_amount'),
            'currency' => $request->input('currency'),
            'description' => $request->input('description'),
            'active' => $request->input('active'),
            'default' => $request->input('default'),
            'recurring' => $request->input('recurring'),
            'plan' => $request->input('plan', null)
        ];

        $price = PriceBuilder::init()->fill($data)->end();

        return PriceResource::make($price);
    }

    public function update($id, Request $request)
    {
        /** @var Price $price */
        $price = Price::findOrFail($id);

        $subscriptionsCount = $price->subscriptions()->where('active', true)->count();

        if ($subscriptionsCount > 0) {
            throw new BadRequestHttpException("There are some active subscriptions related to the price. Deactivate the current price and create a new one instead.");
        }

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

        $data = [
            'currency' => $request->input('currency'),
            'unit_amount' => $request->input('unit_amount'),
            'description' => $request->input('description'),
            'active' => $request->input('active'),
            'default' => $request->input('default'),
            'recurring' => $request->input('recurring'),
            'plan' => $request->input('plan', null)
        ];

        $price = PriceBuilder::init($price)->fill($data)->end();

        return PriceResource::make($price);
    }

    public function delete($id)
    {
        /** @var Price $price */
        $price = Price::findOrFail($id);

        $subscriptionsCount = $price->subscriptions()->where('active', true)->count();

        if ($subscriptionsCount > 0) {
            throw new BadRequestHttpException("There are some active subscriptions related to the price. Deactivate the price instead.");
        }

        $price->delete();

        return response()->json([], HttpStatusCode::No_Content);
    }
}
