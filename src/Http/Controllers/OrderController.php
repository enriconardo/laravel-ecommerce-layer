<?php

namespace EnricoNardo\EcommerceLayer\Http\Controllers;

use EnricoNardo\EcommerceLayer\Enums\OrderStatus;
use EnricoNardo\EcommerceLayer\Events\Order\OrderPlaced;
use EnricoNardo\EcommerceLayer\Http\Resources\OrderResource;
use EnricoNardo\EcommerceLayer\ModelBuilders\OrderBuilder;
use EnricoNardo\EcommerceLayer\Models\Order;
use EnricoNardo\EcommerceLayer\Services\CustomerService;
use EnricoNardo\EcommerceLayer\Services\OrderService;
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
            'customer_id' => 'string|required|exists:EnricoNardo\EcommerceLayer\Models\Customer,id',
            'gateway_id' => 'string|exists:EnricoNardo\EcommerceLayer\Models\Gateway,id',
            'metadata' => 'array',
            'billing_address' => 'array:address_line_1,address_line_2,postal_code,city,state,country,fullname,phone',
            'billing_address.country' => [new EnumValidation(Country::class)],
            'payment_method' => 'array:type,data',
            'payment_method.type' => 'string|required_with:payment_method',
            'payment_method.data' => 'array|required_with:payment_method',
        ]);

        $data = [
            'customer_id' => $request->input('customer_id'),
            'gateway_id' => $request->input('gateway_id'),
            'status' => OrderStatus::DRAFT,
            'currency' => $request->input('currency'),
            'metadata' => $request->input('metadata'),
        ];

        $builder = OrderBuilder::init()->fill($data);

        if ($request->has('billing_address')) {
            $builder->withBillingAddress($request->input('billing_address'));
        }

        if ($request->has('payment_method')) {
            $builder->withPaymentMethod($request->input('payment_method'));
        }

        $order = $builder->end();

        return OrderResource::make($order);
    }

    public function update($id, Request $request)
    {
        /** @var Order $order */
        $order = Order::findOrFail($id);

        $this->authorize('update', $order);

        $request->validate([
            'currency' => ['string', new EnumValidation(Currency::class)],
            'gateway_id' => 'string|exists:EnricoNardo\EcommerceLayer\Models\Gateway,id',
            'metadata' => 'array',
            'billing_address' => 'array:address_line_1,address_line_2,postal_code,city,state,country,fullname,phone',
            'billing_address.country' => [new EnumValidation(Country::class)],
            'payment_method' => 'array:type,data',
            'payment_method.type' => 'string|required_with:payment_method',
            'payment_method.data' => 'array|required_with:payment_method',
        ]);

        $data = [
            'gateway_id' => $request->input('gateway_id'),
            'currency' => $request->input('currency'),
            'metadata' => $request->input('metadata'),
        ];

        $builder = OrderBuilder::init($order)->fill($data);

        if ($request->has('billing_address')) {
            $builder->withBillingAddress($request->input('billing_address'));
        }

        if ($request->has('payment_method')) {
            $builder->withPaymentMethod($request->input('payment_method'));
        }

        $order = $builder->end();

        return OrderResource::make($order);
    }

    public function place($id, Request $request)
    {
        /** @var Order $order */
        $order = Order::findOrFail($id);

        $this->authorize('place', $order);

        $request->validate([
            'currency' => ['string', new EnumValidation(Currency::class)],
            'gateway_id' => ['string', Rule::requiredIf(!$order->gateway()->exists()), 'exists:EnricoNardo\EcommerceLayer\Models\Gateway,id'],
            'billing_address' => [
                'array:address_line_1,address_line_2,postal_code,city,state,country,fullname,phone',
                Rule::requiredIf($order->billing_address === null)
            ],
            'billing_address.country' => [new EnumValidation(Country::class)],
            'payment_method' => ['array:type,data', Rule::requiredIf($order->payment_method === null)],
            'payment_method.type' => 'string|required_with:payment_method',
            'payment_method.data' => 'array|required_with:payment_method',
        ]);

        $data = [
            'status' => OrderStatus::OPEN,
            'gateway_id' => $request->input('gateway_id'),
            'currency' => $request->input('currency'),
        ];

        $builder = OrderBuilder::init($order)->fill($data);

        if ($request->has('billing_address')) {
            $builder->withBillingAddress($request->input('billing_address'));
        }

        if ($request->has('payment_method')) {
            $builder->withPaymentMethod($request->input('payment_method'));
        }

        $order = $builder->end();

        /** @var CustomerService $customerService */
        $customerService = new CustomerService;
        $customerService->syncWithGateway($order->customer, $order->gateway);

        /** @var OrderService $service */
        $orderService = new OrderService;
        $order = $orderService->pay($order);

        OrderPlaced::dispatch($order);

        return OrderResource::make($order);
    }

    public function delete($id)
    {
        /** @var Order $order */
        $order = Order::findOrFail($id);

        $this->authorize('delete', $order);

        $order->delete();

        return response()->json([], HttpStatusCode::No_Content);
    }
}
