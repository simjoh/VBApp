<?php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../src/common/Brevetcalculator/ACPBrevetCalculator.php';

use App\common\Brevetcalculator\ACPBrevetCalculator;

$startTime = '2024-07-01 07:00:00';
$brevetDistance = 600;
$calculator = new ACPBrevetCalculator($brevetDistance, $startTime);

$controls = [0, 50, 200, 400, 600];
echo "Testing ACPBrevetCalculator for a 600km brevet starting at $startTime\n\n";
foreach ($controls as $distance) {
    $times = $calculator->getControlTimes($distance);
    echo "Control at {$distance} km:\n";
    echo "  Opens:  " . $times['opening_datetime']->format('Y-m-d H:i:s') . "\n";
    echo "  Closing: " . $times['closing_datetime']->format('Y-m-d H:i:s') . "\n";
    if ($distance === 0) {
        $expected = (new DateTime($startTime))->modify('+1 hour')->format('Y-m-d H:i:s');
        echo "  [Expected closing for 0 km: $expected]";
        if ($times['closing_datetime']->format('Y-m-d H:i:s') === $expected) {
            echo "  [PASS]";
        } else {
            echo "  [FAIL]";
        }
        echo "\n";
    }
    echo "\n";
} 