<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';

use App\common\Rest\DTO\EventDTO;
use App\common\Rest\DTO\RouteDetailsDTO;
use App\common\Rest\DTO\EventConfigurationDTO;
use App\common\Rest\DTO\StartNumberConfigDTO;
use App\common\Rest\DTO\ReservationConfigDTO;
use App\common\Rest\DTO\ProductDTO;
use App\common\Rest\Client\LoppServiceEventRestClient;

/**
 * Example usage of the LoppServiceEventRestClient with DTOs
 */

// Example settings array (similar to what you might have in your application)
$settings = [
    'loppserviceurl' => 'http://app:80/loppservice',  // URL from settings
    'apikey' => 'your-api-key-here'                   // Replace with your actual API key
];

// Initialize the client with settings
$client = new LoppServiceEventRestClient($settings);

// Alternatively, you can initialize with direct URL and API key
// $client = new LoppServiceEventRestClient(
//     'https://loppservice.example.com',  // Replace with your actual LoppService URL
//     'your-api-key-here'                 // Replace with your actual API key
// );

// Example 1: Get all events
echo "Example 1: Get all events\n";
$events = $client->getAllEvents();
foreach ($events as $event) {
    echo "Event: {$event->title} (UID: {$event->event_uid})\n";
}
echo "\n";

// Example 2: Get a specific event
echo "Example 2: Get a specific event\n";
$eventUid = '32f7355a-2bcf-4fb1-8d4f-9c428a48ff78';  // Replace with an actual event UID
$event = $client->getEventById($eventUid);
if ($event) {
    echo "Found event: {$event->title}\n";
    echo "Description: {$event->description}\n";
    echo "Dates: {$event->startdate} to {$event->enddate}\n";
    echo "Event Type: {$event->event_type}\n";
    echo "Organizer ID: {$event->organizer_id}\n";
} else {
    echo "Event not found\n";
}
echo "\n";

// Example 3: Create a new event
echo "Example 3: Create a new event\n";
$newEvent = new EventDTO();
$newEvent->title = 'New Cycling Event';
$newEvent->description = 'A great cycling event';
$newEvent->startdate = '2023-06-15';
$newEvent->enddate = '2023-06-16';
$newEvent->event_type = 'BRM';
$newEvent->organizer_id = 1;
$newEvent->completed = 0;

// Create event configuration
$eventConfig = new EventConfigurationDTO();
$eventConfig->max_registrations = 100;
$eventConfig->registration_opens = '2023-05-01';
$eventConfig->registration_closes = '2023-06-10';
$eventConfig->resarvation_on_event = 1;
$eventConfig->use_stripe_payment = 1;

// Create start number configuration
$startNumberConfig = new StartNumberConfigDTO();
$startNumberConfig->begins_at = 1;
$startNumberConfig->ends_at = 200;
$startNumberConfig->increments = 1;
$eventConfig->startnumberconfig = $startNumberConfig;

// Create reservation configuration
$reservationConfig = new ReservationConfigDTO();
$reservationConfig->duration = 30; // 30 minutes
$eventConfig->reservationconfig = $reservationConfig;

// Add product IDs (instead of ProductDTO objects)
// These should be existing product IDs in the system
$eventConfig->products = [1013, 1014]; // Example product IDs

// Assign configuration to event
$newEvent->eventconfiguration = $eventConfig;

// Create route details
$routeDetails = new RouteDetailsDTO();
$routeDetails->name = 'Scenic Route';
$routeDetails->distance = 200;
$routeDetails->elevation = 2500;
$routeDetails->route_type = 'loop';
$routeDetails->surface_type = 'mixed';

// Assign route details to event
$newEvent->route_detail = $routeDetails;

$createdEvent = $client->createEvent($newEvent);
if ($createdEvent) {
    echo "Event created with UID: {$createdEvent->event_uid}\n";
    
    // Access nested objects
    if ($createdEvent->eventconfiguration) {
        echo "Event configuration: Max registrations: {$createdEvent->eventconfiguration->max_registrations}\n";
        
        if ($createdEvent->eventconfiguration->startnumberconfig) {
            echo "Start numbers: {$createdEvent->eventconfiguration->startnumberconfig->begins_at} to {$createdEvent->eventconfiguration->startnumberconfig->ends_at}\n";
        }
        
        echo "Products: " . count($createdEvent->eventconfiguration->products) . "\n";
        echo "Product IDs: " . implode(', ', $createdEvent->eventconfiguration->products) . "\n";
    }
    
    if ($createdEvent->route_detail) {
        echo "Route: {$createdEvent->route_detail->name}, {$createdEvent->route_detail->distance}km\n";
    }
} else {
    echo "Failed to create event\n";
}
echo "\n";

// Example 4: Update an event
echo "Example 4: Update an event\n";
if ($createdEvent) {
    $createdEvent->title = 'Updated Event Title';
    $createdEvent->description = 'Updated description with more details';
    
    $updatedEvent = $client->updateEvent($createdEvent->event_uid, $createdEvent);
    if ($updatedEvent) {
        echo "Event updated successfully\n";
    } else {
        echo "Failed to update event\n";
    }
}
echo "\n";

// Example 5: Get route details for an event
echo "Example 5: Get route details for an event\n";
if ($createdEvent) {
    $routeDetails = $client->getRouteDetails($createdEvent->event_uid);
    if ($routeDetails) {
        echo "Route details retrieved successfully\n";
        echo "Distance: {$routeDetails->distance} km\n";
        echo "Elevation: {$routeDetails->elevation} m\n";
    } else {
        echo "No route details found or failed to retrieve\n";
    }
}
echo "\n";

// Example 6: Update route details for an event
echo "Example 6: Update route details for an event\n";
if ($createdEvent) {
    $routeDetails = new RouteDetailsDTO();
    $routeDetails->name = 'Mountain Route';
    $routeDetails->distance = 200;
    $routeDetails->elevation = 2500;
    $routeDetails->route_type = 'loop';
    $routeDetails->surface_type = 'mixed';
    $routeDetails->gpx_file = null; // Could be a base64 encoded GPX file
    
    $updatedRouteDetails = $client->updateRouteDetails($createdEvent->event_uid, $routeDetails);
    if ($updatedRouteDetails) {
        echo "Route details updated successfully\n";
    } else {
        echo "Failed to update route details\n";
    }
}
echo "\n";

// Example 7: Asynchronous requests
echo "Example 7: Asynchronous requests\n";
// Create multiple async requests
$promises = [
    'event1' => $client->getEventByIdAsync($eventUid),
    'events' => $client->getAllEventsAsync()
];

// Wait for all requests to complete
$results = $client->awaitAll($promises);

// Access results by key
$event1 = $results['event1']['value'] ?? null;
$allEvents = $results['events']['value'] ?? [];

if ($event1) {
    echo "Async - Found event: {$event1->title}\n";
} else {
    echo "Async - Event not found\n";
}

echo "Async - Found " . count($allEvents) . " events\n";
echo "\n";

// Example 8: Delete an event
echo "Example 8: Delete an event\n";
if ($createdEvent) {
    $success = $client->deleteEvent($createdEvent->event_uid);
    if ($success) {
        echo "Event deleted successfully\n";
    } else {
        echo "Failed to delete event\n";
    }
}
echo "\n";

echo "Examples completed\n"; 