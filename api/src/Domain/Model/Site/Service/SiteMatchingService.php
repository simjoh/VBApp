<?php

namespace App\Domain\Model\Site\Service;

use App\Domain\Model\Site\Repository\SiteRepository;
use App\Domain\Model\Site\Site;
use App\Domain\Model\Site\Rest\SiteRepresentation;

class SiteMatchingService
{
    private SiteRepository $siteRepository;
    private float $defaultMatchThreshold;

    public function __construct(SiteRepository $siteRepository, float $defaultMatchThreshold = 1.0)
    {
        $this->siteRepository = $siteRepository;
        $this->defaultMatchThreshold = $defaultMatchThreshold;
    }

    /**
     * Find the nearest site to a given waypoint within a maximum distance threshold.
     * 
     * @param float $lat Waypoint latitude
     * @param float $lon Waypoint longitude
     * @param float|null $maxDistance Maximum distance threshold in km (uses default if null)
     * @return array|null Array with 'site' and 'distance' keys, or null if no match found
     */
    public function findNearestSite(float $lat, float $lon, ?float $maxDistance = null): ?array
    {
        $maxDistance = $maxDistance ?? $this->defaultMatchThreshold;
        $sites = $this->siteRepository->allSites();
        
        if (empty($sites)) {
            return null;
        }

        $nearestSite = null;
        $minDistance = INF;
        
        foreach ($sites as $site) {
            $distance = $this->calculateDistance(
                $lat, 
                $lon, 
                floatval(strval($site->getLat())), 
                floatval(strval($site->getLng()))
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
     * Find all sites within a specified radius of a waypoint.
     * 
     * @param float $lat Waypoint latitude
     * @param float $lon Waypoint longitude
     * @param float $radius Radius in km
     * @return array Array of sites with their distances
     */
    public function findSitesWithinRadius(float $lat, float $lon, float $radius): array
    {
        $sites = $this->siteRepository->allSites();
        $matchingSites = [];
        
        foreach ($sites as $site) {
            $distance = $this->calculateDistance(
                $lat, 
                $lon, 
                floatval(strval($site->getLat())), 
                floatval(strval($site->getLng()))
            );
            
            if ($distance <= $radius) {
                $matchingSites[] = [
                    'site' => $site,
                    'distance' => $distance
                ];
            }
        }
        
        // Sort by distance (closest first)
        usort($matchingSites, function($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });
        
        return $matchingSites;
    }

    /**
     * Match a waypoint to an existing site or create a new site representation.
     * 
     * @param float $lat Waypoint latitude
     * @param float $lon Waypoint longitude
     * @param string $name Waypoint name
     * @param string $description Waypoint description
     * @param float|null $maxDistance Maximum distance threshold in km
     * @return array Array with 'siteRepresentation' and 'matchedSite' keys
     */
    public function matchWaypointToSite(
        float $lat, 
        float $lon, 
        string $name, 
        string $description = '', 
        ?float $maxDistance = null
    ): array {
        $matchedSite = $this->findNearestSite($lat, $lon, $maxDistance);
        
        $siteRepresentation = new SiteRepresentation();
        
        if ($matchedSite) {
            // Use existing site
            $site = $matchedSite['site'];
            $siteRepresentation->setSiteUid($site->getSiteUid());
            $siteRepresentation->setPlace($site->getPlace());
            $siteRepresentation->setAdress($site->getAdress());
            $siteRepresentation->setLat(strval($site->getLat()));
            $siteRepresentation->setLng(strval($site->getLng()));
            $siteRepresentation->setDescription($site->getDescription());
            $siteRepresentation->setCheckInDistance(strval($site->getCheckInDistance()));
        } else {
            // Create new site representation
            $siteRepresentation->setPlace($name);
            $siteRepresentation->setAdress("");
            $siteRepresentation->setLat(strval($lat));
            $siteRepresentation->setLng(strval($lon));
            $siteRepresentation->setDescription($description);
            $siteRepresentation->setCheckInDistance("0.90");
        }
        
        return [
            'siteRepresentation' => $siteRepresentation,
            'matchedSite' => $matchedSite ? $matchedSite['site'] : null,
            'matchDistance' => $matchedSite ? $matchedSite['distance'] : null
        ];
    }

    /**
     * Create a new site from waypoint data.
     * 
     * @param float $lat Waypoint latitude
     * @param float $lon Waypoint longitude
     * @param string $name Waypoint name
     * @param string $description Waypoint description
     * @param string $address Waypoint address (optional)
     * @return Site The created site
     */
    public function createSiteFromWaypoint(
        float $lat, 
        float $lon, 
        string $name, 
        string $description = '', 
        string $address = ''
    ): Site {
        $site = new Site(
            '', // site_uid will be generated by repository
            $name,
            $address,
            $description,
            '', // location
            new \PrestaShop\Decimal\DecimalNumber(strval($lat)),
            new \PrestaShop\Decimal\DecimalNumber(strval($lon)),
            '', // picture
            new \PrestaShop\Decimal\DecimalNumber('0.90') // default check_in_distance
        );
        
        return $this->siteRepository->createSite($site);
    }

    /**
     * Calculate distance between two lat/lon points using Haversine formula.
     * 
     * @param float $lat1 First point latitude
     * @param float $lon1 First point longitude
     * @param float $lat2 Second point latitude
     * @param float $lon2 Second point longitude
     * @return float Distance in kilometers
     */
    private function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $R = 6371; // Earth radius in km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $R * $c;
    }

    /**
     * Get the default match threshold.
     * 
     * @return float Default threshold in kilometers
     */
    public function getDefaultMatchThreshold(): float
    {
        return $this->defaultMatchThreshold;
    }

    /**
     * Set the default match threshold.
     * 
     * @param float $threshold Threshold in kilometers
     */
    public function setDefaultMatchThreshold(float $threshold): void
    {
        $this->defaultMatchThreshold = $threshold;
    }
} 