<?php

namespace App\common\Rest\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

/**
 * REST client for the LoppService Participant/Registration API
 * 
 * This client provides methods to interact with the LoppService Participant/Registration API,
 * including retrieving, creating, updating, and deleting registrations.
 * 
 * @package App\common\Rest\Client
 */
class LoppServiceParticipantRestClient
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
     * $client = new LoppServiceParticipantRestClient($settings);
     * ```
     * 
     * Usage with direct URL and API key:
     * ```php
     * $client = new LoppServiceParticipantRestClient(
     *     'https://loppservice.example.com',
     *     'your-api-key-here'
     * );
     * ```
     */
    public function __construct($settings = [], string $apiKey = '')
    {
        error_log("=== LOPPSERVICE REST CLIENT CONSTRUCTOR START ===");
        error_log("Settings type: " . gettype($settings));
        error_log("Settings: " . print_r($settings, true));
        error_log("API Key parameter: " . $apiKey);
        
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

        error_log("Final Base URL: " . $this->baseUrl);
        error_log("Final API Key: " . $this->apiKey);

        $this->client = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'apikey' => $this->apiKey,
                'User-Agent' => 'Loppservice/1.0'
            ]
        ]);
        
        error_log("=== LOPPSERVICE REST CLIENT CONSTRUCTOR END ===");
    }

    /**
     * Delete a registration
     * 
     * @param string $registrationUid The registration UID
     * @return bool True if deletion was successful, false otherwise
     * 
     * Usage:
     * ```php
     * if ($client->deleteRegistration('12345-abcde-67890')) {
     *     echo "Registration deleted successfully\n";
     * } else {
     *     echo "Failed to delete registration\n";
     * }
     * ```
     */
    public function deleteRegistration(string $registrationUid): bool
    {

        
        try {
            $url = $this->baseUrl . '/api/integration/registration/' . $registrationUid;
          
            
            $response = $this->client->request('DELETE', $url);
            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            
            
            $success = $statusCode >= 200 && $statusCode < 300;
            error_log("Success: " . ($success ? 'YES' : 'NO'));
            
            return $success;
        } catch (RequestException $e) {
            error_log("RequestException in deleteRegistration: " . $e->getMessage());
            $this->handleException($e);
            return false;
        }
    }

    /**
     * Get a registration by UID
     * 
     * @param string $registrationUid The registration UID
     * @return array|null The registration data or null if not found
     * 
     * Usage:
     * ```php
     * $registration = $client->getRegistration('12345-abcde-67890');
     * if ($registration) {
     *     echo "Found registration: " . $registration['registration_uid'] . "\n";
     * }
     * ```
     */
    public function getRegistration(string $registrationUid): ?array
    {
        try {
            $response = $this->client->request('GET', $this->baseUrl . '/api/integration/registration/' . $registrationUid . '/registration');
            $data = json_decode($response->getBody()->getContents(), true);
            return $data;
        } catch (RequestException $e) {
            if ($e->getResponse() && $e->getResponse()->getStatusCode() === 404) {
                return null; // Registration not found
            }
            $this->handleException($e);
            return null;
        }
    }

    /**
     * Get all registrations for an event
     * 
     * @param string $eventUid The event UID
     * @return array Array of registration data
     * 
     * Usage:
     * ```php
     * $registrations = $client->getEventRegistrations('12345-abcde-67890');
     * foreach ($registrations as $registration) {
     *     echo $registration['registration_uid'] . "\n";
     * }
     * ```
     */
    public function getEventRegistrations(string $eventUid): array
    {
        try {
            $response = $this->client->request('GET', $this->baseUrl . '/api/integration/event/' . $eventUid . '/registrations');
            $data = json_decode($response->getBody()->getContents(), true);
            return $data['registrations'] ?? [];
        } catch (RequestException $e) {
            $this->handleException($e);
            return [];
        }
    }

    /**
     * Handle API exceptions
     * 
     * @param RequestException $e The exception to handle
     * @return void
     */
    private function handleException(RequestException $e): void
    {
        $response = $e->getResponse();
        if ($response) {
            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            error_log("LoppService Participant API Error: HTTP {$statusCode} - {$body}");
        } else {
            error_log("LoppService Participant API Error: " . $e->getMessage());
        }
    }
}
