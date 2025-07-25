<?php

namespace App\common\Gpx;

use App\Domain\Model\Track\Rest\TrackRepresentation;
use App\Domain\Model\CheckPoint\Rest\CheckpointRepresentation;
use App\common\Brevetcalculator\ACPBrevetCalculator;
use App\Domain\Model\Site\Rest\SiteRepresentation;

class GpxService
{
    /**
     * Parse a GPX file and return its XML structure.
     * @param string $filepath
     * @return \SimpleXMLElement
     */
    public function parseGpxFile(string $filepath): \SimpleXMLElement
    {
        return simplexml_load_file($filepath);
    }

    /**
     * Validate a GPX file for required structure and compatibility.
     * @param \SimpleXMLElement $gpx
     * @return bool
     */
    public function validateGpx(\SimpleXMLElement $gpx): bool
    {
        // Basic validation: must have <trk> and at least one <trkpt>
        if (!isset($gpx->trk) || !isset($gpx->trk->trkseg) || !isset($gpx->trk->trkseg->trkpt)) {
            return false;
        }
        return true;
    }

    /**
     * Convert a GPX XML structure to a TrackRepresentation object.
     * @param \SimpleXMLElement $gpx
     * @return TrackRepresentation
     */
    public function gpxToTrackRepresentation(\SimpleXMLElement $gpx): TrackRepresentation
    {
        $track = new TrackRepresentation();
        $track->setTitle((string)($gpx->trk->name ?? 'Imported GPX Track'));
        $track->setDescriptions((string)($gpx->trk->desc ?? ''));
        $track->setLinktotrack((string)($gpx->trk->url ?? ''));
        // Calculate total distance from trkpts
        $trkpts = $gpx->trk->trkseg->trkpt;
        $totalDistance = $this->calculateTotalDistance($trkpts);
        $track->setDistance((string)round($totalDistance, 1));
        // Start date/time is not in GPX, must be set by user later
        $track->setStartDateTime("");
        $track->setCheckpoints([]); // To be set after gpxToCheckpoints
        return $track;
    }

    /**
     * Convert GPX waypoints to an array of CheckpointRepresentation objects.
     * @param \SimpleXMLElement $gpx
     * @param string $startDateTime
     * @param float $totalDistance
     * @return CheckpointRepresentation[]
     */
    public function gpxToCheckpoints(\SimpleXMLElement $gpx, string $startDateTime, float $totalDistance): array
    {
        $checkpoints = [];
        // Use <wpt> as controls if present, else use first/last <trkpt>
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
            $lat = (string)$wpt['lat'];
            $lon = (string)$wpt['lon'];
            $name = (string)($wpt->name ?? ("Control " . ($i+1)));
            $desc = (string)($wpt->desc ?? '');
            // Find nearest trkpt to this wpt for distance calculation
            $distance = $this->findDistanceAlongTrack($trkptCoords, floatval($lat), floatval($lon));
            // Build SiteRepresentation
            $site = new SiteRepresentation();
            $site->setPlace($name);
            $site->setAdress("");
            $site->setLat($lat);
            $site->setLng($lon);
            $site->setDescription($desc);
            // Calculate open/close times if startDateTime is set
            $opens = "";
            $closing = "";
            if ($startDateTime && $totalDistance > 0) {
                $calc = new ACPBrevetCalculator($totalDistance, $startDateTime);
                $opens = $calc->getOpeningDateTime($distance)->format('Y-m-d H:i:s');
                $closing = $calc->getClosingDateTime($distance)->format('Y-m-d H:i:s');
            }
            // Build CheckpointRepresentation
            $cp = new CheckpointRepresentation();
            $cp->setSite($site);
            $cp->setTitle($name);
            $cp->setDescription($desc);
            $cp->setDistance($distance);
            $cp->setOpens($opens);
            $cp->setClosing($closing);
            $checkpoints[] = $cp;
        }
        return $checkpoints;
    }

    /**
     * Calculate total distance of a track from trkpt array (in km).
     * @param \SimpleXMLElement[] $trkpts
     * @return float
     */
    private function calculateTotalDistance($trkpts): float
    {
        $total = 0.0;
        $prev = null;
        foreach ($trkpts as $pt) {
            $lat = floatval($pt['lat']);
            $lon = floatval($pt['lon']);
            if ($prev !== null) {
                $total += $this->haversine($prev[0], $prev[1], $lat, $lon);
            }
            $prev = [$lat, $lon];
        }
        return $total;
    }

    /**
     * Find the distance along the track to the closest trkpt to (lat, lon).
     * @param array $trkptCoords
     * @param float $lat
     * @param float $lon
     * @return float
     */
    private function findDistanceAlongTrack(array $trkptCoords, float $lat, float $lon): float
    {
        $minDist = INF;
        $closestIdx = 0;
        foreach ($trkptCoords as $i => $pt) {
            $d = $this->haversine($pt[0], $pt[1], $lat, $lon);
            if ($d < $minDist) {
                $minDist = $d;
                $closestIdx = $i;
            }
        }
        // Sum distance up to closestIdx
        $dist = 0.0;
        for ($i = 1; $i <= $closestIdx; $i++) {
            $dist += $this->haversine($trkptCoords[$i-1][0], $trkptCoords[$i-1][1], $trkptCoords[$i][0], $trkptCoords[$i][1]);
        }
        return $dist;
    }

    /**
     * Haversine formula to calculate distance between two lat/lon points (in km).
     */
    private function haversine($lat1, $lon1, $lat2, $lon2): float
    {
        $R = 6371; // Earth radius in km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $R * $c;
    }
} 