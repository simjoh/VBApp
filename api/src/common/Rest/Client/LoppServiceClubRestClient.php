<?php

namespace App\common\Rest\Client;

use App\common\Rest\DTO\ClubDTO;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise;
use Psr\Http\Message\ResponseInterface;

/**
 * REST client for the LoppService Club API
 * 
 * This client provides methods to interact with the LoppService Club API,
 * including retrieving, creating, updating, and deleting clubs.
 * 
 * @package App\common\Rest\Client
 */
class LoppServiceClubRestClient
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
     * $client = new LoppServiceClubRestClient($settings);
     * ```
     * 
     * Usage with direct URL and API key:
     * ```php
     * $client = new LoppServiceClubRestClient(
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
     * Get all clubs
     * 
     * @return ClubDTO[] Array of ClubDTO objects
     * 
     * Usage:
     * ```php
     * $clubs = $client->getAllClubs();
     * foreach ($clubs as $club) {
     *     echo $club->name . "\n";
     * }
     * ```
     */
    public function getAllClubs(): array
    {
        try {
            $response = $this->client->request('GET', $this->baseUrl . '/api/integration/clubs');
            $data = json_decode($response->getBody()->getContents(), true);
            return ClubDTO::fromCollection($data);
        } catch (RequestException $e) {
            $this->handleException($e);
            return [];
        }
    }

    /**
     * Get a club by ID
     * 
     * @param string $clubUid The club UID
     * @return ClubDTO|null The club DTO or null if not found
     * 
     * Usage:
     * ```php
     * $club = $client->getClubById('12345-abcde-67890');
     * if ($club) {
     *     echo "Found club: " . $club->name . "\n";
     * }
     * ```
     */
    public function getClubById(string $clubUid): ?ClubDTO
    {
        try {
            $response = $this->client->request('GET', $this->baseUrl . '/api/integration/clubs/' . $clubUid);
            $data = json_decode($response->getBody()->getContents(), true);
            return ClubDTO::fromArray($data);
        } catch (RequestException $e) {
            if ($e->getResponse() && $e->getResponse()->getStatusCode() === 404) {
                return null; // Club not found
            }
            $this->handleException($e);
            return null;
        }
    }

    /**
     * Create a new club
     * 
     * @param ClubDTO $club The club data
     * @return ClubDTO|null The created club DTO or null on failure
     * 
     * Usage:
     * ```php
     * $clubData = new ClubDTO();
     * $clubData->name = 'My Cycling Club';
     * $clubData->description = 'A great cycling club';
     * $clubData->official_club = true;
     * 
     * $createdClub = $client->createClub($clubData);
     * if ($createdClub) {
     *     echo "Created club with UID: " . $createdClub->club_uid . "\n";
     * }
     * ```
     */
    public function createClub(ClubDTO $club): ?ClubDTO
    {
        try {
            $response = $this->client->request('POST', $this->baseUrl . '/api/integration/clubs', [
                'json' => $club->toArray()
            ]);
            $data = json_decode($response->getBody()->getContents(), true);
            return ClubDTO::fromArray($data);
        } catch (RequestException $e) {
            $this->handleException($e);
            return null;
        }
    }

    /**
     * Update an existing club
     * 
     * @param string $clubUid The club UID
     * @param ClubDTO $club The updated club data
     * @return ClubDTO|null The updated club DTO or null on failure
     * 
     * Usage:
     * ```php
     * $clubData = new ClubDTO();
     * $clubData->name = 'Updated Club Name';
     * $clubData->description = 'Updated description';
     * 
     * $updatedClub = $client->updateClub('12345-abcde-67890', $clubData);
     * if ($updatedClub) {
     *     echo "Updated club: " . $updatedClub->name . "\n";
     * }
     * ```
     */
    public function updateClub(string $clubUid, ClubDTO $club): ?ClubDTO
    {
        try {
            $response = $this->client->request('PUT', $this->baseUrl . '/api/integration/clubs/' . $clubUid, [
                'json' => $club->toArray()
            ]);
            $data = json_decode($response->getBody()->getContents(), true);
            return ClubDTO::fromArray($data);
        } catch (RequestException $e) {
            $this->handleException($e);
            return null;
        }
    }

    /**
     * Delete a club
     * 
     * @param string $clubUid The club UID
     * @return bool True if deletion was successful, false otherwise
     * 
     * Usage:
     * ```php
     * if ($client->deleteClub('12345-abcde-67890')) {
     *     echo "Club deleted successfully\n";
     * } else {
     *     echo "Failed to delete club\n";
     * }
     * ```
     */
    public function deleteClub(string $clubUid): bool
    {
        try {
            $response = $this->client->request('DELETE', $this->baseUrl . '/api/integration/clubs/' . $clubUid);
            return $response->getStatusCode() >= 200 && $response->getStatusCode() < 300;
        } catch (RequestException $e) {
            $this->handleException($e);
            return false;
        }
    }

    /**
     * Handle API exceptions
     * 
     * @param RequestException $e The exception to handle
     * @throws RequestException Re-throws the exception after logging
     */
    private function handleException(RequestException $e): void
    {
        // Exception handling without logging - let the calling code handle logging if needed
        throw $e;
    }
} 