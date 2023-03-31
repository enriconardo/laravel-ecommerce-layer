<?php

namespace EcommerceLayer\Http\Controllers;

use EcommerceLayer\Http\Resources\OrderResource;
use EcommerceLayer\Models\Order;
use EcommerceLayer\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum as EnumValidation;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use PrinsFrank\Standards\Currency\ISO4217_Alpha_3 as Currency;
use PrinsFrank\Standards\Country\ISO3166_1_Alpha_2 as Country;
use PrinsFrank\Standards\Http\HttpStatusCode;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function list(Request $request)
    {
        $orders = QueryBuilder::for(Order::class)
            ->allowedFilters(['status', 'payment_status', 'currency', AllowedFilter::exact('customer.id')])
            ->allowedSorts('status', 'payment_status')
            ->paginate()
            ->appends($request->query());

        return OrderResource::collection($orders);
    }

    public function find($id)
    {
        /** @var Order $order */
        $order = Order::findOrFail($id);

        return OrderResource::make($order);
    }

    public function create(Request $request)
    {
        $request->validate([
            'currency' => ['string', 'required', new EnumValidation(Currency::class)],
            'customer_id' => 'string|required|exists:EcommerceLayer\Models\Customer,id',
            'gateway_id' => 'string|exists:EcommerceLayer\Models\Gateway,id',
            'metadata' => 'array',
            'billing_address' => 'array:address_line_1,address_line_2,postal_code,city,state,country,fullname,phone',
            'billing_address.country' => [new EnumValidation(Country::class)],
            'payment_method' => 'array:type,data',
            'payment_method.type' => 'string|required_with:payment_method',
            'payment_method.data' => 'array|required_with:payment_method',
        ]);

        $order = $this->orderService->create($request->all());

        return OrderResource::make($order);
    }

    public function update($id, Request $request)
    {
        /** @var Order $order */
        $order = Order::findOrFail($id);

        $request->validate([
            'currency' => ['string', new EnumValidation(Currency::class)],
            'gateway_id' => 'string|exists:EcommerceLayer\Models\Gateway,id',
            'metadata' => 'array',
            'billing_address' => 'array:address_line_1,address_line_2,postal_code,city,state,country,fullname,phone',
            'billing_address.country' => [new EnumValidation(Country::class)],
            'payment_method' => 'array:type,data',
            'payment_method.type' => 'string|required_with:payment_method',
            'payment_method.data' => 'array|required_with:payment_method',
        ]);

        $order = $this->orderService->update($order, $request->all());

        return OrderResource::make($order);
    }

    public function place($id, Request $request)
    {
        /** @var Order $order */
        $order = Order::findOrFail($id);

        $request->validate([
            'currency' => ['string', new EnumValidation(Currency::class)],
            'gateway_id' => [
                'string', 
                Rule::requiredIf(!$order->gateway()->exists()), 
                'exists:EcommerceLayer\Models\Gateway,id'
            ],
            'billing_address' => [
                'array:address_line_1,address_line_2,postal_code,city,state,country,fullname,phone',
                Rule::requiredIf($order->billing_address === null)
            ],
            'billing_address.country' => [new EnumValidation(Country::class)],
            'payment_method' => ['array:type,data', Rule::requiredIf($order->payment_method === null)],
            'payment_method.type' => 'string|required_with:payment_method',
            'payment_method.data' => 'array|required_with:payment_method',
        ]);

        $order = $this->orderService->place($order, $request->all());

        return OrderResource::make($order);
    }

    public function delete($id)
    {
        /** @var Order $order */
        $order = Order::findOrFail($id);

        $order = $this->orderService->delete($order);

        return response()->json([], HttpStatusCode::No_Content->value);
    }
}
