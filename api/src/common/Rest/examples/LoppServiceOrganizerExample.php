<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';

use App\common\Rest\DTO\OrganizerDTO;
use App\common\Rest\Client\LoppServiceOrganizerRestClient;

/**
 * Example usage of the LoppServiceOrganizerRestClient with DTOs
 */

// Example settings array (similar to what you might have in your application)
$settings = [
    'loppserviceurl' => 'http://app:80/loppservice',  // URL from settings
    'apikey' => 'your-api-key-here'                   // Replace with your actual API key
];

// Initialize the client with settings
$client = new LoppServiceOrganizerRestClient($settings);

// Example 1: Get all organizers
echo "Example 1: Get all organizers\n";
$organizers = $client->getAllOrganizers();
foreach ($organizers as $organizer) {
    echo "Organizer: {$organizer->organization_name} (ID: {$organizer->id})\n";
}
echo "\n";

// Example 2: Get a specific organizer
echo "Example 2: Get a specific organizer\n";
$organizerId = 1;  // Replace with an actual organizer ID
$organizer = $client->getOrganizerById($organizerId);
if ($organizer) {
    echo "Found organizer: {$organizer->organization_name}\n";
    echo "Description: {$organizer->description}\n";
    echo "Contact person: {$organizer->contact_person_name}\n";
    echo "Email: {$organizer->email}\n";
    echo "Website: {$organizer->website}\n";
    echo "Active: " . ($organizer->active ? 'Yes' : 'No') . "\n";
} else {
    echo "Organizer not found\n";
}
echo "\n";

// Example 3: Create a new organizer
echo "Example 3: Create a new organizer\n";
$newOrganizer = new OrganizerDTO();
$newOrganizer->organization_name = 'New Event Organizer';
$newOrganizer->description = 'A company that organizes cycling events';
$newOrganizer->contact_person_name = 'John Doe';
$newOrganizer->email = 'contact@neworganizer.com';
$newOrganizer->website = 'https://neworganizer.com';
$newOrganizer->active = true;
// Optional: Add an SVG logo
// $newOrganizer->logo_svg = '<svg>...</svg>'; // Raw SVG or base64 encoded

$createdOrganizer = $client->createOrganizer($newOrganizer);
if ($createdOrganizer) {
    echo "Organizer created with ID: {$createdOrganizer->id}\n";
} else {
    echo "Failed to create organizer\n";
}
echo "\n";

// Example 4: Update an organizer
echo "Example 4: Update an organizer\n";
if ($createdOrganizer) {
    $createdOrganizer->description = 'Updated description with more details';
    $createdOrganizer->website = 'https://updated-organizer.com';
    $createdOrganizer->contact_person_name = 'Jane Smith';
    
    $updatedOrganizer = $client->updateOrganizer($createdOrganizer->id, $createdOrganizer);
    if ($updatedOrganizer) {
        echo "Organizer updated successfully\n";
    } else {
        echo "Failed to update organizer\n";
    }
}
echo "\n";

// Example 5: Get events for an organizer
echo "Example 5: Get events for an organizer\n";
if ($createdOrganizer) {
    $events = $client->getOrganizerEvents($createdOrganizer->id);
    if (count($events) > 0) {
        echo "Events organized by {$createdOrganizer->organization_name}:\n";
        foreach ($events as $event) {
            echo "- {$event->name} (UID: {$event->uid})\n";
        }
    } else {
        echo "No events found for this organizer\n";
    }
}
echo "\n";

// Example 6: Asynchronous requests
echo "Example 6: Asynchronous requests\n";
// Create multiple async requests
$promises = [
    'organizer1' => $client->getOrganizerByIdAsync($organizerId),
    'organizers' => $client->getAllOrganizersAsync()
];

// Wait for all requests to complete
$results = $client->awaitAll($promises);

// Access results by key
$organizer1 = $results['organizer1']['value'] ?? null;
$allOrganizers = $results['organizers']['value'] ?? [];

if ($organizer1) {
    echo "Async - Found organizer: {$organizer1->organization_name}\n";
} else {
    echo "Async - Organizer not found\n";
}

echo "Async - Found " . count($allOrganizers) . " organizers\n";
echo "\n";

// Example 7: Delete an organizer
echo "Example 7: Delete an organizer\n";
if ($createdOrganizer) {
    $success = $client->deleteOrganizer($createdOrganizer->id);
    if ($success) {
        echo "Organizer deleted successfully\n";
    } else {
        echo "Failed to delete organizer\n";
    }
}
echo "\n";

echo "Examples completed\n"; 