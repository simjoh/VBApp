<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Models\Event;
use App\Models\RouteDetail;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration fills the route_details table with data from events.
     * Height differences are estimated based on the event distance.
     * Handles both BRM and MSR event types.
     */
    public function up(): void
    {
        // Get all BRM and MSR events from 2024
        $events = Event::whereIn('event_type', ['BRM', 'MSR'])
            ->whereYear('startdate', '2024')
            ->get();

        foreach ($events as $event) {
            // Extract distance from event title
            $distance = $this->extractDistanceFromTitle($event->title);

            if ($distance) {
                // Estimate height difference based on distance and event type
                $heightDifference = $this->estimateHeightDifference($distance, $event->event_type);

                // Extract start place and time from description
                $startPlace = $this->extractStartPlace($event->description);
                $startTime = $this->extractStartTime($event->description);

                // Clean up description by removing start place and time information
                $cleanDescription = $this->cleanDescription($event->description);

                // Create route detail record using Eloquent ORM
                $routeDetail = new RouteDetail();
                $routeDetail->event_uid = $event->event_uid;
                $routeDetail->distance = $distance;
                $routeDetail->height_difference = $heightDifference;
                $routeDetail->start_time = $startTime;
                $routeDetail->start_place = $startPlace;
                $routeDetail->name = $event->title;
                $routeDetail->description = $cleanDescription;

                // Add track link based on event type and distance
                $routeDetail->track_link = $this->generateTrackLink($event->event_type, $distance, $startPlace);

                $routeDetail->save();

                // Log the creation
                echo "Created route detail for {$event->event_type} event: {$event->title}, distance: {$distance}km, height difference: {$heightDifference}m\n";
                echo "Start place: {$startPlace}, Start time: {$startTime}\n";
            } else {
                echo "Could not extract distance from event title: {$event->title}\n";
            }
        }
    }

    /**
     * Extract distance from event title.
     *
     * @param string $title Event title
     * @return float|null Distance in kilometers or null if not found
     */
    private function extractDistanceFromTitle($title)
    {
        // For MSR events, always return 1200km
        if (stripos($title, 'MSR') !== false) {
            return 1200.0;
        }

        // Look for patterns like "BRM 200K", "BRM 300", "400K", etc.
        if (preg_match('/\b(\d+)K?\b/i', $title, $matches)) {
            return (float) $matches[1];
        }

        return null;
    }

    /**
     * Extract start place from event description.
     *
     * @param string $description Event description
     * @return string|null Start place or null if not found
     */
    private function extractStartPlace($description)
    {
        // Look for patterns like "Startplats: Broparken, Umeå" or "Startplats: ICA KLOCKARETORPET"
        if (preg_match('/Startplats:\s*([^\.]+?)(?:,|\.|Starttid)/i', $description, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }

    /**
     * Extract start time from event description.
     *
     * @param string $description Event description
     * @return string Start time in HH:MM format or default 08:00
     */
    private function extractStartTime($description)
    {
        // Look for patterns like "Starttid: 08:00" or "Starttid: 11:00."
        if (preg_match('/Starttid:\s*(\d{1,2}:\d{2})/i', $description, $matches)) {
            // Format as HH:MM
            $time = explode(':', $matches[1]);
            return sprintf('%02d:%02d', (int)$time[0], (int)$time[1]);
        }

        return '08:00'; // Default start time
    }

    /**
     * Clean description by removing start place and time information.
     *
     * @param string $description Original event description
     * @return string Cleaned description
     */
    private function cleanDescription($description)
    {
        // Remove "Startplats: ..." and "Starttid: ..." patterns
        $cleaned = preg_replace('/Startplats:\s*[^\.]+?(?=\.|$)/i', '', $description);
        $cleaned = preg_replace('/Starttid:\s*\d{1,2}:\d{2}\.?\s*/i', '', $cleaned);

        // Clean up any double spaces or periods
        $cleaned = preg_replace('/\s+/', ' ', $cleaned);
        $cleaned = preg_replace('/\.+/', '.', $cleaned);
        $cleaned = trim($cleaned);

        return $cleaned;
    }

    /**
     * Estimate height difference based on distance and event type.
     *
     * @param float $distance Distance in kilometers
     * @param string $eventType Event type (BRM or MSR)
     * @return float Estimated height difference in meters
     */
    private function estimateHeightDifference($distance, $eventType = 'BRM')
    {
        // For MSR events, always return 11000m height difference
        if ($eventType === 'MSR') {
            return 11000.0;
        }

        // Estimate height difference based on distance categories for BRM events
        if ($distance <= 200) {
            return $distance * 5; // Approximately 1000m for a 200km BRM route
        } elseif ($distance <= 300) {
            return $distance * 6; // Approximately 1800m for a 300km BRM route
        } elseif ($distance <= 400) {
            return $distance * 7; // Approximately 2800m for a 400km BRM route
        } elseif ($distance <= 600) {
            return $distance * 8; // Approximately 4800m for a 600km BRM route
        } else {
            return $distance * 9; // For longer routes like 1000km
        }
    }

    /**
     * Generate a track link based on event type and distance.
     * This is a placeholder that generates example Strava or Komoot links.
     * Only generates links in non-production environment.
     * Only adds links to 75% of events.
     *
     * @param string $eventType Event type (BRM or MSR)
     * @param float $distance Distance in kilometers
     * @param string|null $startPlace Start place
     * @return string|null Track link URL or null
     */
    private function generateTrackLink($eventType, $distance, $startPlace = null)
    {
        // Only generate links in non-production environment
        if (\Illuminate\Support\Facades\App::isProduction()) {
            return null;
        }

        // Only add links to 75% of events (3/4)
        if (rand(1, 4) === 1) {
            return null; // Skip 25% of events
        }

        // For demonstration purposes, we'll generate example links
        // In a real application, you would have actual track links

        $platform = rand(0, 1) == 0 ? 'strava' : 'komoot';
        $locationSlug = $startPlace ? strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $startPlace)) : 'sweden';

        if ($platform === 'strava') {
            // Example Strava route link
            return "https://www.strava.com/routes/example-{$eventType}-{$distance}-{$locationSlug}-" . rand(1000000, 9999999);
        } else {
            // Example Komoot route link
            return "https://www.komoot.com/tour/example-{$eventType}-{$distance}-{$locationSlug}-" . rand(1000000, 9999999);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove all route details for BRM and MSR events from 2024
        $events = Event::whereIn('event_type', ['BRM', 'MSR'])
            ->whereYear('startdate', '2024')
            ->pluck('event_uid');

        // Use Eloquent to delete records
        RouteDetail::whereIn('event_uid', $events)->delete();
    }
};

