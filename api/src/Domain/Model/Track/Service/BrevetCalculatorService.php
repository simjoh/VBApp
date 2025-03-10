<?php

namespace App\Domain\Model\Track\Service;

use DateTime;
use App\common\Brevetcalculator\ACPBrevetCalculator;

class BrevetCalculatorService
{
    private ACPBrevetCalculator $calculator;

    /**
     * Create a new BrevetCalculatorService instance
     * 
     * @param float $distance Total brevet distance in kilometers
     * @param string|null $startTime Start time in format 'Y-m-d H:i', defaults to current time
     */
    public function __construct(float $distance, ?string $startTime = null)
    {
        $this->calculator = new ACPBrevetCalculator($distance, $startTime ?? 'now');
    }

    /**
     * Get the overall time limits for the brevet
     * 
     * @return array Associative array containing brevet time limits
     */
    public function getBrevetLimits(): array
    {
        return $this->calculator->getBrevetTimeLimits();
    }

    /**
     * Calculate control times for a specific control point
     * 
     * @param float $controlDistance Distance of the control in kilometers
     * @return array Control opening and closing times
     */
    public function getControlTimes(float $controlDistance): array
    {
        return $this->calculator->getControlTimes($controlDistance);
    }

    /**
     * Calculate times for multiple control points
     * 
     * @param array $controlDistances Array of control distances in kilometers
     * @return array Array of control times for each control point
     */
    public function calculateControlPoints(array $controlDistances): array
    {
        return $this->calculator->calculateControls($controlDistances);
    }

    /**
     * Get minimum completion time in hours
     * 
     * @return float Minimum completion time in hours
     */
    public function getMinimumCompletionTime(): float
    {
        return $this->calculator->getMinimumCompletionTime();
    }

    /**
     * Get maximum completion time in hours
     * 
     * @return float Maximum completion time in hours
     */
    public function getMaximumCompletionTime(): float
    {
        return $this->calculator->getMaximumCompletionTime();
    }

    /**
     * Get the earliest allowed finish datetime
     * 
     * @return DateTime
     */
    public function getMinimumCompletionDateTime(): DateTime
    {
        return $this->calculator->getMinimumCompletionDateTime();
    }

    /**
     * Get the latest allowed finish datetime
     * 
     * @return DateTime
     */
    public function getMaximumCompletionDateTime(): DateTime
    {
        return $this->calculator->getMaximumCompletionDateTime();
    }
} 