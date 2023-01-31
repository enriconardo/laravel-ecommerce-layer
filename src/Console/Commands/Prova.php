<?php

namespace EnricoNardo\EcommerceLayer\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Stripe\StripeClient;

class Prova extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prova';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bla Bla';

    /**
     * Execute the console command.
     *
     * @param  \App\Support\DripEmailer  $drip
     * @return mixed
     */
    public function handle()
    {
        $stripe = new StripeClient([
            'api_key' => config('ecommerce-layer.gateways.stripe.secret_key')
        ]);

        /*
        $paymentMethod = $stripe->paymentMethods->create([
            'type' => 'card',
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => 8,
                'exp_year' => 2030,
                'cvc' => '111',
            ],
        ]);
        */

        $paymentIntent = $stripe->paymentIntents->create(
            [
                'amount' => 2000,
                'currency' => 'eur',
                // 'payment_method' => $paymentMethod->id
            ]
        );

        $i = 0;

        // $paymentIntent->confirm();

        // Ora verifico se lo status è "succeded" oppure "requires_action" o altro (errore quindi)
    }
}
