<?php


// $calculator = new BrevetCalculator();

// // Calculate times for a single control
// $times = $calculator->calculateControlTimes(175, 200);
// echo "Control at 175km: Opening " . $times['opening'] . ", Closing " . $times['closing'];

// // Calculate times for all controls in a brevet
// $brevetDistance = 600;
// $controls = [60, 120, 200, 350, 550, 600];
// $allTimes = $calculator->calculateAllControlTimes($brevetDistance, $controls);

// foreach ($allTimes as $distance => $times) {
//     echo "Control at {$distance}km: Opening " . $times['opening'] . ", Closing " . $times['closing'] . "\n";
// }

class BrevetCalculator {
    // Control speed limits as per ACP rules
    private $speedLimits = [
        [0, 200, 15, 34],
        [200, 400, 15, 32],
        [400, 600, 15, 30],
        [600, 1000, 11.428, 28],
        [1000, 1300, 13.333, 26]
    ];
    
    // Brevet distance limits and their respective time limits in hours
    private $brevetLimits = [
        200 => 13.5,   // 13H30
        300 => 20,     // 20H00
        400 => 27,     // 27H00
        600 => 40,     // 40H00
        1000 => 75,    // 75H00
        1200 => 90,    // 90H00
        1400 => 116.4  // 116H24
    ];
    
    /**
     * Calculate opening and closing times for a control
     * 
     * @param float $controlDistance Distance of control in kilometers
     * @param float $brevetDistance Official brevet distance in kilometers
     * @return array Opening and closing times in hours and minutes
     */
    public function calculateControlTimes($controlDistance, $brevetDistance) {
        // Truncate to nearest kilometer
        $controlDistance = floor($controlDistance);
        
        // For finish control, use the official brevet distance
        if ($controlDistance >= $brevetDistance) {
            $controlDistance = $this->getOfficialBrevetDistance($brevetDistance);
        }
        
        // Calculate opening time
        $openingTime = $this->calculateOpeningTime($controlDistance);
        
        // Calculate closing time
        $closingTime = $this->calculateClosingTime($controlDistance, $brevetDistance);
        
        // Convert times to hours and minutes format
        $openingTimeFormatted = $this->formatTime($openingTime);
        $closingTimeFormatted = $this->formatTime($closingTime);
        
        return [
            'opening' => $openingTimeFormatted,
            'closing' => $closingTimeFormatted
        ];
    }
    
    /**
     * Calculate opening time for a control based on maximum speeds
     * 
     * @param float $distance Control distance in kilometers
     * @return float Opening time in hours
     */
    private function calculateOpeningTime($distance) {
        $time = 0;
        $remainingDistance = $distance;
        
        foreach ($this->speedLimits as $limit) {
            list($minDist, $maxDist, $minSpeed, $maxSpeed) = $limit;
            
            if ($remainingDistance <= 0) {
                break;
            }
            
            if ($minDist < $remainingDistance) {
                $segmentDistance = min($maxDist, $remainingDistance) - $minDist;
                
                if ($segmentDistance > 0) {
                    $time += $segmentDistance / $maxSpeed;
                    $remainingDistance -= $segmentDistance;
                }
            }
        }
        
        return $time;
    }
    
    /**
     * Calculate closing time for a control based on minimum speeds
     * 
     * @param float $distance Control distance in kilometers
     * @param float $brevetDistance Official brevet distance
     * @return float Closing time in hours
     */
    private function calculateClosingTime($distance, $brevetDistance) {
        // Special handling for controls within first 60km
        if ($distance < 60) {
            // Relaxed control time for first 60km (March 2018 update)
            // Formula: distance/20 + 1 hour
            return $distance / 20 + 1;
        }
        
        $time = 0;
        $remainingDistance = $distance;
        
        foreach ($this->speedLimits as $limit) {
            list($minDist, $maxDist, $minSpeed, $maxSpeed) = $limit;
            
            if ($remainingDistance <= 0) {
                break;
            }
            
            if ($minDist < $remainingDistance) {
                $segmentDistance = min($maxDist, $remainingDistance) - $minDist;
                
                if ($segmentDistance > 0) {
                    $time += $segmentDistance / $minSpeed;
                    $remainingDistance -= $segmentDistance;
                }
            }
        }
        
        // If this is the final control, apply the official brevet time limit
        if ($distance >= $this->getOfficialBrevetDistance($brevetDistance)) {
            $officialDistance = $this->getOfficialBrevetDistance($brevetDistance);
            return $this->getBrevetTimeLimit($officialDistance);
        }
        
        return $time;
    }
    
    /**
     * Get the official brevet distance for time calculations
     * 
     * @param float $actualDistance Actual brevet distance
     * @return float Official distance (200, 300, 400, 600, 1000, 1200, 1400)
     */
    private function getOfficialBrevetDistance($actualDistance) {
        foreach (array_keys($this->brevetLimits) as $distance) {
            if ($actualDistance <= $distance * 1.05) {  // Allow for up to 5% over
                return $distance;
            }
        }
        return $actualDistance;
    }
    
    /**
     * Get the official time limit for a brevet distance
     * 
     * @param float $distance Official brevet distance
     * @return float Time limit in hours
     */
    private function getBrevetTimeLimit($distance) {
        return $this->brevetLimits[$distance] ?? ($distance / 15);  // Default to distance/15 if not in table
    }
    
    /**
     * Convert decimal hours to hours and minutes format
     * 
     * @param float $time Time in decimal hours
     * @return string Time in "HH:MM" format
     */
    private function formatTime($time) {
        $hours = floor($time);
        $minutes = round(($time - $hours) * 60);
        
        // Handle minute overflow
        if ($minutes == 60) {
            $hours++;
            $minutes = 0;
        }
        
        return sprintf("%02d:%02d", $hours, $minutes);
    }
    
    /**
     * Convert miles to kilometers
     * 
     * @param float $miles Distance in miles
     * @return float Distance in kilometers (truncated to nearest km)
     */
    public function milesToKilometers($miles) {
        return floor($miles * 1.609344);
    }
    
    /**
     * Calculate all control times for a brevet
     * 
     * @param float $brevetDistance Official brevet distance
     * @param array $controlDistances Array of control distances
     * @return array Control opening and closing times
     */
    public function calculateAllControlTimes($brevetDistance, $controlDistances) {
        $result = [];
        
        foreach ($controlDistances as $distance) {
            $result[$distance] = $this->calculateControlTimes($distance, $brevetDistance);
        }
        
        return $result;
    }
}
