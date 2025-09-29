<?php

namespace App\common\Rest\Client;

use App\common\Rest\DTO\EventDTO;
use App\common\Rest\DTO\OrganizerDTO;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise;
use Psr\Http\Message\ResponseInterface;

/**
 * REST client for the LoppService Organizer API
 * 
 * This client provides methods to interact with the LoppService Organizer API,
 * including retrieving, creating, updating, and deleting organizers.
 * 
 * @package App\common\Rest\Client
 */
class LoppServiceOrganizerRestClient
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
     * $client = new LoppServiceOrganizerRestClient($settings);
     * ```
     * 
     * Usage with direct URL and API key:
     * ```php
     * $client = new LoppServiceOrganizerRestClient(
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
                'User-Agent' => 'Loppservice/1.0'
            ]
        ]);
    }

    /**
     * Get all organizers
     * 
     * @return OrganizerDTO[] Array of OrganizerDTO objects
     * 
     * Usage:
     * ```php
     * $organizers = $client->getAllOrganizers();
     * foreach ($organizers as $organizer) {
     *     echo $organizer->organization_name . "\n";
     * }
     * ```
     */
    public function getAllOrganizers(): array
    {
        try {
            $response = $this->client->request('GET', $this->baseUrl . '/api/integration/organizers');
            $data = json_decode($response->getBody()->getContents(), true);
            return OrganizerDTO::fromCollection($data);
        } catch (RequestException $e) {
            $this->handleException($e);
            return [];
        }
    }

    /**
     * Get all organizers asynchronously
     * 
     * @return Promise\PromiseInterface A promise that resolves to an array of OrganizerDTO objects
     * 
     * Usage:
     * ```php
     * $promise = $client->getAllOrganizersAsync();
     * $promise->then(
     *     function (array $organizers) {
     *         foreach ($organizers as $organizer) {
     *             echo $organizer->organization_name . "\n";
     *         }
     *     },
     *     function (RequestException $e) {
     *         echo "Error: " . $e->getMessage() . "\n";
     *     }
     * );
     * 
     * // Or wait for the result
     * $organizers = $promise->wait();
     * ```
     */
    public function getAllOrganizersAsync(): Promise\PromiseInterface
    {
        $promise = $this->client->requestAsync('GET', $this->baseUrl . '/api/integration/organizers');
        
        return $promise->then(
            function (ResponseInterface $response) {
                $data = json_decode($response->getBody()->getContents(), true);
                return OrganizerDTO::fromCollection($data);
            },
            function (RequestException $e) {
                $this->handleException($e);
                return [];
            }
        );
    }

    /**
     * Get organizer by ID
     * 
     * @param int $id The organizer ID
     * @return OrganizerDTO|null The organizer data or null if not found
     * 
     * Usage:
     * ```php
     * $organizerId = 123;
     * $organizer = $client->getOrganizerById($organizerId);
     * if ($organizer) {
     *     echo "Organizer name: " . $organizer->organization_name . "\n";
     * } else {
     *     echo "Organizer not found\n";
     * }
     * ```
     */
    public function getOrganizerById(int $id): ?OrganizerDTO
    {
        try {
            $response = $this->client->request('GET', $this->baseUrl . '/api/integration/organizers/' . $id);
            $data = json_decode($response->getBody()->getContents(), true);
            return OrganizerDTO::fromArray($data);
        } catch (RequestException $e) {
            $this->handleException($e);
            return null;
        }
    }

    /**
     * Get organizer by ID asynchronously
     * 
     * @param int $id The organizer ID
     * @return Promise\PromiseInterface A promise that resolves to an OrganizerDTO or null
     * 
     * Usage:
     * ```php
     * $organizerId = 123;
     * $promise = $client->getOrganizerByIdAsync($organizerId);
     * $promise->then(
     *     function (?OrganizerDTO $organizer) {
     *         if ($organizer) {
     *             echo "Organizer name: " . $organizer->organization_name . "\n";
     *         } else {
     *             echo "Organizer not found\n";
     *         }
     *     },
     *     function (RequestException $e) {
     *         echo "Error: " . $e->getMessage() . "\n";
     *     }
     * );
     * ```
     */
    public function getOrganizerByIdAsync(int $id): Promise\PromiseInterface
    {
        $promise = $this->client->requestAsync('GET', $this->baseUrl . '/api/integration/organizers/' . $id);
        
        return $promise->then(
            function (ResponseInterface $response) {
                $data = json_decode($response->getBody()->getContents(), true);
                return OrganizerDTO::fromArray($data);
            },
            function (RequestException $e) {
                $this->handleException($e);
                return null;
            }
        );
    }

    /**
     * Create a new organizer
     * 
     * @param OrganizerDTO $organizer The organizer data
     * @return OrganizerDTO|null The created organizer data or null if creation failed
     * 
     * Usage:
     * ```php
     * $organizer = new OrganizerDTO();
     * $organizer->organization_name = 'New Organizer';
     * $organizer->description = 'A great event organizer';
     * $organizer->email = 'contact@organizer.com';
     * $organizer->phone = '+46701234567';
     * $organizer->website = 'https://organizer.com';
     * // Set other properties...
     * 
     * $newOrganizer = $client->createOrganizer($organizer);
     * if ($newOrganizer) {
     *     echo "Organizer created with ID: " . $newOrganizer->id . "\n";
     * } else {
     *     echo "Failed to create organizer\n";
     * }
     * ```
     */
    public function createOrganizer(OrganizerDTO $organizer): ?OrganizerDTO
    {
        try {
            $response = $this->client->request('POST', $this->baseUrl . '/api/integration/organizers', [
                'json' => $organizer
            ]);
            $data = json_decode($response->getBody()->getContents(), true);
            return OrganizerDTO::fromArray($data);
        } catch (RequestException $e) {
            $this->handleException($e);
            return null;
        }
    }

    /**
     * Create a new organizer asynchronously
     * 
     * @param OrganizerDTO $organizer The organizer data
     * @return Promise\PromiseInterface A promise that resolves to the created OrganizerDTO or null
     * 
     * Usage:
     * ```php
     * $organizer = new OrganizerDTO();
     * $organizer->organization_name = 'New Organizer';
     * $organizer->description = 'A great event organizer';
     * $organizer->email = 'contact@organizer.com';
     * 
     * $promise = $client->createOrganizerAsync($organizer);
     * $promise->then(
     *     function (?OrganizerDTO $newOrganizer) {
     *         if ($newOrganizer) {
     *             echo "Organizer created with ID: " . $newOrganizer->id . "\n";
     *         } else {
     *             echo "Failed to create organizer\n";
     *         }
     *     },
     *     function (RequestException $e) {
     *         echo "Error: " . $e->getMessage() . "\n";
     *     }
     * );
     * ```
     */
    public function createOrganizerAsync(OrganizerDTO $organizer): Promise\PromiseInterface
    {
        $promise = $this->client->requestAsync('POST', $this->baseUrl . '/api/integration/organizers', [
            'json' => $organizer
        ]);
        
        return $promise->then(
            function (ResponseInterface $response) {
                $data = json_decode($response->getBody()->getContents(), true);
                return OrganizerDTO::fromArray($data);
            },
            function (RequestException $e) {
                $this->handleException($e);
                return null;
            }
        );
    }

    /**
     * Update an existing organizer
     * 
     * @param int $id The organizer ID
     * @param OrganizerDTO $organizer The updated organizer data
     * @return OrganizerDTO|null The updated organizer data or null if update failed
     * 
     * Usage:
     * ```php
     * $organizerId = 123;
     * 
     * // First get the existing organizer
     * $organizer = $client->getOrganizerById($organizerId);
     * 
     * // Update properties
     * $organizer->organization_name = 'Updated Organizer Name';
     * $organizer->description = 'Updated description';
     * 
     * $updatedOrganizer = $client->updateOrganizer($organizerId, $organizer);
     * if ($updatedOrganizer) {
     *     echo "Organizer updated successfully\n";
     * } else {
     *     echo "Failed to update organizer\n";
     * }
     * ```
     */
    public function updateOrganizer(int $id, OrganizerDTO $organizer): ?OrganizerDTO
    {
        try {
            $response = $this->client->request('PUT', $this->baseUrl . '/api/integration/organizers/' . $id, [
                'json' => $organizer
            ]);
            $data = json_decode($response->getBody()->getContents(), true);
            return OrganizerDTO::fromArray($data);
        } catch (RequestException $e) {
            $this->handleException($e);
            return null;
        }
    }

    /**
     * Update an existing organizer asynchronously
     * 
     * @param int $id The organizer ID
     * @param OrganizerDTO $organizer The updated organizer data
     * @return Promise\PromiseInterface A promise that resolves to the updated OrganizerDTO or null
     * 
     * Usage:
     * ```php
     * $organizerId = 123;
     * 
     * // First get the existing organizer
     * $organizer = $client->getOrganizerById($organizerId);
     * 
     * // Update properties
     * $organizer->organization_name = 'Updated Organizer Name';
     * $organizer->description = 'Updated description';
     * 
     * $promise = $client->updateOrganizerAsync($organizerId, $organizer);
     * $promise->then(
     *     function (?OrganizerDTO $updatedOrganizer) {
     *         if ($updatedOrganizer) {
     *             echo "Organizer updated successfully\n";
     *         } else {
     *             echo "Failed to update organizer\n";
     *         }
     *     },
     *     function (RequestException $e) {
     *         echo "Error: " . $e->getMessage() . "\n";
     *     }
     * );
     * ```
     */
    public function updateOrganizerAsync(int $id, OrganizerDTO $organizer): Promise\PromiseInterface
    {
        $promise = $this->client->requestAsync('PUT', $this->baseUrl . '/api/integration/organizers/' . $id, [
            'json' => $organizer
        ]);
        
        return $promise->then(
            function (ResponseInterface $response) {
                $data = json_decode($response->getBody()->getContents(), true);
                return OrganizerDTO::fromArray($data);
            },
            function (RequestException $e) {
                $this->handleException($e);
                return null;
            }
        );
    }

    /**
     * Delete an organizer
     * 
     * @param int $id The organizer ID
     * @return bool Whether the deletion was successful
     * 
     * Usage:
     * ```php
     * $organizerId = 123;
     * $success = $client->deleteOrganizer($organizerId);
     * if ($success) {
     *     echo "Organizer deleted successfully\n";
     * } else {
     *     echo "Failed to delete organizer\n";
     * }
     * ```
     */
    public function deleteOrganizer(int $id): bool
    {
        try {
            $this->client->request('DELETE', $this->baseUrl . '/api/integration/organizers/' . $id);
            return true;
        } catch (RequestException $e) {
            $this->handleException($e);
            return false;
        }
    }

    /**
     * Delete an organizer asynchronously
     * 
     * @param int $id The organizer ID
     * @return Promise\PromiseInterface A promise that resolves to a boolean indicating success
     * 
     * Usage:
     * ```php
     * $organizerId = 123;
     * $promise = $client->deleteOrganizerAsync($organizerId);
     * $promise->then(
     *     function (bool $success) {
     *         if ($success) {
     *             echo "Organizer deleted successfully\n";
     *         } else {
     *             echo "Failed to delete organizer\n";
     *         }
     *     },
     *     function (RequestException $e) {
     *         echo "Error: " . $e->getMessage() . "\n";
     *     }
     * );
     * ```
     */
    public function deleteOrganizerAsync(int $id): Promise\PromiseInterface
    {
        $promise = $this->client->requestAsync('DELETE', $this->baseUrl . '/api/integration/organizers/' . $id);
        
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
     * Get events for an organizer
     * 
     * @param int $id The organizer ID
     * @return EventDTO[] Array of EventDTO objects
     * 
     * Usage:
     * ```php
     * $organizerId = 123;
     * $events = $client->getOrganizerEvents($organizerId);
     * foreach ($events as $event) {
     *     echo "Event: {$event->name} (UID: {$event->uid})\n";
     * }
     * ```
     */
    public function getOrganizerEvents(int $id): array
    {
        try {
            $response = $this->client->request('GET', $this->baseUrl . '/api/integration/organizers/' . $id . '/events');
            $data = json_decode($response->getBody()->getContents(), true);
            return EventDTO::fromCollection($data);
        } catch (RequestException $e) {
            $this->handleException($e);
            return [];
        }
    }

    /**
     * Get events for an organizer asynchronously
     * 
     * @param int $id The organizer ID
     * @return Promise\PromiseInterface A promise that resolves to an array of EventDTO objects
     * 
     * Usage:
     * ```php
     * $organizerId = 123;
     * $promise = $client->getOrganizerEventsAsync($organizerId);
     * $promise->then(
     *     function (array $events) {
     *         foreach ($events as $event) {
     *             echo "Event: {$event->name} (UID: {$event->uid})\n";
     *         }
     *     },
     *     function (RequestException $e) {
     *         echo "Error: " . $e->getMessage() . "\n";
     *     }
     * );
     * ```
     */
    public function getOrganizerEventsAsync(int $id): Promise\PromiseInterface
    {
        $promise = $this->client->requestAsync('GET', $this->baseUrl . '/api/integration/organizers/' . $id . '/events');
        
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
     * Make multiple async requests and wait for all of them to complete
     * 
     * @param array $promises An array of promises
     * @return array The results of all promises
     * 
     * Usage:
     * ```php
     * // Create multiple async requests
     * $promises = [
     *     'organizer1' => $client->getOrganizerByIdAsync(1),
     *     'organizer2' => $client->getOrganizerByIdAsync(2),
     *     'organizers' => $client->getAllOrganizersAsync()
     * ];
     * 
     * // Wait for all requests to complete
     * $results = $client->awaitAll($promises);
     * 
     * // Access results by key
     * $organizer1 = $results['organizer1'];
     * $organizer2 = $results['organizer2'];
     * $allOrganizers = $results['organizers'];
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