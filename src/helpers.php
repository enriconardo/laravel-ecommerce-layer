<?php

/**
 * Removes null values from the given array
 *
 * @return response()
 */
if (!function_exists('attributes_filter')) {
    function attributes_filter($array)
    {
        return array_filter($array, function ($val) {
            return !is_null($val);
        });
    }
}

/**
 * Get the instance of the gateway based on the identifier
 * 
 * @return \EcommerceLayer\Gateways\GatewayProviderInterface
 */
if (!function_exists('gateway')) {
    function gateway(string $identifier): \EcommerceLayer\Gateways\GatewayProviderInterface
    {
        return app(\EcommerceLayer\Gateways\GatewayProviderFactory::class)->make($identifier);
    }
}
