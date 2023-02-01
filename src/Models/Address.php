<?php

namespace EnricoNardo\EcommerceLayer\Models;

use Illuminate\Support\Arr;

/**
 * @property string|null $address_line_1 Address line 1 (e.g: street, PO Box, or company name).
 * @property string|null $address_line_2 Address line 2 (e.g: apartment, suite, unit, or building).
 * @property string|null $postal_code ZIP or postal code.
 * @property string|null $city City, district, suburb, town, or village.
 * @property string|null $state State, county, province, or region.
 * @property string|null $country Two-letter country code (ISO 3166-1 alpha-2).
 * @property string|null $fullname
 * @property string|null $phone
 */
final class Address
{
    public string|null $address_line_1;
    public string|null $address_line_2;
    public string|null $postal_code;
    public string|null $city;
    public string|null $state;
    public string|null $country;
    public string|null $fullname;
    public string|null $phone;

    public function __construct(array $data = []) 
    {
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