# Laravel Ecommerce Layer

This is a work in progress project.

## Installation

You can install the package via composer:

```bash
composer require enriconardo/laravel-ecommerce-layer:0.1.0-alpha@alpha
```

## Usage

### Using a payment gateway in your code

``` php
/** @var \EcommerceLayer\Gateways\GatewayProviderInterface $gateway */
$gateway = gateway('your_gateway_identifier');
```

## Authentication

Laravel Ecommerce Layer doesn't implement an authentication flow, this should be a responsability of the main application where it is installed.

### Set a custom authentication guard

By default the Laravel Ecommerce Layer routes are protected by the `web` guard, but you can set your cutom auth guard thanks to the `ECOMMERCE_LAYER_AUTH_GUARD` environment variable. For instance:

```
# .env file

ECOMMERCE_LAYER_AUTH_GUARD=jwt
```


## Adding custom payment gateway

A gateway package is composed by a set of classes:

- A *Main class* which extends `\EcommerceLayer\Gateways\GatewayProviderInterface`
- A *Payment service* class which extends `\EcommerceLayer\Gateways\PaymentServiceInterface`
- A *Customer service* class which extends `\EcommerceLayer\Gateways\CustomerServiceInterface`

In order to enable the gateway, just add the following line of code in the `register` function of one of your service provider:

``` php
/**
* Register the application services.
*/
public function register()
{
    // ...

    // Enable the gateways
    $this->app->make(GatewayProviderFactory::class)->enableGateway(new YourMainClass);

    // ...
}
```

`YourMainClass` is your actual *Main class* described before.

## ISO standards

ISO standards for currencies, countries, languages and HTTP status codes are handled by PHP enums of the `prinsfrank/standards` package. Check it on [Github](https://github.com/PrinsFrank/standards).

In the *Laravel Ecommerce Layer*, the ISO standards used are:

- **ISO4217_Alpha_3** for currencies (three letters)
- **ISO3166_1_Alpha_2** for countries (two letters)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## To Do

- [ ] Handle subscriptions
- [ ] Adding discounts management