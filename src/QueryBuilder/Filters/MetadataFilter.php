<?php

namespace EcommerceLayer\QueryBuilder\Filters;

use Exception;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class MetadataFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        if (!is_array($value) || sizeof($value) > 2) {
            throw new Exception('Metadata filter must be shaped as the following: filter[metadata]=key,value');
        }

        $key = $value[0];
        $realValue = $value[1];

        $key = Str::contains($key, '.') ? Str::replace('.', '->', $key) : $key;

        $query->where("metadata->$property", $realValue);
    }
}
