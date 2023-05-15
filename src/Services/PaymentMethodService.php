<?php

namespace EcommerceLayer\Services;

use EcommerceLayer\Models\Gateway;
use EcommerceLayer\Models\PaymentMethod;
use Exception;
use Illuminate\Support\Arr;

class PaymentMethodService
{
    public function create(Gateway $gateway, array $data = []): PaymentMethod
    {
        $paymentMethod = new PaymentMethod(
            Arr::get($data, 'type'),
            Arr::get($data, 'data', [])
        );

        return $this->syncWithGateway($paymentMethod, $gateway);
    }

    public function syncWithGateway(PaymentMethod $paymentMethod, Gateway $gateway): PaymentMethod
    {
        /** @var \EcommerceLayer\Gateways\GatewayProviderInterface $gatewayProviderService */
        $gatewayProviderService = gateway($gateway->identifier);

        if (!$gatewayProviderService) {
            throw new Exception("The gateway [$gateway->identifier] is not enabled");
        }

        /** @var \EcommerceLayer\Gateways\Models\GatewayPaymentMethod $gatewayPaymentMethod */
        $gatewayPaymentMethod = $gatewayProviderService->paymentMethods()->create(
            $paymentMethod->type,
            $paymentMethod->data
        );

        $paymentMethod->gateway_id = $gatewayPaymentMethod->id;

        return $paymentMethod;
    }
}
