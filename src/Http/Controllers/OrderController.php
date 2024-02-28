<?php

namespace EcommerceLayer\Http\Controllers;

use EcommerceLayer\Events\Order\OrderPlaced;
use EcommerceLayer\Exceptions\InvalidEntityException;
use EcommerceLayer\Http\Resources\OrderResource;
use EcommerceLayer\Models\Gateway;
use EcommerceLayer\Models\Order;
use EcommerceLayer\Services\OrderService;
use EcommerceLayer\Services\PaymentMethodService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum as EnumValidation;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use PrinsFrank\Standards\Currency\ISO4217_Alpha_3 as Currency;
use PrinsFrank\Standards\Country\ISO3166_1_Alpha_2 as Country;
use PrinsFrank\Standards\Http\HttpStatusCode;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class OrderController extends Controller
{
    protected OrderService $orderService;

    protected PaymentMethodService $paymentMethodService;

    public function __construct(
        OrderService $orderService,
        PaymentMethodService $paymentMethodService
    ) {
        $this->orderService = $orderService;
        $this->paymentMethodService = $paymentMethodService;
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
            'customer_id' => 'required|exists:EcommerceLayer\Models\Customer,id',
            'metadata' => 'array',
            'billing_address' => 'array:address_line_1,address_line_2,postal_code,city,state,country,fullname,phone',
            'billing_address.country' => ['nullable', new EnumValidation(Country::class)],
            'discount_percentage' => 'numeric|nullable'
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
            'metadata' => 'array',
            'billing_address' => 'array:address_line_1,address_line_2,postal_code,city,state,country,fullname,phone',
            'billing_address.country' => ['nullable', new EnumValidation(Country::class)],
            'discount_percentage' => 'numeric|nullable'
        ]);

        $order = $this->orderService->update($order, $request->all());

        return OrderResource::make($order);
    }

    public function place($id, Request $request)
    {
        /** @var Order $order */
        $order = Order::findOrFail($id);
        
        $request->validate([
            'gateway_id' => 'required|exists:EcommerceLayer\Models\Gateway,id',
            'payment_method' => 'required|array:type,data',
            'payment_method.type' => 'string|required_with:payment_method',
            'payment_method.data' => 'array|nullable',
            'other_payment_data' => 'array' // e.g: return_url, success_url...
        ]);

        $gatewayId = $request->input('gateway_id');
        /** @var \EcommerceLayer\Models\Gateway $gateway */
        $gateway = Gateway::find($gatewayId);
        if (!$gateway) {
            throw new BadRequestHttpException("Gateway with id [$gatewayId] does not exists");
        }

        /** @var \EcommerceLayer\Models\PaymentMethod $paymentMethod */
        $paymentMethod = $this->paymentMethodService->create($gateway, $request->input('payment_method'));

        $order = $this->orderService->update($order, [
            'payment_method' => $paymentMethod,
            'gateway_id' => $gateway->id
        ]);

        $order = $this->orderService->place($order, $request->input('other_payment_data', []));

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
