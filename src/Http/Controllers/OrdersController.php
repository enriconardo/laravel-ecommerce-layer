<?php

namespace EnricoNardo\EcommerceLayer\Http\Controllers;

use EnricoNardo\EcommerceLayer\Enums\OrderStatus;
use EnricoNardo\EcommerceLayer\Gateways\GatewayServiceFactory;
use EnricoNardo\EcommerceLayer\Gateways\GatewayServiceInterface;
use EnricoNardo\EcommerceLayer\ModelBuilders\OrderBuilder;
use EnricoNardo\EcommerceLayer\Models\Address;
use EnricoNardo\EcommerceLayer\Models\Order;
use EnricoNardo\EcommerceLayer\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrdersController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'customer_id' => 'string|required',
            'currency' => 'string|required|size:3',
            'gateway_id' => 'string',
            'billing_address' => 'array:address_line_1,address_line_2,postal_code,city,state,country,fullname,phone',
            'payment_method' => 'array:type,data',
            'payment_method.type' => 'string|required_with:payment_method',
            'payment_method.data' => 'array|required_with:payment_method',
        ]);

        $data = [
            'status' => OrderStatus::DRAFT,
            'currency' => $request->input('currency'),
            'billing_address' => $request->has('billing_address') 
                ? new Address($request->input('billing_address')) 
                : null,
            'payment_method' => $request->has('payment_method') 
                ? new PaymentMethod($request->input('payment_method.type'), $request->input('payment_method.data', [])) 
                : null,
        ];

        $builder = OrderBuilder::init()->fill($data);
        $builder->withCustomer($request->input('customer_id'));

        if ($request->has('gateway_id')) {
            $builder->withGateway($request->input('gateway_id'));
        }

        $order = $builder->end();

        // return $order
    }

    public function update($id, Request $request)
    {
        /** @var Order $order */
        $order = Order::findOrFail($id);

        $request->validate([
            'currency' => 'string|size:3',
            'gateway_id' => 'string',
            'billing_address' => 'array:address_line_1,address_line_2,postal_code,city,state,country,fullname,phone',
            'payment_method' => 'array:type,data',
            'payment_method.type' => 'string|required_with:payment_method',
            'payment_method.data' => 'array|required_with:payment_method',
        ]);

        $data = [
            'currency' => $request->input('currency'),
            'billing_address' => $request->has('billing_address') 
                ? new Address($request->input('billing_address')) 
                : null,
            'payment_method' => $request->has('payment_method') 
                ? new PaymentMethod($request->input('payment_method.type'), $request->input('payment_method.data', [])) 
                : null,
        ];

        $builder = OrderBuilder::init($order)->fill($data);

        if ($request->has('gateway_id')) {
            $builder->withGateway($request->input('gateway_id'));
        }

        $order = $builder->end();

        // return $order
    }

    public function place($id, Request $request)
    {
        /** @var Order $order */
        $order = Order::findOrFail($id);

        $request->validate([
            'status' => OrderStatus::OPEN,
            'currency' => 'string|size:3',
            'gateway_id' => ['string', Rule::requiredIf(!$order->gateway()->exists())],
            'billing_address' => ['array:address_line_1,address_line_2,postal_code,city,state,country,fullname,phone', Rule::requiredIf($order->billing_address === null)],
            'payment_method' => ['array:type,data', Rule::requiredIf($order->payment_method === null)],
            'payment_method.type' => 'string|required_with:payment_method',
            'payment_method.data' => 'array|required_with:payment_method'
        ]);

        $data = [
            'currency' => $request->input('currency'),
            'billing_address' => $request->has('billing_address') 
                ? new Address($request->input('billing_address')) 
                : null,
            'payment_method' => $request->has('payment_method') 
                ? new PaymentMethod($request->input('payment_method.type'), $request->input('payment_method.data', [])) 
                : null,
        ];
 
        $builder = OrderBuilder::init($order)->fill($data);

        if ($request->has('gateway_id')) {
            $builder->withGateway($request->input('gateway_id'));
        }

        $order = $builder->end();

        /** @var GatewayServiceInterface $gatewayService */
        $gatewayService = app(GatewayServiceFactory::class)->make($order->gateway->identifier);
        $payment = $gatewayService->payments()->create($order->total, $order->currency, $order->payment_method, $order->billing_address);

        $order = OrderBuilder::init($order)->fill([
            'payment_status' => $payment->status,
            'gateway_payment_identifier' => $payment->gateway_identifier
        ])->end();

        // return $order
    }
}
