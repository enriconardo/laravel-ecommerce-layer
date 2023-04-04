<?php

namespace EcommerceLayer\Models;

use Carbon\Carbon;
use EcommerceLayer\Enums\PlanInterval;

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

    public function calcExpirationTime(Carbon $startTime = null)
    {
        $date = is_null($startTime) ? Carbon::now() : $startTime;
        
        switch ($this->interval) {
            case PlanInterval::DAY->value:
                $expirationTime = $date->addDays($this->interval_count);
                break;
            case PlanInterval::WEEK->value:
                $expirationTime = $date->addWeeks($this->interval_count);
                break;
            case PlanInterval::MONTH->value:
                $expirationTime = $date->addMonths($this->interval_count);
                break;
            case PlanInterval::YEAR->value:
                $expirationTime = $date->addYears($this->interval_count);
                break;
            default:
                $expirationTime = null;
        }

        return $expirationTime;
    }
}