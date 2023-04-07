<?php

namespace EcommerceLayer\Gateways\Stripe;

use EcommerceLayer\Enums\PaymentStatus;
use EcommerceLayer\Gateways\Events\GatewayPaymentRefused;
use EcommerceLayer\Gateways\Events\GatewayPaymentSucceeded;
use EcommerceLayer\Gateways\Events\GatewayPaymentUpdated;
use EcommerceLayer\Gateways\Models\Payment;
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
        $endpointSecret = 'whsec_b302ec2f25cc5ce5c41c7894cede30bcebb4eed8e0be4205bd205db314e5ee75';
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
                http_response_code(200);

                /** @var PaymentIntent $paymentIntent */
                $paymentIntent = $event->data->object;
                
                $additionalData = [];
                if ($paymentIntent->payment_method) {
                    $additionalData['payment_method_key'] = $paymentIntent->payment_method;
                }

                $payment = new Payment(
                    $paymentIntent->id,
                    PaymentStatus::PAID,
                    $additionalData
                );

                GatewayPaymentUpdated::dispatch($payment);
            case 'payment_intent.payment_failed':
                http_response_code(200);

                /** @var PaymentIntent $paymentIntent */
                $paymentIntent = $event->data->object;

                $additionalData = [];
                if ($paymentIntent->payment_method) {
                    $additionalData['payment_method_key'] = $paymentIntent->payment_method;
                }

                $payment = new Payment(
                    $paymentIntent->id,
                    PaymentStatus::REFUSED,
                    $additionalData
                );

                GatewayPaymentUpdated::dispatch($payment);
            default:
                return response('Received unknown event type ' . $event->type);
        }
    }
}
