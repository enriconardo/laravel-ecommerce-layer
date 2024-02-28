<?php

/**
 * Removes from an associative array the values that are not included in a specified list of keys
 *
 * @return response()
 */
if (!function_exists('attributes_filter')) {
    function attributes_filter(array $array, array $inArray = [])
    {
        return array_filter($array, function ($val, $key) use ($inArray) {
            return in_array($key, $inArray);
        }, ARRAY_FILTER_USE_BOTH);
    }
}

/**
 * Get the instance of the gateway based on the identifier
 * 
 * @return \EcommerceLayer\Gateways\GatewayProviderInterface
 */
if (!function_exists('gateway')) {
    function gateway(string $identifier): \EcommerceLayer\Gateways\GatewayProviderInterface|null
    {
        return app(\EcommerceLayer\Gateways\GatewayProviderFactory::class)->make($identifier);
    }
}
