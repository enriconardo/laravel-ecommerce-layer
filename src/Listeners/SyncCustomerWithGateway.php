<?php
 
namespace EnricoNardo\EcommerceLayer\Listeners;

use EnricoNardo\EcommerceLayer\Events\Order\OrderPlacing;
use EnricoNardo\EcommerceLayer\Gateways\Stripe\CustomerService;

class SyncCustomerWithGateway
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        // ...
    }
 
    /**
     * Handle the event.
     */
    public function handle(OrderPlacing $event): void
    {
        $customerService = resolve(CustomerService::class);
        $customerService->syncWithGateway($event->order->customer, $event->order->gateway);
    }
}