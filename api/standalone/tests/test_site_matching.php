<?php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../src/common/Gpx/GpxService.php';

use App\common\Gpx\GpxService;

// Test waypoints (simulating GPX waypoints)
$testWaypoints = [
    [
        'name' => 'Stockholm Central',
        'lat' => 59.3293,
        'lon' => 18.0686,
        'desc' => 'Central Station Stockholm'
    ],
    [
        'name' => 'Göteborg Central', 
        'lat' => 57.7089,
        'lon' => 11.9746,
        'desc' => 'Central Station Gothenburg'
    ],
    [
        'name' => 'Malmö Central',
        'lat' => 55.6095,
        'lon' => 13.0038,
        'desc' => 'Central Station Malmö'
    ],
    [
        'name' => 'Unknown Location',
        'lat' => 60.0000,
        'lon' => 15.0000,
        'desc' => 'This should not match any existing site'
    ]
];

// Demo sites (simulating sites from database)
$demoSites = [
    [
        'site_uid' => 'demo-site-1',
        'place' => 'Stockholm',
        'adress' => 'Centralen',
        'description' => 'Central Station Stockholm',
        'lat' => 59.3293,
        'lng' => 18.0686,
        'check_in_distance' => 0.90
    ],
    [
        'site_uid' => 'demo-site-2', 
        'place' => 'Göteborg',
        'adress' => 'Centralstationen',
        'description' => 'Central Station Gothenburg',
        'lat' => 57.7089,
        'lng' => 11.9746,
        'check_in_distance' => 0.90
    ],
    [
        'site_uid' => 'demo-site-3',
        'place' => 'Malmö',
        'adress' => 'Centralstationen', 
        'description' => 'Central Station Malmö',
        'lat' => 55.6095,
        'lng' => 13.0038,
        'check_in_distance' => 0.90
    ]
];

/**
 * Haversine formula to calculate distance between two lat/lon points (in km).
 */
function haversine($lat1, $lon1, $lat2, $lon2): float
{
    $R = 6371; // Earth radius in km
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    return $R * $c;
}

/**
 * Find the nearest site to a given waypoint within a maximum distance threshold.
 */
function findNearestSite($waypoint, $sites, $maxDistance = 1.0): ?array
{
    $nearestSite = null;
    $minDistance = INF;
    
    foreach ($sites as $site) {
        $distance = haversine(
            $waypoint['lat'], 
            $waypoint['lon'], 
            $site['lat'], 
            $site['lng']
        );
        
        if ($distance < $minDistance && $distance <= $maxDistance) {
            $minDistance = $distance;
            $nearestSite = [
                'site' => $site,
                'distance' => $distance
            ];
        }
    }
    
    return $nearestSite;
}

echo "=== Site Matching Test ===\n\n";

echo "Using " . count($demoSites) . " demo sites for testing\n\n";

// Test matching each waypoint to sites
foreach ($testWaypoints as $i => $waypoint) {
    echo "Waypoint " . ($i + 1) . ": " . $waypoint['name'] . "\n";
    echo "  Coordinates: " . $waypoint['lat'] . ", " . $waypoint['lon'] . "\n";
    echo "  Description: " . $waypoint['desc'] . "\n";
    
    $nearestSite = findNearestSite($waypoint, $demoSites, 1.0); // 1 km threshold
    
    if ($nearestSite) {
        echo "  ✓ MATCHED to: " . $nearestSite['site']['place'] . "\n";
        echo "    Distance: " . round($nearestSite['distance'], 3) . " km\n";
        echo "    Site UID: " . $nearestSite['site']['site_uid'] . "\n";
        echo "    Address: " . $nearestSite['site']['adress'] . "\n";
    } else {
        echo "  ✗ NO MATCH found (within 1 km threshold)\n";
        echo "    Would need to create new site\n";
    }
    echo "\n";
}

// Test with different distance thresholds
echo "=== Testing Different Distance Thresholds ===\n\n";

$testWaypoint = $testWaypoints[3]; // Unknown Location
echo "Testing waypoint: " . $testWaypoint['name'] . "\n";

$thresholds = [0.1, 0.5, 1.0, 5.0, 10.0];
foreach ($thresholds as $threshold) {
    $nearestSite = findNearestSite($testWaypoint, $demoSites, $threshold);
    if ($nearestSite) {
        echo "  Threshold " . $threshold . " km: ✓ MATCHED to " . $nearestSite['site']['place'] . " (" . round($nearestSite['distance'], 3) . " km)\n";
    } else {
        echo "  Threshold " . $threshold . " km: ✗ NO MATCH\n";
    }
}

echo "\n=== Site Matching Summary ===\n";
echo "This test demonstrates how to:\n";
echo "1. Load existing sites from the database\n";
echo "2. Calculate distances between waypoints and sites using Haversine formula\n";
echo "3. Match waypoints to existing sites within a distance threshold\n";
echo "4. Handle cases where no match is found (new site creation needed)\n";
echo "5. Test different distance thresholds for matching sensitivity\n\n";

echo "In a real implementation, you would:\n";
echo "- Use the matched site_uid for existing sites\n";
echo "- Create new sites for unmatched waypoints\n";
echo "- Consider using the site's check_in_distance as the matching threshold\n";
echo "- Add fuzzy matching for place names as a fallback\n";

echo "\n=== Integration with GpxService ===\n";
echo "To integrate this with your GpxService, you would:\n";
echo "1. Add a method to GpxService like 'matchWaypointToSite()'\n";
echo "2. Modify gpxToCheckpoints() to use existing site_uid when a match is found\n";
echo "3. Only create new SiteRepresentation objects for unmatched waypoints\n";
echo "4. Consider adding a 'createSiteFromWaypoint()' method for new sites\n"; 