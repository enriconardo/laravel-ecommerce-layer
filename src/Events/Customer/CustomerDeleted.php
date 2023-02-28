<?php

namespace EnricoNardo\EcommerceLayer\Events\Customer;

use EnricoNardo\EcommerceLayer\Models\Customer;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CustomerDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Customer $customer;

    /**
     * Create a new event instance.
     */
    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }
}
