<?php

namespace EcommerceLayer\Commands;

use EcommerceLayer\Services\GatewayService;
use Illuminate\Console\Command;

class CreateGateway extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ecommerce-layer:gateway:create 
        {name : The name of the Gateway (e.g: Stripe)} 
        {identifier : A human and machine readable identifier for the gateway (e.g: stripe). Do not use spaces}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a gateway in the database';

    /**
     * Execute the console command.
     */
    public function handle(GatewayService $gatewayService): void
    {
        $gatewayService->create($this->arguments());

        $this->info('Gateway record created');
    }
}
