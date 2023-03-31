<?php

namespace EcommerceLayer\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait HasMetadata
{
    public function initializeHasMetadata()
    {
        $this->fillable[] = 'metadata';
        $this->casts['metadata'] = 'array';
    }

    /**
     * Scope a query to only include users of a given type.
     */
    public function scopeWhereMetadata(Builder $query, string $key, $value): void
    {
        $key = Str::contains($key, '.') ? Str::replace('.', '->', $key) : $key;
        $query->where("metadata->$key", $value);
    }
}