<?php

namespace EnricoNardo\EcommerceLayer\Models;

use EnricoNardo\EcommerceLayer\Enums\PlanInterval;

/**
 * @property PlanInterval $interval The frequency at which a subscription is billed. One of day, week, month or year.
 * @property int $interval_count The number of intervals (specified in the interval attribute) between subscription billings. For example, interval=month and interval_count=3 bills every 3 months.
 */
class Plan
{
    public PlanInterval $interval;

    public int $interval_count;

    public function __construct(PlanInterval $interval, int $interval_count)
    {
        $this->interval = $interval;
        $this->interval_count = $interval_count;
    }
}