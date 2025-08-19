<?php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../src/common/Gpx/GpxService.php';

use App\common\Gpx\GpxService;

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

/**
 * Enhanced GPX to Checkpoints with site matching
 */
function gpxToCheckpointsWithSiteMatching($gpx, $sites, $startDateTime, $totalDistance): array
{
    $checkpoints = [];
    $waypoints = $gpx->wpt;
    
    if (count($waypoints) == 0) {
        // Fallback: use first and last trkpt as start/finish
        $trkpts = $gpx->trk->trkseg->trkpt;
        if (count($trkpts) > 0) {
            $waypoints = [$trkpts[0], $trkpts[count($trkpts)-1]];
        }
    }
    
    // Build array of all trkpt for distance calculation
    $trkpts = $gpx->trk->trkseg->trkpt;
    $trkptCoords = [];
    foreach ($trkpts as $pt) {
        $trkptCoords[] = [floatval($pt['lat']), floatval($pt['lon'])];
    }
    
    // Calculate distances for each checkpoint
    foreach ($waypoints as $i => $wpt) {
        $lat = floatval($wpt['lat']);
        $lon = floatval($wpt['lon']);
        $name = (string)($wpt->name ?? ("Control " . ($i+1)));
        $desc = (string)($wpt->desc ?? '');
        
        // Find nearest trkpt to this wpt for distance calculation
        $distance = findDistanceAlongTrack($trkptCoords, $lat, $lon);
        
        // Try to match to existing site
        $waypointData = [
            'lat' => $lat,
            'lon' => $lon,
            'name' => $name,
            'desc' => $desc
        ];
        
        $matchedSite = findNearestSite($waypointData, $sites, 1.0);
        
        // Build SiteRepresentation
        $site = new \App\Domain\Model\Site\Rest\SiteRepresentation();
        
        if ($matchedSite) {
            // Use existing site
            $site->setSiteUid($matchedSite['site']['site_uid']);
            $site->setPlace($matchedSite['site']['place']);
            $site->setAdress($matchedSite['site']['adress']);
            $site->setLat(strval($matchedSite['site']['lat']));
            $site->setLng(strval($matchedSite['site']['lng']));
            $site->setDescription($matchedSite['site']['description']);
            $site->setCheckInDistance(strval($matchedSite['site']['check_in_distance']));
        } else {
            // Create new site representation
            $site->setPlace($name);
            $site->setAdress("");
            $site->setLat(strval($lat));
            $site->setLng(strval($lon));
            $site->setDescription($desc);
            $site->setCheckInDistance("0.90");
        }
        
        // Calculate open/close times if startDateTime is set
        $opens = "";
        $closing = "";
        if ($startDateTime && $totalDistance > 0) {
            $calc = new \App\common\Brevetcalculator\ACPBrevetCalculator($totalDistance, $startDateTime);
            $opens = $calc->getOpeningDateTime($distance)->format('Y-m-d H:i:s');
            $closing = $calc->getClosingDateTime($distance)->format('Y-m-d H:i:s');
        }
        
        // Build CheckpointRepresentation
        $cp = new \App\Domain\Model\CheckPoint\Rest\CheckpointRepresentation();
        $cp->setSite($site);
        $cp->setTitle($name);
        $cp->setDescription($desc);
        $cp->setDistance($distance);
        $cp->setOpens($opens);
        $cp->setClosing($closing);
        
        $checkpoints[] = [
            'checkpoint' => $cp,
            'matched_site' => $matchedSite ? $matchedSite['site'] : null,
            'match_distance' => $matchedSite ? $matchedSite['distance'] : null
        ];
    }
    
    return $checkpoints;
}

/**
 * Find the distance along the track to the closest trkpt to (lat, lon).
 */
function findDistanceAlongTrack(array $trkptCoords, float $lat, float $lon): float
{
    $minDist = INF;
    $closestIdx = 0;
    foreach ($trkptCoords as $i => $pt) {
        $d = haversine($pt[0], $pt[1], $lat, $lon);
        if ($d < $minDist) {
            $minDist = $d;
            $closestIdx = $i;
        }
    }
    // Sum distance up to closestIdx
    $dist = 0.0;
    for ($i = 1; $i <= $closestIdx; $i++) {
        $dist += haversine($trkptCoords[$i-1][0], $trkptCoords[$i-1][1], $trkptCoords[$i][0], $trkptCoords[$i][1]);
    }
    return $dist;
}

echo "=== GPX with Site Matching Test ===\n\n";

// Create a simple GPX structure for testing
$gpxXml = '<?xml version="1.0" encoding="UTF-8"?>
<gpx creator="Test" version="1.1" xmlns="http://www.topografix.com/GPX/1/1">
  <metadata>
    <name>Test Track with Site Matching</name>
  </metadata>
  <wpt lat="59.3293" lon="18.0686">
    <name>Stockholm Central</name>
    <desc>Central Station Stockholm</desc>
  </wpt>
  <wpt lat="57.7089" lon="11.9746">
    <name>Göteborg Central</name>
    <desc>Central Station Gothenburg</desc>
  </wpt>
  <wpt lat="60.0000" lon="15.0000">
    <name>New Location</name>
    <desc>This is a new location not in database</desc>
  </wpt>
  <trk>
    <name>Test Track</name>
    <trkseg>
      <trkpt lat="59.3293" lon="18.0686"></trkpt>
      <trkpt lat="57.7089" lon="11.9746"></trkpt>
      <trkpt lat="60.0000" lon="15.0000"></trkpt>
    </trkseg>
  </trk>
</gpx>';

$gpx = simplexml_load_string($gpxXml);
$gpxService = new GpxService();

if ($gpxService->validateGpx($gpx)) {
    echo "GPX file parsed and validated successfully!\n\n";
    
    $startDateTime = '2024-07-01 07:00:00';
    $totalDistance = 500.0; // Example distance
    
    echo "Processing GPX with site matching...\n";
    echo "Available sites in database: " . count($demoSites) . "\n\n";
    
    $checkpoints = gpxToCheckpointsWithSiteMatching($gpx, $demoSites, $startDateTime, $totalDistance);
    
    foreach ($checkpoints as $i => $checkpointData) {
        $cp = $checkpointData['checkpoint'];
        $matchedSite = $checkpointData['matched_site'];
        $matchDistance = $checkpointData['match_distance'];
        
        echo "Checkpoint " . ($i + 1) . ":\n";
        echo "  Name: " . $cp->getTitle() . "\n";
        echo "  Distance: " . round($cp->getDistance(), 3) . " km\n";
        echo "  Opens: " . $cp->getOpens() . "\n";
        echo "  Closing: " . $cp->getClosing() . "\n";
        
        if ($matchedSite) {
            echo "  ✓ MATCHED to existing site: " . $matchedSite['place'] . "\n";
            echo "    Site UID: " . $matchedSite['site_uid'] . "\n";
            echo "    Match distance: " . round($matchDistance, 3) . " km\n";
        } else {
            echo "  ✗ NO MATCH - would create new site\n";
            echo "    New site name: " . $cp->getSite()->getPlace() . "\n";
            echo "    New site coordinates: " . $cp->getSite()->getLat() . ", " . $cp->getSite()->getLng() . "\n";
        }
        echo "\n";
    }
    
    echo "=== Summary ===\n";
    $matchedCount = 0;
    $newSitesCount = 0;
    
    foreach ($checkpoints as $checkpointData) {
        if ($checkpointData['matched_site']) {
            $matchedCount++;
        } else {
            $newSitesCount++;
        }
    }
    
    echo "Total checkpoints: " . count($checkpoints) . "\n";
    echo "Matched to existing sites: " . $matchedCount . "\n";
    echo "New sites needed: " . $newSitesCount . "\n";
    
} else {
    echo "GPX validation failed!\n";
} 