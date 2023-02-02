<?php

namespace EnricoNardo\EcommerceLayer\Http\Controllers;

use EnricoNardo\EcommerceLayer\Models\Price;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PricesController extends Controller
{
    public function update($id, Request $request)
    {
        /** @var Price $price */
        $price = Price::findOrFail($id);

        $subscriptionsCount = $price->subscriptions()->where('active', true)->count();

        if ($subscriptionsCount > 0) {
            throw new BadRequestHttpException("There are some active subscriptions related to the price. Deactivate the current price and create a new one instead.");
        }

        // TODO
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

        return response()->json([], 204);
    }
}
