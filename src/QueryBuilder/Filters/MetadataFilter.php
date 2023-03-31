<?php

namespace EcommerceLayer\QueryBuilder\Filters;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class MetadataFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        $value = Str::contains($value, ',') ? explode(',', $value) : $value;

        $property = Str::contains($property, '.') ? Str::replace('.', '->', $property) : $property;

        if (is_array($value)) {
            $query->whereJsonContains($property, $value);
        } else {
            $query->where($property, $value);
        }
    }
}
