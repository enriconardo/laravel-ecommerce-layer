<?php

namespace EcommerceLayer\ModelBuilders;

use EcommerceLayer\Enums\PlanInterval;
use EcommerceLayer\Models\Plan;
use EcommerceLayer\Models\Price;
use Illuminate\Support\Arr;
use Exception;

class PriceBuilder extends BaseBuilder
{
    public static function getModelClass(): string
    {
        return Price::class;
    }

    /**
     * @param array $attributes
     * @return $this
     * @throws Exception
     */
    public function fill(array $attributes)
    {
        try {
            $recurring = Arr::get($attributes, 'recurring', null);
            if ($recurring) {
                $interval = PlanInterval::from(Arr::get($attributes, 'plan.interval'));
                $plan = new Plan($interval, Arr::get($attributes, 'plan.interval_count'));
                $attributes['plan'] = $plan;
            }
        } catch (Exception $e) {
            $this->abort();
            throw $e;
        }

        return parent::fill($attributes);
    }
}
