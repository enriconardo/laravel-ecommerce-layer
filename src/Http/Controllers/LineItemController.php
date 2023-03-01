<?php

namespace EnricoNardo\EcommerceLayer\Http\Controllers;

use EnricoNardo\EcommerceLayer\Http\Resources\LineItemResource;
use EnricoNardo\EcommerceLayer\Models\LineItem;
use EnricoNardo\EcommerceLayer\Models\Order;
use EnricoNardo\EcommerceLayer\Services\LineItemService;
use Illuminate\Http\Request;
use PrinsFrank\Standards\Http\HttpStatusCode;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class LineItemController extends Controller
{
    protected LineItemService $lineItemService;

    public function __construct(LineItemService $lineItemService)
    {
        $this->lineItemService = $lineItemService;
    }

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
        $order = Order::find($request->input('order_id'));

        $request->validate([
            'quantity' => 'integer|required',
            'price_id' => 'string|required|exists:EnricoNardo\EcommerceLayer\Models\Price,id',
            'order_id' => 'string|required|exists:EnricoNardo\EcommerceLayer\Models\Order,id'
        ]);

        $lineItem = $this->lineItemService->create($request->all(), $order);

        return LineItemResource::make($lineItem);
    }

    public function update($id, Request $request)
    {
        /** @var LineItem $lineItem */
        $lineItem = LineItem::findOrFail($id);

        $request->validate([
            'quantity' => 'integer'
        ]);

        $lineItem = $this->lineItemService->update($lineItem, $request->all());

        return LineItemResource::make($lineItem);
    }

    public function delete($id)
    {
        /** @var LineItem $lineItem */
        $lineItem = LineItem::findOrFail($id);

        $lineItem = $this->lineItemService->delete($lineItem);

        return response()->json([], HttpStatusCode::No_Content);
    }
}
