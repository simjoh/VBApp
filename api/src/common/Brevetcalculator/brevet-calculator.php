<?php

/**
 * ACPBrevetCalculator
 * 
 * This class calculates the opening and closing times for controls
 * in an ACP (Audax Club Parisien) brevet based on official rules.
 * 
 * 
 * 
 * 
 * // Create calculator with initial settings
 * $calculator = new ACPBrevetCalculator(600, '2025-03-10 06:00:00');

 * // Get times based on the current start time
 * $controls1 = $calculator->calculateControls([200, 400, 600]);

 * // Override start time for future calculations
 *$calculator->setStartTime('2025-03-15 08:00:00');
 * $controls2 = $calculator->calculateControls([200, 400, 600]);

 * // Use a custom start time for a single calculation without changing the stored time
 * $customStart = new DateTime('2025-03-20 10:00:00');
 * $controls3 = $calculator->calculateControls([200, 400, 600], $customStart);

 * // The calculator's internal start time remains '2025-03-15 08:00:00'
 * echo "Current start time: " . $calculator->getStartTime()->format('Y-m-d H:i') . "\n";
 * 
 * 
 * 
 * 
 */
class ACPBrevetCalculator
{
    /**
     * Speed limits for different segments of a brevet
     * @var array
     */
    private $speedLimits = [
        [
            'start' => 0,
            'end' => 200,
            'min' => 15,
            'max' => 34
        ],
        [
            'start' => 200,
            'end' => 400,
            'min' => 15,
            'max' => 32
        ],
        [
            'start' => 400,
            'end' => 600,
            'min' => 15,
            'max' => 30
        ],
        [
            'start' => 600,
            'end' => 1000,
            'min' => 11.428,
            'max' => 28
        ],
        [
            'start' => 1000,
            'end' => 1300,
            'min' => 13.333,
            'max' => 26
        ]
    ];

    /**
     * Official time limits for standard brevets in hours
     * @var array
     */
    private $officialTimeLimits = [
        200 => 13.5,  // 13h30
        300 => 20,    // 20h00
        400 => 27,    // 27h00
        600 => 40,    // 40h00
        1000 => 75,   // 75h00
        1200 => 90    // 90h00
    ];

    /**
     * Total brevet distance in kilometers
     * @var float
     */
    private $brevetDistance;

    /**
     * Official brevet distance (200, 300, 400, 600, 1000, 1200)
     * @var int
     */
    private $officialDistance;

    /**
     * Start time of the brevet
     * @var DateTime
     */
    private $startTime;

    /**
     * Constructor
     * 
     * @param float $distance Total brevet distance in kilometers
     * @param string $startTime Start time in format 'Y-m-d H:i'
     */
    public function __construct($distance, $startTime = 'now')
    {
        // Convert to kilometers and truncate to nearest kilometer
        $this->brevetDistance = floor($distance);

        // Determine official brevet distance
        $this->setOfficialDistance();

        // Set start time
        $this->setStartTime($startTime);
    }

    /**
     * Set the start time of the brevet
     * 
     * @param string|DateTime $startTime Start time as string or DateTime object
     * @return self Returns $this for method chaining
     */
    public function setStartTime($startTime)
    {
        if ($startTime instanceof DateTime) {
            $this->startTime = clone $startTime;
        } else {
            $this->startTime = new DateTime($startTime);
        }

        return $this;
    }

    /**
     * Get the current start time
     * 
     * @return DateTime Current start time
     */
    public function getStartTime()
    {
        return clone $this->startTime;
    }

    /**
     * Set the total distance of the brevet
     * 
     * @param float $distance Total brevet distance in kilometers
     * @return self Returns $this for method chaining
     */
    public function setDistance($distance)
    {
        $this->brevetDistance = floor($distance);
        $this->setOfficialDistance();

        return $this;
    }

    /**
     * Get the total distance of the brevet
     * 
     * @return float Total brevet distance in kilometers
     */
    public function getDistance()
    {
        return $this->brevetDistance;
    }

    /**
     * Get the official distance of the brevet
     * 
     * @return int Official brevet distance
     */
    public function getOfficialDistance()
    {
        return $this->officialDistance;
    }

    /**
     * Set the official brevet distance based on the actual route length
     */
    private function setOfficialDistance()
    {
        $standardDistances = [200, 300, 400, 600, 1000, 1200];

        foreach ($standardDistances as $distance) {
            if ($this->brevetDistance <= $distance * 1.05) {
                $this->officialDistance = $distance;
                return;
            }
        }

        // If we get here, it's longer than a standard brevet
        $this->officialDistance = $this->brevetDistance;
    }

    /**
     * Calculate opening time for a control
     * 
     * @param float $controlDistance Distance of the control in kilometers
     * @return float Time in hours
     */
    public function calculateOpeningTime($controlDistance)
    {
        // Truncate to nearest kilometer
        $controlDistance = floor($controlDistance);

        // Cap the control distance at the official distance for final control
        if ($controlDistance > $this->officialDistance) {
            $controlDistance = $this->officialDistance;
        }

        $openingTime = 0;
        $remainingDistance = $controlDistance;

        foreach ($this->speedLimits as $limit) {
            if ($remainingDistance <= 0) {
                break;
            }

            $segmentDistance = min($remainingDistance, $limit['end'] - $limit['start']);

            if ($segmentDistance > 0) {
                $openingTime += $segmentDistance / $limit['max'];
                $remainingDistance -= $segmentDistance;
            }
        }

        return $openingTime;
    }

    /**
     * Calculate closing time for a control
     * 
     * @param float $controlDistance Distance of the control in kilometers
     * @return float Time in hours
     */
    public function calculateClosingTime($controlDistance)
    {
        // Truncate to nearest kilometer
        $controlDistance = floor($controlDistance);

        // Special case for controls within 60km from start
        if ($controlDistance < 60) {
            // Relaxed closing times for early controls
            // 60km at 20km/h = 3 hours instead of 4 hours (at 15km/h)
            return $controlDistance / 20;
        }

        // Cap the control distance at the official distance for final control
        if ($controlDistance > $this->officialDistance) {
            $controlDistance = $this->officialDistance;
        }

        $closingTime = 0;
        $remainingDistance = $controlDistance;

        foreach ($this->speedLimits as $limit) {
            if ($remainingDistance <= 0) {
                break;
            }

            $segmentDistance = min($remainingDistance, $limit['end'] - $limit['start']);

            if ($segmentDistance > 0) {
                $closingTime += $segmentDistance / $limit['min'];
                $remainingDistance -= $segmentDistance;
            }
        }

        // Apply official time limits for standard brevets
        if ($controlDistance >= $this->officialDistance && isset($this->officialTimeLimits[$this->officialDistance])) {
            return $this->officialTimeLimits[$this->officialDistance];
        }

        return $closingTime;
    }

    /**
     * Calculate minimum completion time (fastest allowed time)
     * 
     * @return float Time in hours
     */
    public function getMinimumCompletionTime()
    {
        return $this->calculateOpeningTime($this->brevetDistance);
    }

    /**
     * Calculate maximum completion time (time limit)
     * 
     * @return float Time in hours
     */
    public function getMaximumCompletionTime()
    {
        // For standard brevets, use the official time limit
        if (isset($this->officialTimeLimits[$this->officialDistance])) {
            return $this->officialTimeLimits[$this->officialDistance];
        }

        // For non-standard distances, calculate based on minimum speeds
        return $this->calculateClosingTime($this->brevetDistance);
    }

    /**
     * Convert hours to a formatted time string (HH:MM)
     * 
     * @param float $hours Time in hours
     * @return string Formatted time string
     */
    public function formatTime($hours)
    {
        $totalMinutes = round($hours * 60);
        $h = floor($totalMinutes / 60);
        $m = $totalMinutes % 60;

        return sprintf("%dH%02d", $h, $m);
    }

    /**
     * Get control opening datetime
     * 
     * @param float $controlDistance Distance of the control in kilometers
     * @param DateTime|null $customStartTime Optional custom start time
     * @return DateTime Opening datetime
     */
    public function getOpeningDateTime($controlDistance, $customStartTime = null)
    {
        $openingTime = $this->calculateOpeningTime($controlDistance);
        $hours = floor($openingTime);
        $minutes = round(($openingTime - $hours) * 60);

        $startTime = $customStartTime ? clone $customStartTime : clone $this->startTime;
        $startTime->modify("+{$hours} hours");
        $startTime->modify("+{$minutes} minutes");

        return $startTime;
    }

    /**
     * Get control closing datetime
     * 
     * @param float $controlDistance Distance of the control in kilometers
     * @param DateTime|null $customStartTime Optional custom start time
     * @return DateTime Closing datetime
     */
    public function getClosingDateTime($controlDistance, $customStartTime = null)
    {
        $closingTime = $this->calculateClosingTime($controlDistance);
        $hours = floor($closingTime);
        $minutes = round(($closingTime - $hours) * 60);

        $startTime = $customStartTime ? clone $customStartTime : clone $this->startTime;
        $startTime->modify("+{$hours} hours");
        $startTime->modify("+{$minutes} minutes");

        return $startTime;
    }

    /**
     * Get minimum completion datetime (earliest allowed finish)
     * 
     * @param DateTime|null $customStartTime Optional custom start time
     * @return DateTime Minimum completion datetime
     */
    public function getMinimumCompletionDateTime($customStartTime = null)
    {
        $minTime = $this->getMinimumCompletionTime();
        $hours = floor($minTime);
        $minutes = round(($minTime - $hours) * 60);

        $startTime = $customStartTime ? clone $customStartTime : clone $this->startTime;
        $startTime->modify("+{$hours} hours");
        $startTime->modify("+{$minutes} minutes");

        return $startTime;
    }

    /**
     * Get maximum completion datetime (time limit)
     * 
     * @param DateTime|null $customStartTime Optional custom start time
     * @return DateTime Maximum completion datetime
     */
    public function getMaximumCompletionDateTime($customStartTime = null)
    {
        $maxTime = $this->getMaximumCompletionTime();
        $hours = floor($maxTime);
        $minutes = round(($maxTime - $hours) * 60);

        $startTime = $customStartTime ? clone $customStartTime : clone $this->startTime;
        $startTime->modify("+{$hours} hours");
        $startTime->modify("+{$minutes} minutes");

        return $startTime;
    }

    /**
     * Get control times as formatted strings
     * 
     * @param float $controlDistance Distance of the control in kilometers
     * @param DateTime|null $customStartTime Optional custom start time
     * @return array Associative array with opening and closing times
     */
    public function getControlTimes($controlDistance, $customStartTime = null)
    {
        return [
            'distance' => $controlDistance,
            'opening_hours' => $this->calculateOpeningTime($controlDistance),
            'opening_time' => $this->formatTime($this->calculateOpeningTime($controlDistance)),
            'opening_datetime' => $this->getOpeningDateTime($controlDistance, $customStartTime),
            'closing_hours' => $this->calculateClosingTime($controlDistance),
            'closing_time' => $this->formatTime($this->calculateClosingTime($controlDistance)),
            'closing_datetime' => $this->getClosingDateTime($controlDistance, $customStartTime)
        ];
    }

    /**
     * Get brevets's overall time limits
     * 
     * @param DateTime|null $customStartTime Optional custom start time
     * @return array Associative array with min and max completion times
     */
    public function getBrevetTimeLimits($customStartTime = null)
    {
        return [
            'distance' => $this->brevetDistance,
            'official_distance' => $this->officialDistance,
            'min_hours' => $this->getMinimumCompletionTime(),
            'min_time' => $this->formatTime($this->getMinimumCompletionTime()),
            'min_datetime' => $this->getMinimumCompletionDateTime($customStartTime),
            'max_hours' => $this->getMaximumCompletionTime(),
            'max_time' => $this->formatTime($this->getMaximumCompletionTime()),
            'max_datetime' => $this->getMaximumCompletionDateTime($customStartTime)
        ];
    }

    /**
     * Calculate control times for multiple controls
     * 
     * @param array $controlDistances Array of control distances
     * @param DateTime|null $customStartTime Optional custom start time
     * @return array Array of control times
     */
    public function calculateControls(array $controlDistances, $customStartTime = null)
    {
        $results = [];

        foreach ($controlDistances as $distance) {
            $results[] = $this->getControlTimes($distance, $customStartTime);
        }

        return $results;
    }
}
