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
 * @return \EnricoNardo\EcommerceLayer\Gateways\GatewayServiceInterface
 */
if (!function_exists('gateway')) {
    function gateway(string $identifier): \EnricoNardo\EcommerceLayer\Gateways\GatewayServiceInterface
    {
        return app(\EnricoNardo\EcommerceLayer\Gateways\GatewayServiceFactory::class)->make($identifier);
    }
}
