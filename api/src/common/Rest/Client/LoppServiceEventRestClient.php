<?php

namespace App\common\Rest\Client;

use App\common\Rest\DTO\EventDTO;
use App\common\Rest\DTO\RouteDetailsDTO;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise;
use Psr\Http\Message\ResponseInterface;

/**
 * REST client for the LoppService Event API
 * 
 * This client provides methods to interact with the LoppService Event API,
 * including retrieving, creating, updating, and deleting events.
 * 
 * @package App\common\Rest\Client
 */
class LoppServiceEventRestClient
{
    private string $baseUrl;
    private Client $client;
    private string $apiKey;

    /**
     * Constructor
     * 
     * @param array|string $settings Either a settings array or the base URL string
     * @param string $apiKey The API key for authentication (only used if first parameter is a string)
     * 
     * Usage with settings array:
     * ```php
     * $settings = [
     *     'loppserviceurl' => 'http://app:80/loppservice',
     *     'apikey' => 'your-api-key-here'
     * ];
     * $client = new LoppServiceEventRestClient($settings);
     * ```
     * 
     * Usage with direct URL and API key:
     * ```php
     * $client = new LoppServiceEventRestClient(
     *     'https://loppservice.example.com',
     *     'your-api-key-here'
     * );
     * ```
     */
    public function __construct($settings = [], string $apiKey = '')
    {
        // Handle both settings array and direct URL string
        if (is_array($settings)) {
            $this->baseUrl = $settings['loppserviceurl'] ?? '';
            $this->apiKey = $settings['apikey'] ?? '';
        } else {
            $this->baseUrl = (string)$settings;
            $this->apiKey = $apiKey;
        }

        // Remove trailing slash from URL if present
        $this->baseUrl = rtrim($this->baseUrl, '/');

        $this->client = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'apikey' => $this->apiKey
            ]
        ]);
    }

    /**
     * Get all events
     * 
     * @return EventDTO[] Array of EventDTO objects
     * 
     * Usage:
     * ```php
     * $events = $client->getAllEvents();
     * foreach ($events as $event) {
     *     echo $event->title . "\n";
     * }
     * ```
     */
    public function getAllEvents(): array
    {
        try {
            $response = $this->client->request('GET', $this->baseUrl . '/api/integration/event/all');
            $data = json_decode($response->getBody()->getContents(), true);
            return EventDTO::fromCollection($data);
        } catch (RequestException $e) {
            $this->handleException($e);
            return [];
        }
    }

    /**
     * Get all events asynchronously
     * 
     * @return Promise\PromiseInterface A promise that resolves to an array of EventDTO objects
     * 
     * Usage:
     * ```php
     * $promise = $client->getAllEventsAsync();
     * $promise->then(
     *     function (array $events) {
     *         foreach ($events as $event) {
     *             echo $event->title . "\n";
     *         }
     *     },
     *     function (RequestException $e) {
     *         echo "Error: " . $e->getMessage() . "\n";
     *     }
     * );
     * 
     * // Or wait for the result
     * $events = $promise->wait();
     * ```
     */
    public function getAllEventsAsync(): Promise\PromiseInterface
    {
        $promise = $this->client->requestAsync('GET', $this->baseUrl . '/api/integration/event/all');
        
        return $promise->then(
            function (ResponseInterface $response) {
                $data = json_decode($response->getBody()->getContents(), true);
                return EventDTO::fromCollection($data);
            },
            function (RequestException $e) {
                $this->handleException($e);
                return [];
            }
        );
    }

    /**
     * Get an event by its UID
     * 
     * @param string $eventUid The event UID
     * @return EventDTO|null The event data or null if not found
     * 
     * Usage:
     * ```php
     * $eventUid = '12345-abcde-67890';
     * $event = $client->getEventById($eventUid);
     * if ($event) {
     *     echo "Event title: " . $event->title . "\n";
     * } else {
     *     echo "Event not found\n";
     * }
     * ```
     */
    public function getEventById(string $eventUid): ?EventDTO
    {
        try {
            $response = $this->client->request('GET', $this->baseUrl . '/api/integration/event/' . $eventUid);
            $data = json_decode($response->getBody()->getContents(), true);
            return EventDTO::fromArray($data);
        } catch (RequestException $e) {
            $this->handleException($e);
            return null;
        }
    }

    /**
     * Get an event by its UID asynchronously
     * 
     * @param string $eventUid The event UID
     * @return Promise\PromiseInterface A promise that resolves to the EventDTO or null
     * 
     * Usage:
     * ```php
     * $eventUid = '12345-abcde-67890';
     * $promise = $client->getEventByIdAsync($eventUid);
     * $promise->then(
     *     function (?EventDTO $event) {
     *         if ($event) {
     *             echo "Event title: " . $event->title . "\n";
     *         } else {
     *             echo "Event not found\n";
     *         }
     *     },
     *     function (RequestException $e) {
     *         echo "Error: " . $e->getMessage() . "\n";
     *     }
     * );
     * ```
     */
    public function getEventByIdAsync(string $eventUid): Promise\PromiseInterface
    {
        $promise = $this->client->requestAsync('GET', $this->baseUrl . '/api/integration/event/' . $eventUid);
        
        return $promise->then(
            function (ResponseInterface $response) {
                $data = json_decode($response->getBody()->getContents(), true);
                return EventDTO::fromArray($data);
            },
            function (RequestException $e) {
                $this->handleException($e);
                return null;
            }
        );
    }

    /**
     * Create a new event
     * 
     * @param EventDTO $event The event data
     * @return EventDTO|null The created event data or null if creation failed
     * 
     * Usage:
     * ```php
     * $event = new EventDTO();
     * $event->title = 'New Cycling Event';
     * $event->description = 'A great cycling event';
     * $event->startdate = '2023-06-15';
     * $event->enddate = '2023-06-16';
     * $event->event_type = 'BRM';
     * $event->organizer_id = 1;
     * 
     * $newEvent = $client->createEvent($event);
     * if ($newEvent) {
     *     echo "Event created with UID: " . $newEvent->event_uid . "\n";
     * } else {
     *     echo "Failed to create event\n";
     * }
     * ```
     */
    public function createEvent(EventDTO $event): ?EventDTO
    {
        try {
            $response = $this->client->request('POST', $this->baseUrl . '/api/integration/event', [
                'json' => $event
            ]);
            $data = json_decode($response->getBody()->getContents(), true);
            return EventDTO::fromArray($data);
        } catch (RequestException $e) {
            $this->handleException($e);
            
            // Check if it's an "alreadyexists" error and throw a specific exception
            if ($e->hasResponse()) {
                $statusCode = $e->getResponse()->getStatusCode();
                $body = $e->getResponse()->getBody()->getContents();
                
                if ($statusCode === 500 && $body === '"alreadyexists"') {
                    throw new \Exception('alreadyexists');
                }
            }
            
            return null;
        }
    }

    /**
     * Create a new event asynchronously
     * 
     * @param EventDTO $event The event data
     * @return Promise\PromiseInterface A promise that resolves to the created EventDTO or null
     * 
     * Usage:
     * ```php
     * $event = new EventDTO();
     * $event->title = 'New Cycling Event';
     * $event->description = 'A great cycling event';
     * $event->startdate = '2023-06-15';
     * $event->enddate = '2023-06-16';
     * $event->event_type = 'BRM';
     * $event->organizer_id = 1;
     * 
     * $promise = $client->createEventAsync($event);
     * $promise->then(
     *     function (?EventDTO $newEvent) {
     *         if ($newEvent) {
     *             echo "Event created with UID: " . $newEvent->event_uid . "\n";
     *         } else {
     *             echo "Failed to create event\n";
     *         }
     *     },
     *     function (RequestException $e) {
     *         echo "Error: " . $e->getMessage() . "\n";
     *     }
     * );
     * ```
     */
    public function createEventAsync(EventDTO $event): Promise\PromiseInterface
    {
        $promise = $this->client->requestAsync('POST', $this->baseUrl . '/api/integration/event', [
            'json' => $event
        ]);
        
        return $promise->then(
            function (ResponseInterface $response) {
                $data = json_decode($response->getBody()->getContents(), true);
                return EventDTO::fromArray($data);
            },
            function (RequestException $e) {
                $this->handleException($e);
                return null;
            }
        );
    }

    /**
     * Update an existing event
     * 
     * @param string $eventUid The event UID
     * @param EventDTO $event The updated event data
     * @return EventDTO|null The updated event data or null if update failed
     * 
     * Usage:
     * ```php
     * $eventUid = '12345-abcde-67890';
     * 
     * // First get the existing event
     * $event = $client->getEventById($eventUid);
     * 
     * // Update properties
     * $event->title = 'Updated Event Title';
     * $event->description = 'Updated description';
     * 
     * $updatedEvent = $client->updateEvent($eventUid, $event);
     * if ($updatedEvent) {
     *     echo "Event updated successfully\n";
     * } else {
     *     echo "Failed to update event\n";
     * }
     * ```
     */
    public function updateEvent(string $eventUid, EventDTO $event): ?EventDTO
    {
        try {
            $response = $this->client->request('PUT', $this->baseUrl . '/api/integration/event/' . $eventUid, [
                'json' => $event
            ]);
            $data = json_decode($response->getBody()->getContents(), true);
            return EventDTO::fromArray($data);
        } catch (RequestException $e) {
            $this->handleException($e);
            return null;
        }
    }

    /**
     * Update an existing event asynchronously
     * 
     * @param string $eventUid The event UID
     * @param EventDTO $event The updated event data
     * @return Promise\PromiseInterface A promise that resolves to the updated EventDTO or null
     * 
     * Usage:
     * ```php
     * $eventUid = '12345-abcde-67890';
     * 
     * // First get the existing event
     * $event = $client->getEventById($eventUid);
     * 
     * // Update properties
     * $event->title = 'Updated Event Title';
     * $event->description = 'Updated description';
     * 
     * $promise = $client->updateEventAsync($eventUid, $event);
     * $promise->then(
     *     function (?EventDTO $updatedEvent) {
     *         if ($updatedEvent) {
     *             echo "Event updated successfully\n";
     *         } else {
     *             echo "Failed to update event\n";
     *         }
     *     },
     *     function (RequestException $e) {
     *         echo "Error: " . $e->getMessage() . "\n";
     *     }
     * );
     * ```
     */
    public function updateEventAsync(string $eventUid, EventDTO $event): Promise\PromiseInterface
    {
        $promise = $this->client->requestAsync('PUT', $this->baseUrl . '/api/integration/event/' . $eventUid, [
            'json' => $event
        ]);
        
        return $promise->then(
            function (ResponseInterface $response) {
                $data = json_decode($response->getBody()->getContents(), true);
                return EventDTO::fromArray($data);
            },
            function (RequestException $e) {
                $this->handleException($e);
                return null;
            }
        );
    }

    /**
     * Delete an event
     * 
     * @param string $eventUid The event UID
     * @return bool Whether the deletion was successful
     * 
     * Usage:
     * ```php
     * $eventUid = '12345-abcde-67890';
     * $success = $client->deleteEvent($eventUid);
     * if ($success) {
     *     echo "Event deleted successfully\n";
     * } else {
     *     echo "Failed to delete event\n";
     * }
     * ```
     */
    public function deleteEvent(string $eventUid): bool
    {
        try {
            $this->client->request('DELETE', $this->baseUrl . '/api/integration/event/' . $eventUid);
            return true;
        } catch (RequestException $e) {
            $this->handleException($e);
            return false;
        }
    }

    /**
     * Delete an event asynchronously
     * 
     * @param string $eventUid The event UID
     * @return Promise\PromiseInterface A promise that resolves to a boolean indicating success
     * 
     * Usage:
     * ```php
     * $eventUid = '12345-abcde-67890';
     * $promise = $client->deleteEventAsync($eventUid);
     * $promise->then(
     *     function (bool $success) {
     *         if ($success) {
     *             echo "Event deleted successfully\n";
     *         } else {
     *             echo "Failed to delete event\n";
     *         }
     *     },
     *     function (RequestException $e) {
     *         echo "Error: " . $e->getMessage() . "\n";
     *     }
     * );
     * ```
     */
    public function deleteEventAsync(string $eventUid): Promise\PromiseInterface
    {
        $promise = $this->client->requestAsync('DELETE', $this->baseUrl . '/api/integration/event/' . $eventUid);
        
        return $promise->then(
            function (ResponseInterface $response) {
                return true;
            },
            function (RequestException $e) {
                $this->handleException($e);
                return false;
            }
        );
    }

    /**
     * Get route details for an event
     * 
     * @param string $eventUid The event UID
     * @return RouteDetailsDTO|null The route details or null if not found
     * 
     * Usage:
     * ```php
     * $eventUid = '12345-abcde-67890';
     * $routeDetails = $client->getRouteDetails($eventUid);
     * if ($routeDetails) {
     *     echo "Route distance: " . $routeDetails->distance . " km\n";
     *     echo "Elevation: " . $routeDetails->elevation . " m\n";
     * } else {
     *     echo "Route details not found\n";
     * }
     * ```
     */
    public function getRouteDetails(string $eventUid): ?RouteDetailsDTO
    {
        try {
            $response = $this->client->request('GET', $this->baseUrl . '/api/integration/event/event/' . $eventUid . '/route-details');
            $data = json_decode($response->getBody()->getContents(), true);
            return RouteDetailsDTO::fromArray($data);
        } catch (RequestException $e) {
            $this->handleException($e);
            return null;
        }
    }

    /**
     * Get route details for an event asynchronously
     * 
     * @param string $eventUid The event UID
     * @return Promise\PromiseInterface A promise that resolves to a RouteDetailsDTO or null
     * 
     * Usage:
     * ```php
     * $eventUid = '12345-abcde-67890';
     * $promise = $client->getRouteDetailsAsync($eventUid);
     * $promise->then(
     *     function (?RouteDetailsDTO $routeDetails) {
     *         if ($routeDetails) {
     *             echo "Route distance: " . $routeDetails->distance . " km\n";
     *             echo "Elevation: " . $routeDetails->elevation . " m\n";
     *         } else {
     *             echo "Route details not found\n";
     *         }
     *     },
     *     function (RequestException $e) {
     *         echo "Error: " . $e->getMessage() . "\n";
     *     }
     * );
     * ```
     */
    public function getRouteDetailsAsync(string $eventUid): Promise\PromiseInterface
    {
        $promise = $this->client->requestAsync('GET', $this->baseUrl . '/api/integration/event/event/' . $eventUid . '/route-details');
        
        return $promise->then(
            function (ResponseInterface $response) {
                $data = json_decode($response->getBody()->getContents(), true);
                return RouteDetailsDTO::fromArray($data);
            },
            function (RequestException $e) {
                $this->handleException($e);
                return null;
            }
        );
    }

    /**
     * Update route details for an event
     * 
     * @param string $eventUid The event UID
     * @param RouteDetailsDTO $routeDetails The route details data
     * @return RouteDetailsDTO|null The updated route details or null if update failed
     * 
     * Usage:
     * ```php
     * $eventUid = '12345-abcde-67890';
     * 
     * $routeDetails = new RouteDetailsDTO();
     * $routeDetails->event_uid = $eventUid;
     * $routeDetails->distance = 120.5;
     * $routeDetails->elevation_gain = 1500;
     * $routeDetails->route_type = 'loop';
     * $routeDetails->surface_type = 'mixed';
     * $routeDetails->gpx_file_url = 'https://example.com/routes/event123.gpx';
     * 
     * $updatedRoute = $client->updateRouteDetails($eventUid, $routeDetails);
     * if ($updatedRoute) {
     *     echo "Route details updated successfully\n";
     * } else {
     *     echo "Failed to update route details\n";
     * }
     * ```
     */
    public function updateRouteDetails(string $eventUid, RouteDetailsDTO $routeDetails): ?RouteDetailsDTO
    {
        try {
            $response = $this->client->request('POST', $this->baseUrl . '/api/integration/event/event/' . $eventUid . '/route-details', [
                'json' => $routeDetails
            ]);
            $data = json_decode($response->getBody()->getContents(), true);
            return RouteDetailsDTO::fromArray($data);
        } catch (RequestException $e) {
            $this->handleException($e);
            return null;
        }
    }

    /**
     * Update route details for an event asynchronously
     * 
     * @param string $eventUid The event UID
     * @param RouteDetailsDTO $routeDetails The route details data
     * @return Promise\PromiseInterface A promise that resolves to the updated RouteDetailsDTO or null
     * 
     * Usage:
     * ```php
     * $eventUid = '12345-abcde-67890';
     * 
     * $routeDetails = new RouteDetailsDTO();
     * $routeDetails->event_uid = $eventUid;
     * $routeDetails->distance = 120.5;
     * $routeDetails->elevation_gain = 1500;
     * $routeDetails->route_type = 'loop';
     * $routeDetails->surface_type = 'mixed';
     * $routeDetails->gpx_file_url = 'https://example.com/routes/event123.gpx';
     * 
     * $promise = $client->updateRouteDetailsAsync($eventUid, $routeDetails);
     * $promise->then(
     *     function (?RouteDetailsDTO $updatedRoute) {
     *         if ($updatedRoute) {
     *             echo "Route details updated successfully\n";
     *         } else {
     *             echo "Failed to update route details\n";
     *         }
     *     },
     *     function (RequestException $e) {
     *         echo "Error: " . $e->getMessage() . "\n";
     *     }
     * );
     * ```
     */
    public function updateRouteDetailsAsync(string $eventUid, RouteDetailsDTO $routeDetails): Promise\PromiseInterface
    {
        $promise = $this->client->requestAsync('POST', $this->baseUrl . '/api/integration/event/event/' . $eventUid . '/route-details', [
            'json' => $routeDetails
        ]);
        
        return $promise->then(
            function (ResponseInterface $response) {
                $data = json_decode($response->getBody()->getContents(), true);
                return RouteDetailsDTO::fromArray($data);
            },
            function (RequestException $e) {
                $this->handleException($e);
                return null;
            }
        );
    }

    /**
     * Make multiple async requests and wait for all of them to complete
     * 
     * @param array $promises An array of promises
     * @return array The results of all promises
     * 
     * Usage:
     * ```php
     * // Create multiple async requests
     * $promises = [
     *     'event1' => $client->getEventByIdAsync('event-uid-1'),
     *     'event2' => $client->getEventByIdAsync('event-uid-2'),
     *     'events' => $client->getAllEventsAsync()
     * ];
     * 
     * // Wait for all requests to complete
     * $results = $client->awaitAll($promises);
     * 
     * // Access results by key
     * $event1 = $results['event1'];
     * $event2 = $results['event2'];
     * $allEvents = $results['events'];
     * ```
     */
    public function awaitAll(array $promises): array
    {
        return Promise\Utils::settle($promises)->wait();
    }

    /**
     * Handle request exceptions
     * 
     * @param RequestException $e The request exception
     * @return void
     */
    private function handleException(RequestException $e): void
    {
        if ($e->hasResponse()) {
            $statusCode = $e->getResponse()->getStatusCode();
            $body = $e->getResponse()->getBody()->getContents();
            error_log("LoppService API Error ($statusCode): $body");
        } else {
            error_log("LoppService API Error: " . $e->getMessage());
        }
    }
} 