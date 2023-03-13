<?php

namespace EcommerceLayer\Traits;

use Illuminate\Database\Eloquent\Builder;

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
        $query->where("metadata->$key", $value);
    }
}