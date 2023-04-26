<?php

namespace EcommerceLayer\Gateways\Stripe;

use EcommerceLayer\Enums\PaymentStatus;
use EcommerceLayer\Gateways\Events\GatewayPaymentUpdated;
use EcommerceLayer\Gateways\Models\GatewayPayment;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Stripe\PaymentIntent;

class WebhookController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function handle(Request $request)
    {
        $endpointSecret = config('ecommerce-layer.gateways.stripe.endpoint_secret');
        $signature = $request->server('HTTP_STRIPE_SIGNATURE');
        $payload = $request->getContent();

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $signature, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            throw new BadRequestHttpException('Invalid payload');
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            throw new BadRequestHttpException('Invalid signature');
        }

        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                /** @var PaymentIntent $paymentIntent */
                $paymentIntent = $event->data->object;

                $payment = new GatewayPayment(
                    $paymentIntent->id,
                    PaymentStatus::PAID
                );

                GatewayPaymentUpdated::dispatch($payment);

                http_response_code(200);
            case 'payment_intent.payment_failed':
                /** @var PaymentIntent $paymentIntent */
                $paymentIntent = $event->data->object;

                $payment = new GatewayPayment(
                    $paymentIntent->id,
                    PaymentStatus::REFUSED
                );

                GatewayPaymentUpdated::dispatch($payment);

                http_response_code(200);
            default:
                return response('Received unknown event type ' . $event->type);
        }
    }
}
