<?php
 
namespace EcommerceLayer\Listeners;

use EcommerceLayer\Events\Order\OrderPlaced;
use EcommerceLayer\Services\CustomerService;

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
    public function handle(OrderPlaced $event): void
    {
        $customerService = resolve(CustomerService::class);
        $customerService->syncWithGateway($event->order->customer, $event->order->gateway);
    }
}