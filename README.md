# Laravel Ecommerce Layer

This is a work in progress project.

## Installation

You can install the package via composer:

```bash
composer require enriconardo/laravel-ecommerce-layer:dev-master
```

### Enable Subscriptions handler

If you decide to use subscriptions, in order to regularly check the subscriptions status and automatically renew or cancel them, you have to schedule the proper task in the file `app\Console\Kernel.php` of your application:

``` php
protected function schedule(Schedule $schedule): void
{
    // You can schedule with the frequency you like
    $schedule->job(new \EcommerceLayer\Jobs\RenewSubscriptions)->hourly();
}
```

## Usage

### Publish configuration

``` bash
php artisan vendor:publish --provider="EcommerceLayer\Providers\ServiceProvider" --tag=config
```

### Using a payment gateway in your code

``` php
/** @var \EcommerceLayer\Gateways\GatewayProviderInterface $gateway */
$gateway = gateway('your_gateway_identifier');
```

## Authentication

Laravel Ecommerce Layer doesn't implement an authentication flow, this should be a responsability of the main application where it is installed.

In order to set an authentication guard, add the right middleware to the group of middlewares attached to the Laravel Ecommerce Layer routes by updating the configuration file. First of all publish the configuration file, like reported [here](#publish-configuration), then change the following attribute:

``` php
# File config/ecommerce-layer.php

'http' => [
    'routes' => [
        'middlewares' => [
            'api', // Don't remove this unless it is really necessary
            'your-custom-middleware' // E.g: auth:api
        ],
    ]
],
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

Then you need to add a proper record in the database, to do this just type the following command:

```bash
php artisan ecommerce-layer:gateway:create {name} {identifier}
```

Where `name` is a label for your Gateway (eg: Stripe) and `identifier` is a value for the system to distinguish it from others (eg: your-identifier). Do not use spaces.

## ISO standards

ISO standards for currencies, countries, languages and HTTP status codes are handled by PHP enums of the `prinsfrank/standards` package. Check it on [Github](https://github.com/PrinsFrank/standards).

In the *Laravel Ecommerce Layer*, the ISO standards used are:

- **ISO4217_Alpha_3** for currencies (three letters)
- **ISO3166_1_Alpha_2** for countries (two letters)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## To Do

- [ ] Adding discounts management
- [ ] Tax management
- [ ] Shipping