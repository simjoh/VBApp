<?php

namespace App\Domain\Model\Organizer\Service;

use App\common\Exceptions\BrevetException;
use App\common\Rest\Client\LoppServiceOrganizerRestClient;
use App\common\Rest\DTO\OrganizerDTO;
use App\Domain\Model\Organizer\Organizer;
use App\Domain\Model\Organizer\Repository\OrganizerRepository;
use App\Domain\Model\Organizer\Rest\OrganizerAssembly;
use App\Domain\Model\Organizer\Rest\OrganizerRepresentation;
use App\Domain\Permission\PermissionRepository;
use Psr\Container\ContainerInterface;

class OrganizerService
{
    private $organizerRepository;
    private $permissionRepository;
    private $organizerAssembly;
    private $organizerRestClient;
    private $settings;

    public function __construct(ContainerInterface $c,
                                OrganizerRepository $organizerRepository,
                                PermissionRepository $permissionRepository,
                                OrganizerAssembly $organizerAssembly,
                                LoppServiceOrganizerRestClient $organizerRestClient)
    {
        $this->settings = $c->get('settings');
        $this->organizerRepository = $organizerRepository;
        $this->permissionRepository = $permissionRepository;
        $this->organizerAssembly = $organizerAssembly;
        $this->organizerRestClient = $organizerRestClient;
    }

    public function getOrganizerById(int $organizer_id, string $currentuser_id): ?OrganizerRepresentation
    {
        $permissions = $this->getPermissions($currentuser_id);
        $organizer = $this->organizerRepository->getOrganizerById($organizer_id);
        if ($organizer != null) {
            return $this->organizerAssembly->toRepresentation($organizer, $permissions);
        }
        return new OrganizerRepresentation();
    }

    public function getAllOrganizers(string $currentuser_id): ?array
    {
        $permissions = $this->getPermissions($currentuser_id);
        $organizers = $this->organizerRepository->getAllOrganizers();
        return $this->organizerAssembly->toRepresentations($organizers, $currentuser_id);
    }

    public function createOrganizer(string $currentuser_id, OrganizerRepresentation $organizerRepresentation): ?OrganizerRepresentation
    {
        $permissions = $this->getPermissions($currentuser_id);
        
        $organizer = new Organizer(
            0, // ID will be set by database
            $organizerRepresentation->getOrganizationName(),
            $organizerRepresentation->getContactPersonName(),
            $organizerRepresentation->getEmail(),
            $organizerRepresentation->getDescription(),
            $organizerRepresentation->getWebsite(),
            $organizerRepresentation->getWebsitePay(),
            $organizerRepresentation->getLogoSvg(),
            $organizerRepresentation->isActive() ?? true,
            $organizerRepresentation->getClubUid()
        );

        // Get the database connection from the repository
        $connection = $this->organizerRepository->getConnection();
        
        // Begin transaction
        $connection->beginTransaction();
        
        try {
            // Create organizer in local database first
            $createdOrganizer = $this->organizerRepository->createOrganizer($organizer);
            
            if (!$createdOrganizer) {
                throw new BrevetException("Failed to create organizer locally", 1);
            }
            
            // Try to create corresponding organizer in LoppService, but don't fail if unavailable
            try {
                $organizerDTO = $this->createOrganizerDTOFromOrganizer($createdOrganizer);
                
                // Debug log
                error_log("Creating organizer in LoppService with data: " . json_encode([
                    'id' => $organizerDTO->id,
                    'organization_name' => $organizerDTO->organization_name,
                    'contact_person_name' => $organizerDTO->contact_person_name,
                    'email' => $organizerDTO->email,
                    'active' => $organizerDTO->active
                ]));
                
                $createdOrganizerDTO = $this->organizerRestClient->createOrganizer($organizerDTO);
                
                if (!$createdOrganizerDTO) {
                    error_log("Warning: Failed to create organizer in LoppService, but continuing with local organizer: " . $createdOrganizer->getId());
                } else {
                    error_log("Successfully created organizer in LoppService: " . $createdOrganizer->getId());
                }
            } catch (\Exception $loppServiceException) {
                // Only skip LoppService for connection errors, otherwise re-throw
                $errorMessage = $loppServiceException->getMessage();
                if (strpos($errorMessage, 'Could not resolve host') !== false || 
                    strpos($errorMessage, 'Connection refused') !== false ||
                    strpos($errorMessage, 'Connection timed out') !== false) {
                    // Connection issue - log and continue
                    error_log("LoppService connection failed for new organizer " . $createdOrganizer->getId() . ": " . $errorMessage);
                    error_log("Continuing with local organizer creation only");
                } else {
                    // Other error - re-throw to maintain existing behavior for non-connection issues
                    throw $loppServiceException;
                }
            }
            
            $connection->commit();
            return $this->organizerAssembly->toRepresentation($createdOrganizer, $permissions);
            
        } catch (\Exception $e) {
            // Rollback the transaction if any exception occurs
            $connection->rollBack();
            
            // Log the error with more details
            error_log("Failed to create organizer with rollback: " . $e->getMessage());
            error_log("Organizer data: " . json_encode([
                'organization_name' => $organizer->getOrganizationName(),
                'contact_person_name' => $organizer->getContactPersonName(),
                'email' => $organizer->getEmail(),
                'active' => $organizer->isActive()
            ]));
            
            // Re-throw the exception to be handled by the caller
            throw new BrevetException("Det gick inte att skapa organizer: " . $e->getMessage(), 11, $e);
        }
    }

    public function updateOrganizer(string $currentuser_id, OrganizerRepresentation $organizerRepresentation): ?OrganizerRepresentation
    {
        $organizer = $this->organizerRepository->getOrganizerById($organizerRepresentation->getId());
        if ($organizer == null) {
            return null;
        }
        
        // Update the organizer with new values
        $organizer->setOrganizationName($organizerRepresentation->getOrganizationName());
        $organizer->setDescription($organizerRepresentation->getDescription());
        $organizer->setWebsite($organizerRepresentation->getWebsite());
        $organizer->setWebsitePay($organizerRepresentation->getWebsitePay());
        $organizer->setLogoSvg($organizerRepresentation->getLogoSvg());
        $organizer->setContactPersonName($organizerRepresentation->getContactPersonName());
        $organizer->setEmail($organizerRepresentation->getEmail());
        $organizer->setActive($organizerRepresentation->isActive() ?? true);
        $organizer->setClubUid($organizerRepresentation->getClubUid());
        
        $permissions = $this->getPermissions($currentuser_id);

        // Get the database connection from the repository
        $connection = $this->organizerRepository->getConnection();
        
        // Begin transaction
        $connection->beginTransaction();
        
        try {
            // Update organizer in local database first
            $updatedOrganizer = $this->organizerRepository->updateOrganizer($organizer);
            
            if (!$updatedOrganizer) {
                throw new BrevetException("Failed to update organizer locally", 1);
            }
            
            // Try to sync with LoppService, but don't fail if LoppService is unavailable
            try {
                // Try to get the organizer from LoppService to check if it exists
                $existingOrganizerDTO = $this->organizerRestClient->getOrganizerById($updatedOrganizer->getId());
                
                $organizerDTO = $this->createOrganizerDTOFromOrganizer($updatedOrganizer);
                
                if ($existingOrganizerDTO) {
                    // Update the existing organizer
                    $updatedOrganizerDTO = $this->organizerRestClient->updateOrganizer($updatedOrganizer->getId(), $organizerDTO);
                    
                    if (!$updatedOrganizerDTO) {
                        error_log("Warning: Failed to update organizer in LoppService, but continuing with local update: " . $updatedOrganizer->getId());
                    } else {
                        error_log("Successfully updated organizer in LoppService: " . $updatedOrganizer->getId());
                    }
                } else {
                    // Organizer doesn't exist in LoppService, try to create it
                    $createdOrganizerDTO = $this->organizerRestClient->createOrganizer($organizerDTO);
                    
                    if (!$createdOrganizerDTO) {
                        error_log("Warning: Failed to create organizer in LoppService, but continuing with local update: " . $updatedOrganizer->getId());
                    } else {
                        error_log("Successfully created organizer in LoppService: " . $updatedOrganizer->getId());
                    }
                }
            } catch (\Exception $loppServiceException) {
                // Only skip LoppService for connection errors, otherwise re-throw
                $errorMessage = $loppServiceException->getMessage();
                if (strpos($errorMessage, 'Could not resolve host') !== false || 
                    strpos($errorMessage, 'Connection refused') !== false ||
                    strpos($errorMessage, 'Connection timed out') !== false) {
                    // Connection issue - log and continue
                    error_log("LoppService connection failed for organizer " . $updatedOrganizer->getId() . ": " . $errorMessage);
                    error_log("Continuing with local organizer update only");
                } else {
                    // Other error - re-throw to maintain existing behavior for non-connection issues
                    throw $loppServiceException;
                }
            }
            
            $connection->commit();
            return $this->organizerAssembly->toRepresentation($updatedOrganizer, $permissions);
            
        } catch (\Exception $e) {
            // Rollback the transaction if any exception occurs
            $connection->rollBack();
            
            // Log the error with more details
            error_log("Failed to update organizer with rollback: " . $e->getMessage());
            error_log("Organizer data: " . json_encode([
                'id' => $organizer->getId(),
                'organization_name' => $organizer->getOrganizationName(),
                'contact_person_name' => $organizer->getContactPersonName(),
                'email' => $organizer->getEmail(),
                'active' => $organizer->isActive()
            ]));
            
            // Re-throw the exception to be handled by the caller
            throw new BrevetException("Det gick inte att uppdatera organizer: " . $e->getMessage(), 14, $e);
        }
    }

    public function deleteOrganizer(int $organizerId, string $currentUserUid): bool
    {
        $permissions = $this->getPermissions($currentUserUid);

    /*     if (!in_array('organizer:delete', $permissions)) {
            throw new BrevetException("Användaren har inte rättighet att ta bort arrangörer", 2, null);
        } */

        $organizer = $this->organizerRepository->getOrganizerById($organizerId);
        if (!$organizer) {
            throw new BrevetException("Arrangören hittades inte", 1, null);
        }

        // Check if organizer is being used by tracks before deletion
        $tracksUsingOrganizer = $this->getTracksForOrganizer($organizerId);
        if (!empty($tracksUsingOrganizer)) {
            throw new BrevetException("Det finns banor kopplade till arrangören. Banorna måste tas bort eller kopplas till en annan arrangör innan arrangören kan tas bort", 5, null);
        }

        // Get the database connection from the repository for transaction handling
        $connection = $this->organizerRepository->getConnection();
        
        // Begin transaction
        $connection->beginTransaction();
        
        try {
            // First try to delete from the main VBApp database
            $success = $this->organizerRepository->deleteOrganizer($organizerId);
            
            if (!$success) {
                throw new BrevetException("Kunde inte ta bort arrangören från databasen", 3, null);
            }

            // Then try to delete from loppservice if REST client is available
            if ($this->organizerRestClient) {
                try {
                    $this->organizerRestClient->deleteOrganizer($organizerId);
                } catch (\Exception $e) {
                    // Log the error but don't fail the main transaction
                    error_log("Failed to delete organizer from loppservice: " . $e->getMessage());
                    // Could throw exception here if we want strict synchronization
                    // throw new BrevetException("Kunde inte ta bort arrangören från loppservice: " . $e->getMessage(), 4, null);
                }
            }

            // Commit the transaction
            $connection->commit();
            
            return true;
        } catch (\Exception $e) {
            // Rollback the transaction on any error
            $connection->rollBack();
            
            if ($e instanceof BrevetException) {
                throw $e;
            }
            
            throw new BrevetException("Ett fel uppstod vid borttagning av arrangören", 6, $e);
        }
    }

    /**
     * Get tracks associated with an organizer
     * 
     * @param int $organizerId
     * @return array
     */
    private function getTracksForOrganizer(int $organizerId): array
    {
        try {
            $connection = $this->organizerRepository->getConnection();
            $statement = $connection->prepare("SELECT track_uid, title FROM track WHERE organizer_id = :organizer_id");
            $statement->bindParam(':organizer_id', $organizerId, \PDO::PARAM_INT);
            $statement->execute();
            
            return $statement->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error checking tracks for organizer: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Create an OrganizerDTO from an Organizer domain object
     * 
     * @param Organizer $organizer The organizer domain object
     * @return OrganizerDTO The DTO for LoppService
     */
    private function createOrganizerDTOFromOrganizer(Organizer $organizer): OrganizerDTO
    {
        $dto = new OrganizerDTO();
        $dto->id = $organizer->getId();
        $dto->organization_name = $organizer->getOrganizationName();
        $dto->description = $organizer->getDescription() ?: null;
        
        // Handle website field - trim whitespace and validate URL
        $website = trim($organizer->getWebsite() ?? '');
        if (!empty($website) && filter_var($website, FILTER_VALIDATE_URL)) {
            $dto->website = $website;
        } else {
            $dto->website = null;
        }
        
        $dto->logo_svg = $organizer->getLogoSvg() ?: null;
        $dto->contact_person_name = $organizer->getContactPersonName();
        $dto->email = $organizer->getEmail();
        $dto->active = $organizer->isActive();
        
        return $dto;
    }

    public function getPermissions($user_uid): array
    {
        return $this->permissionRepository->getPermissionsTodata("ORGANIZER", $user_uid);
    }
} 