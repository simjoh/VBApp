<?php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../src/common/Gpx/GpxService.php';

use App\common\Gpx\GpxService;

// Path to the synthetic GPX file
$gpxFile = __DIR__ . '/syntethic_brevet_test.gpx';

if (!file_exists($gpxFile)) {
    echo "GPX file not found: $gpxFile\n";
    exit(1);
}

$gpxService = new GpxService();

try {
    $gpx = $gpxService->parseGpxFile($gpxFile);
    if ($gpxService->validateGpx($gpx)) {
        echo "GPX file parsed and validated successfully!\n";
        echo "Track name: " . ($gpx->trk->name ?? 'N/A') . "\n";
        echo "Waypoints: " . count($gpx->wpt) . "\n";
        $totalDistance = (float)($gpxService->gpxToTrackRepresentation($gpx)->getDistance());
        $startDateTime = '2024-07-01 07:00:00';
        $checkpoints = $gpxService->gpxToCheckpoints($gpx, $startDateTime, $totalDistance);
        echo "\n--- Output from gpxToCheckpoints (ACP Brevet controls) ---\n";
        foreach ($checkpoints as $i => $cp) {
            $site = $cp->getSite();
            echo "Checkpoint " . ($i+1) . ":\n";
            echo "  Name: " . $cp->getTitle() . "\n";
            echo "  Lat:  " . $site->getLat() . "\n";
            echo "  Lon:  " . $site->getLng() . "\n";
            echo "  Desc: " . $cp->getDescription() . "\n";
            echo "  Distance: " . $cp->getDistance() . " km\n";
            echo "  Opens: " . $cp->getOpens() . "\n";
            echo "  Closing: " . $cp->getClosing() . "\n";
        }
    } else {
        echo "GPX file is invalid.\n";
    }
} catch (Exception $e) {
    echo "Error parsing GPX file: " . $e->getMessage() . "\n";
    exit(1);
} 