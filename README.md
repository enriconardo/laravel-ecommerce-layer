# Laravel Ecommerce Layer

## Installation

You can install the package via composer:

```bash
composer require enriconardo/laravel-ecommerce-layer
```

## Usage

### Using a payment gateway in your code

``` php
/** @var \EnricoNardo\EcommerceLayer\Gateways\GatewayServiceInterface $gateway */
$gateway = gateway('your_gateway_identifier');
```

## Adding custom payment gateway

A gateway package is composed by a set of classes:

- A *Main class* which extends `\EnricoNardo\EcommerceLayer\Gateways\GatewayServiceInterface`
- A *Payment service* class which extends `\EnricoNardo\EcommerceLayer\Gateways\PaymentServiceInterface`
- A *Customer service* class which extends `\EnricoNardo\EcommerceLayer\Gateways\CustomerServiceInterface`

In order to enable the gateway, just add the following line of code in the `register` function of one of your service provider:

``` php
/**
* Register the application services.
*/
public function register()
{
    // ...

    // Enable the gateways
    $this->app->make(GatewayServiceFactory::class)->enableGateway(new YourMainClass);

    // ...
}
```

`YourMainClass` is your actual *Main class* described before.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
