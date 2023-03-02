<?php
 
namespace EcommerceLayer\Listeners;

use EcommerceLayer\Events\Order\OrderPlacing;
use EcommerceLayer\Gateways\Stripe\CustomerService;

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