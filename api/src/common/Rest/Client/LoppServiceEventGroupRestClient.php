<?php

namespace App\common\Rest\Client;

use App\common\Rest\DTO\EventGroupDTO;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise;
use Psr\Http\Message\ResponseInterface;

/**
 * REST client for the LoppService Event Group API
 * 
 * This client provides methods to interact with the LoppService Event Group API,
 * including retrieving, creating, updating, and deleting event groups.
 * 
 * @package App\common\Rest\Client
 */
class LoppServiceEventGroupRestClient
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
     * $client = new LoppServiceEventGroupRestClient($settings);
     * ```
     * 
     * Usage with direct URL and API key:
     * ```php
     * $client = new LoppServiceEventGroupRestClient(
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
                'apikey' => $this->apiKey,
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'GET, POST, PUT, PATCH, DELETE, OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type, Accept, Authorization, apikey'
            ],
            'http_errors' => true,
            'verify' => false  // Only if you're dealing with self-signed certificates in development
        ]);
    }

    /**
     * Get all event groups
     * 
     * @return EventGroupDTO[] Array of EventGroupDTO objects
     * 
     * Usage:
     * ```php
     * $eventGroups = $client->getAllEventGroups();
     * foreach ($eventGroups as $eventGroup) {
     *     echo $eventGroup->name . "\n";
     * }
     * ```
     */
    public function getAllEventGroups(): array
    {
        try {
            $response = $this->client->request('GET', $this->baseUrl . '/api/integration/event-group/all');
            $data = json_decode($response->getBody()->getContents(), true);
            return EventGroupDTO::fromCollection($data['data'] ?? []);
        } catch (RequestException $e) {
            $this->handleException($e);
            return [];
        }
    }

    /**
     * Get all event groups asynchronously
     * 
     * @return Promise\PromiseInterface A promise that resolves to an array of EventGroupDTO objects
     * 
     * Usage:
     * ```php
     * $promise = $client->getAllEventGroupsAsync();
     * $promise->then(
     *     function (array $eventGroups) {
     *         foreach ($eventGroups as $eventGroup) {
     *             echo $eventGroup->name . "\n";
     *         }
     *     },
     *     function (RequestException $e) {
     *         echo "Error: " . $e->getMessage() . "\n";
     *     }
     * );
     * ```
     */
    public function getAllEventGroupsAsync(): Promise\PromiseInterface
    {
        $promise = $this->client->requestAsync('GET', $this->baseUrl . '/api/integration/event-group/all');
        
        return $promise->then(
            function (ResponseInterface $response) {
                $data = json_decode($response->getBody()->getContents(), true);
                return EventGroupDTO::fromCollection($data['data'] ?? []);
            },
            function (RequestException $e) {
                $this->handleException($e);
                return [];
            }
        );
    }

    /**
     * Get an event group by its UID
     * 
     * @param string $uid The event group UID
     * @return EventGroupDTO|null The event group data or null if not found
     * 
     * Usage:
     * ```php
     * $uid = '12345-abcde-67890';
     * $eventGroup = $client->getEventGroupById($uid);
     * if ($eventGroup) {
     *     echo "Event group name: " . $eventGroup->name . "\n";
     * } else {
     *     echo "Event group not found\n";
     * }
     * ```
     */
    public function getEventGroupById(string $uid): ?EventGroupDTO
    {
        try {
            $response = $this->client->request('GET', $this->baseUrl . '/api/integration/event-group/' . $uid);
            $data = json_decode($response->getBody()->getContents(), true);
            return EventGroupDTO::fromArray($data['data'] ?? []);
        } catch (RequestException $e) {
            $this->handleException($e);
            return null;
        }
    }

    /**
     * Get an event group by its UID asynchronously
     * 
     * @param string $uid The event group UID
     * @return Promise\PromiseInterface A promise that resolves to the EventGroupDTO or null
     * 
     * Usage:
     * ```php
     * $uid = '12345-abcde-67890';
     * $promise = $client->getEventGroupByIdAsync($uid);
     * $promise->then(
     *     function (?EventGroupDTO $eventGroup) {
     *         if ($eventGroup) {
     *             echo "Event group name: " . $eventGroup->name . "\n";
     *         } else {
     *             echo "Event group not found\n";
     *         }
     *     },
     *     function (RequestException $e) {
     *         echo "Error: " . $e->getMessage() . "\n";
     *     }
     * );
     * ```
     */
    public function getEventGroupByIdAsync(string $uid): Promise\PromiseInterface
    {
        $promise = $this->client->requestAsync('GET', $this->baseUrl . '/api/integration/event-group/' . $uid);
        
        return $promise->then(
            function (ResponseInterface $response) {
                $data = json_decode($response->getBody()->getContents(), true);
                return EventGroupDTO::fromArray($data['data'] ?? []);
            },
            function (RequestException $e) {
                $this->handleException($e);
                return null;
            }
        );
    }

    /**
     * Create a new event group
     * 
     * @param EventGroupDTO $eventGroup The event group data
     * @return EventGroupDTO|null The created event group data or null if creation failed
     * 
     * Usage:
     * ```php
     * $eventGroup = new EventGroupDTO();
     * $eventGroup->name = 'New Event Group';
     * $eventGroup->description = 'A group of cycling events';
     * $eventGroup->startdate = '2023-06-15';
     * $eventGroup->enddate = '2023-06-16';
     * $eventGroup->event_uids = ['event-uid-1', 'event-uid-2'];
     * 
     * $newEventGroup = $client->createEventGroup($eventGroup);
     * if ($newEventGroup) {
     *     echo "Event group created with UID: " . $newEventGroup->uid . "\n";
     * } else {
     *     echo "Failed to create event group\n";
     * }
     * ```
     */
    public function createEventGroup(EventGroupDTO $eventGroup): ?EventGroupDTO
    {
        try {
            $response = $this->client->request('POST', $this->baseUrl . '/api/integration/event-group', [
                'json' => $eventGroup->toArray()
            ]);
            $data = json_decode($response->getBody()->getContents(), true);
            return EventGroupDTO::fromArray($data['data'] ?? []);
        } catch (RequestException $e) {
            $this->handleException($e);
            return null;
        }
    }

    /**
     * Create a new event group asynchronously
     * 
     * @param EventGroupDTO $eventGroup The event group data
     * @return Promise\PromiseInterface A promise that resolves to the created EventGroupDTO or null
     * 
     * Usage:
     * ```php
     * $eventGroup = new EventGroupDTO();
     * $eventGroup->name = 'New Event Group';
     * $eventGroup->description = 'A group of cycling events';
     * $eventGroup->startdate = '2023-06-15';
     * $eventGroup->enddate = '2023-06-16';
     * $eventGroup->event_uids = ['event-uid-1', 'event-uid-2'];
     * 
     * $promise = $client->createEventGroupAsync($eventGroup);
     * $promise->then(
     *     function (?EventGroupDTO $newEventGroup) {
     *         if ($newEventGroup) {
     *             echo "Event group created with UID: " . $newEventGroup->uid . "\n";
     *         } else {
     *             echo "Failed to create event group\n";
     *         }
     *     },
     *     function (RequestException $e) {
     *         echo "Error: " . $e->getMessage() . "\n";
     *     }
     * );
     * ```
     */
    public function createEventGroupAsync(EventGroupDTO $eventGroup): Promise\PromiseInterface
    {
        $promise = $this->client->requestAsync('POST', $this->baseUrl . '/api/integration/event-group', [
            'json' => $eventGroup->toArray()
        ]);
        
        return $promise->then(
            function (ResponseInterface $response) {
                $data = json_decode($response->getBody()->getContents(), true);
                return EventGroupDTO::fromArray($data['data'] ?? []);
            },
            function (RequestException $e) {
                $this->handleException($e);
                return null;
            }
        );
    }

    /**
     * Update an existing event group
     * 
     * @param string $uid The event group UID
     * @param EventGroupDTO $eventGroup The updated event group data
     * @return EventGroupDTO|null The updated event group data or null if update failed
     * 
     * Usage:
     * ```php
     * $uid = '12345-abcde-67890';
     * $eventGroup = $client->getEventGroupById($uid);
     * if ($eventGroup) {
     *     $eventGroup->name = 'Updated Event Group Name';
     *     $eventGroup->event_uids = ['event-uid-1', 'event-uid-3']; // Update associated events
     *     
     *     $updatedEventGroup = $client->updateEventGroup($uid, $eventGroup);
     *     if ($updatedEventGroup) {
     *         echo "Event group updated successfully\n";
     *     } else {
     *         echo "Failed to update event group\n";
     *     }
     * }
     * ```
     */
    public function updateEventGroup(string $uid, EventGroupDTO $eventGroup): ?EventGroupDTO
    {
        try {
            // Ensure the UID is set in the DTO
            $eventGroup->uid = $uid;
            
            // Debug log
            error_log("Sending update request to LoppService: " . json_encode([
                'url' => $this->baseUrl . '/api/integration/event-group/' . $uid,
                'data' => $eventGroup->toArray()
            ]));
            
            $response = $this->client->request('PUT', $this->baseUrl . '/api/integration/event-group/' . $uid, [
                'json' => $eventGroup->toArray(),
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'apikey' => $this->apiKey
                ]
            ]);
            
            $data = json_decode($response->getBody()->getContents(), true);
            return EventGroupDTO::fromArray($data['data'] ?? []);
        } catch (RequestException $e) {
            $this->handleException($e);
            return null;
        }
    }

    /**
     * Update an existing event group asynchronously
     * 
     * @param string $uid The event group UID
     * @param EventGroupDTO $eventGroup The updated event group data
     * @return Promise\PromiseInterface A promise that resolves to the updated EventGroupDTO or null
     * 
     * Usage:
     * ```php
     * $uid = '12345-abcde-67890';
     * $eventGroup = $client->getEventGroupById($uid);
     * if ($eventGroup) {
     *     $eventGroup->name = 'Updated Event Group Name';
     *     
     *     $promise = $client->updateEventGroupAsync($uid, $eventGroup);
     *     $promise->then(
     *         function (?EventGroupDTO $updatedEventGroup) {
     *             if ($updatedEventGroup) {
     *                 echo "Event group updated successfully\n";
     *             } else {
     *                 echo "Failed to update event group\n";
     *             }
     *         },
     *         function (RequestException $e) {
     *             echo "Error: " . $e->getMessage() . "\n";
     *         }
     *     );
     * }
     * ```
     */
    public function updateEventGroupAsync(string $uid, EventGroupDTO $eventGroup): Promise\PromiseInterface
    {
        // Ensure the UID is set in the DTO
        $eventGroup->uid = $uid;
        
        $promise = $this->client->requestAsync('PUT', $this->baseUrl . '/api/integration/event-group/' . $uid, [
            'json' => $eventGroup->toArray(),
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'apikey' => $this->apiKey
            ]
        ]);
        
        return $promise->then(
            function (ResponseInterface $response) {
                $data = json_decode($response->getBody()->getContents(), true);
                return EventGroupDTO::fromArray($data['data'] ?? []);
            },
            function (RequestException $e) {
                $this->handleException($e);
                return null;
            }
        );
    }

    /**
     * Delete an event group
     * 
     * @param string $uid The event group UID
     * @return bool True if deletion was successful, false otherwise
     * 
     * Usage:
     * ```php
     * $uid = '12345-abcde-67890';
     * if ($client->deleteEventGroup($uid)) {
     *     echo "Event group deleted successfully\n";
     * } else {
     *     echo "Failed to delete event group\n";
     * }
     * ```
     */
    public function deleteEventGroup(string $uid): bool
    {
        try {
            $response = $this->client->request('DELETE', $this->baseUrl . '/api/integration/event-group/' . $uid);
            return $response->getStatusCode() >= 200 && $response->getStatusCode() < 300;
        } catch (RequestException $e) {
            $this->handleException($e);
            return false;
        }
    }

    /**
     * Delete an event group asynchronously
     * 
     * @param string $uid The event group UID
     * @return Promise\PromiseInterface A promise that resolves to a boolean indicating success
     * 
     * Usage:
     * ```php
     * $uid = '12345-abcde-67890';
     * $promise = $client->deleteEventGroupAsync($uid);
     * $promise->then(
     *     function (bool $success) {
     *         if ($success) {
     *             echo "Event group deleted successfully\n";
     *         } else {
     *             echo "Failed to delete event group\n";
     *         }
     *     },
     *     function (RequestException $e) {
     *         echo "Error: " . $e->getMessage() . "\n";
     *     }
     * );
     * ```
     */
    public function deleteEventGroupAsync(string $uid): Promise\PromiseInterface
    {
        $promise = $this->client->requestAsync('DELETE', $this->baseUrl . '/api/integration/event-group/' . $uid);
        
        return $promise->then(
            function (ResponseInterface $response) {
                return $response->getStatusCode() >= 200 && $response->getStatusCode() < 300;
            },
            function (RequestException $e) {
                $this->handleException($e);
                return false;
            }
        );
    }

    /**
     * Wait for all promises to complete
     * 
     * @param array $promises Array of promises
     * @return array Array of results
     */
    public function awaitAll(array $promises): array
    {
        return Promise\Utils::unwrap($promises);
    }

    /**
     * Handle exceptions from the API
     * 
     * @param RequestException $e The exception
     * @throws \Exception Rethrows the exception with a more descriptive message
     */
    private function handleException(RequestException $e): void
    {
        $message = $e->getMessage();
        
        if ($e->hasResponse()) {
            $response = $e->getResponse();
            $body = json_decode($response->getBody()->getContents(), true);
            $message = $body['message'] ?? $message;
        }
        
        // Log the error or handle it as needed
        // For now, we'll just rethrow with a more descriptive message
        throw new \Exception("LoppService Event Group API error: " . $message, 0, $e);
    }
} 