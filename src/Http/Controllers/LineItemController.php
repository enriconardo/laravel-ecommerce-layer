<?php

namespace EnricoNardo\EcommerceLayer\Http\Controllers;

use EnricoNardo\EcommerceLayer\Enums\OrderStatus;
use EnricoNardo\EcommerceLayer\Http\Resources\LineItemResource;
use EnricoNardo\EcommerceLayer\ModelBuilders\LineItemBuilder;
use EnricoNardo\EcommerceLayer\Models\LineItem;
use EnricoNardo\EcommerceLayer\Models\Order;
use Illuminate\Http\Request;
use PrinsFrank\Standards\Http\HttpStatusCode;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class LineItemController extends Controller
{
    public function list(Request $request)
    {
        $lineItems = QueryBuilder::for(LineItem::class)
            ->allowedFilters([AllowedFilter::exact('price.id'), AllowedFilter::exact('order.id')])
            ->allowedSorts('quantity')
            ->paginate()
            ->appends($request->query());

        return LineItemResource::collection($lineItems);
    }

    public function find($id)
    {
        /** @var LineItem $lineItem */
        $lineItem = LineItem::findOrFail($id);

        return LineItemResource::make($lineItem);
    }

    public function create(Request $request)
    {
        $request->validate([
            'quantity' => 'integer|required',
            'price_id' => 'string|required|exists:EnricoNardo\EcommerceLayer\Models\Price,id',
            'order_id' => 'string|required|exists:EnricoNardo\EcommerceLayer\Models\Order,id'
        ]);

        $data = [
            'quantity' => $request->input('quantity'),
            'price_id' => $request->input('price_id'),
            'order_id' => $request->input('order_id'),
        ];

        $lineItem = LineItemBuilder::init()->fill($data)->end();

        return LineItemResource::make($lineItem);
    }

    public function update($id, Request $request)
    {
        /** @var LineItem $lineItem */
        $lineItem = LineItem::findOrFail($id);

        $request->validate([
            'quantity' => 'integer'
        ]);

        $data = [
            'quantity' => $request->input('quantity'),
        ];

        $lineItem = LineItemBuilder::init($lineItem)->fill($data)->end();

        return LineItemResource::make($lineItem);
    }

    public function delete($id)
    {
        /** @var LineItem $lineItem */
        $lineItem = LineItem::findOrFail($id);

        /** @var Order $order */
        $order = $lineItem->order;

        if ($order->status !== OrderStatus::DRAFT) {
            throw new BadRequestHttpException("You cannot update an order that has been already closed.");
        }

        $lineItem->delete();

        return response()->json([], HttpStatusCode::No_Content);
    }
}
