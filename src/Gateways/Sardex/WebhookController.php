<?php

namespace EcommerceLayer\Gateways\Sardex;

use EcommerceLayer\Enums\PaymentStatus;
use EcommerceLayer\Gateways\Events\GatewayPaymentUpdated;
use EcommerceLayer\Gateways\Models\GatewayPayment;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class WebhookController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function handle(Request $request)
    {
        // Sardex handles only success webhook
        // TODO since I don't know what it returns

        // Call paymentService->confirm()
    }
}
