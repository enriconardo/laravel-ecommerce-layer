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
