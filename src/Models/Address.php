<?php

namespace EnricoNardo\EcommerceLayer\Models;

use Illuminate\Support\Arr;

final class Address
{
    // Address line 1 (e.g., street, PO Box, or company name).
    public string|null $address_line_1;

    // Address line 2 (e.g., apartment, suite, unit, or building).
    public string|null $address_line_2;

    // ZIP or postal code.
    public string|null $postal_code;

    // City, district, suburb, town, or village.
    public string|null $city;

    // State, county, province, or region.
    public string|null $state;

    // Two-letter country code (ISO 3166-1 alpha-2).
    public string|null $country;

    public string|null $fullname;

    public string|null $phone;

    public function __construct(array $data = []) {
        $this->address_line_1 = Arr::get($data, 'address_line_1');
        $this->address_line_2 = Arr::get($data, 'address_line_2');
        $this->postal_code = Arr::get($data, 'postal_code');
        $this->city = Arr::get($data, 'city');
        $this->state = Arr::get($data, 'state');
        $this->country = Arr::get($data, 'country');
        $this->fullname = Arr::get($data, 'fullname');
        $this->phone = Arr::get($data, 'phone');
    }
}