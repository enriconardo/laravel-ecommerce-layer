<?php

namespace EcommerceLayer\Gateways\Sardex;

use EcommerceLayer\Enums\PaymentStatus;
use EcommerceLayer\Gateways\Events\GatewayWebhookCalled;
use EcommerceLayer\Gateways\Models\GatewayPayment;
use EcommerceLayer\Services\OrderService;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;

class WebhookController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function handle(Request $request)
    {
        // Sardex handles only success webhook
        $orderId = $request->input('orderId');
        $ticketNumber = $request->input('ticketNumber');

        /** @var \EcommerceLayer\Gateways\GatewayProviderInterface $gatewayProviderService */
        $gatewayProviderService = gateway('sardex');

        $payment = new GatewayPayment(
            $ticketNumber,
            PaymentStatus::PENDING,
            [
                'order_id' => $orderId,
                'ticket_number' => $ticketNumber
            ]
        );

        try {
            $payment = $gatewayProviderService->payments()->confirm($payment);
            GatewayWebhookCalled::dispatch($payment);
        } catch (Exception $e) {
            $payment->status = PaymentStatus::REFUSED;
            GatewayWebhookCalled::dispatch($payment);
        }

        http_response_code(200);
    }
}
