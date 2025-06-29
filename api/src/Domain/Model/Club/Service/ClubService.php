<?php

namespace App\Domain\Model\Club\Service;

use App\common\Exceptions\BrevetException;
use App\common\Rest\Client\LoppServiceClubRestClient;
use App\common\Rest\DTO\ClubDTO;
use App\Domain\Model\Club\Club;
use App\Domain\Model\Club\ClubRepository;
use App\Domain\Model\Club\Rest\ClubAssembly;
use App\Domain\Model\Club\Rest\ClubRepresentation;
use App\Domain\Permission\PermissionRepository;
use Psr\Container\ContainerInterface;

class ClubService
{
    private $clubrepository;
    private $permissionrepoitory;
    private $clubAssembly;
    private $settings;
    private $clubRestClient;

    public function __construct(ContainerInterface   $c,
                                ClubRepository       $clubRepository,
                                PermissionRepository $permissionRepository,
                                ClubAssembly         $clubAssembly,
                                LoppServiceClubRestClient $clubRestClient = null)
    {
        $this->settings = $c->get('settings');
        $this->clubrepository = $clubRepository;
        $this->permissionrepoitory = $permissionRepository;
        $this->clubAssembly = $clubAssembly;
        $this->clubRestClient = $clubRestClient;
    }

    public function getClubByUid(string $club_uid, string $currentuser_id): ?ClubRepresentation
    {
        $permissions = $this->getPermissions($currentuser_id);
        $club = $this->clubrepository->getClubByUId($club_uid);
        if ($club != null) {
            return $this->clubAssembly->toRepresentation($club, []);
        }
        return new ClubRepresentation();
    }

    public function getAllClubs(string $currentuser_id): ?array
    {
        $permissions = $this->getPermissions($currentuser_id);
        $clubs = $this->clubrepository->getAllClubs();
        return $this->clubAssembly->toRepresentations($clubs, $currentuser_id);
    }

    public function createClub(string $currentuser_id, ClubRepresentation $clubRepresentation): ?ClubRepresentation
    {
        $permissions = $this->getPermissions($currentuser_id);

        // Get the database connection from the repository for transaction handling
        $connection = $this->clubrepository->getConnection();
        
        // Begin transaction
        $connection->beginTransaction();
        
        try {
            // First check if club already exists
            $club = $this->clubrepository->getClubByTitleLower($clubRepresentation->getTitle());
            if ($club == null) {
                $club_uid = $this->clubrepository->createClub($clubRepresentation->getAcpCode(), $clubRepresentation->getTitle());
                $clubreturn = $this->clubrepository->getClubByUId($club_uid);
            } else {
                $clubreturn = $club;
            }

            if (!$clubreturn) {
                throw new BrevetException("Failed to create club locally", 1);
            }

            // Try to sync with LoppService, but don't fail if LoppService is unavailable
            if ($this->clubRestClient) {
                try {
                    $clubDTO = $this->createClubDTOFromClub($clubreturn);
                    
                    error_log("Creating club in LoppService with data: " . json_encode($clubDTO->toArray()));
                    
                    $createdClubDTO = $this->clubRestClient->createClub($clubDTO);
                    
                    if (!$createdClubDTO) {
                        error_log("Warning: Failed to create club in LoppService, but continuing with local club: " . $clubreturn->getClubUid());
                    } else {
                        error_log("Successfully created club in LoppService: " . $clubreturn->getClubUid());
                    }
                } catch (\Exception $loppServiceException) {
                    // Only skip LoppService for connection errors, otherwise re-throw
                    $errorMessage = $loppServiceException->getMessage();
                    if (strpos($errorMessage, 'Could not resolve host') !== false || 
                        strpos($errorMessage, 'Connection refused') !== false ||
                        strpos($errorMessage, 'Connection timed out') !== false) {
                        // Connection issue - log and continue
                        error_log("LoppService connection failed for club " . $clubreturn->getClubUid() . ": " . $errorMessage);
                        error_log("Continuing with local club creation only");
                    } else {
                        // Other error - log but don't fail the transaction
                        error_log("Warning: Failed to create club in LoppService, but continuing with local club: " . $clubreturn->getClubUid());
                    }
                }
            }

            // Commit the transaction
            $connection->commit();
            
            return $this->clubAssembly->toRepresentation($clubreturn, $permissions);
            
        } catch (\Exception $e) {
            // Rollback the transaction if any exception occurs
            $connection->rollBack();
            
            // Log the error with more details
            error_log("Failed to create club with rollback: " . $e->getMessage());
            
            // Re-throw the exception to be handled by the caller
            if ($e instanceof BrevetException) {
                throw $e;
            }
            
            throw new BrevetException("Det gick inte att skapa klubben: " . $e->getMessage(), 10, $e);
        }
    }

    public function updateClub(string $currentuser_id, ClubRepresentation $clubRepresentation): ?ClubRepresentation
    {
        $club = $this->clubrepository->getClubByUId($clubRepresentation->getClubUid());
        if ($club == null) {
            return null;
        }
        
        // Debug logging
        error_log("DEBUG: Updating club " . $clubRepresentation->getClubUid());
        error_log("DEBUG: Current ACP code: " . ($club->getAcpCode() ?? 'null'));
        error_log("DEBUG: New ACP code from request: " . ($clubRepresentation->getAcpCode() ?? 'null'));
        error_log("DEBUG: New title from request: " . ($clubRepresentation->getTitle() ?? 'null'));
        
        // Update the club with new values
        $club->setTitle($clubRepresentation->getTitle());
        $club->setAcpCode($clubRepresentation->getAcpCode());
        
        // Debug logging after setting
        error_log("DEBUG: ACP code after setting: " . ($club->getAcpCode() ?? 'null'));
        
        $permissions = $this->getPermissions($currentuser_id);

        // Get the database connection from the repository
        $connection = $this->clubrepository->getConnection();
        
        // Begin transaction
        $connection->beginTransaction();
        
        try {
            // Update club in local database first
            $updatedClub = $this->clubrepository->updateClub($club);
            
            if (!$updatedClub) {
                throw new BrevetException("Failed to update club locally", 1);
            }
            
            // Try to sync with LoppService, but don't fail if LoppService is unavailable
            if ($this->clubRestClient) {
                try {
                    // Try to get the club from LoppService to check if it exists
                    $existingClubDTO = $this->clubRestClient->getClubById($updatedClub->getClubUid());
                    
                    $clubDTO = $this->createClubDTOFromClub($updatedClub);
                    
                    if ($existingClubDTO) {
                        // Update the existing club
                        $updatedClubDTO = $this->clubRestClient->updateClub($updatedClub->getClubUid(), $clubDTO);
                        
                        if (!$updatedClubDTO) {
                            error_log("Warning: Failed to update club in LoppService, but continuing with local update: " . $updatedClub->getClubUid());
                        } else {
                            error_log("Successfully updated club in LoppService: " . $updatedClub->getClubUid());
                        }
                    } else {
                        // Club doesn't exist in LoppService, try to create it
                        $createdClubDTO = $this->clubRestClient->createClub($clubDTO);
                        
                        if (!$createdClubDTO) {
                            error_log("Warning: Failed to create club in LoppService, but continuing with local update: " . $updatedClub->getClubUid());
                        } else {
                            error_log("Successfully created club in LoppService: " . $updatedClub->getClubUid());
                        }
                    }
                } catch (\Exception $loppServiceException) {
                    // Only skip LoppService for connection errors, otherwise re-throw
                    $errorMessage = $loppServiceException->getMessage();
                    if (strpos($errorMessage, 'Could not resolve host') !== false || 
                        strpos($errorMessage, 'Connection refused') !== false ||
                        strpos($errorMessage, 'Connection timed out') !== false) {
                        // Connection issue - log and continue
                        error_log("LoppService connection failed for club " . $updatedClub->getClubUid() . ": " . $errorMessage);
                        error_log("Continuing with local club update only");
                    } else {
                        // Other error - log but don't fail the transaction
                        error_log("Warning: Failed to update club in LoppService, but continuing with local update: " . $updatedClub->getClubUid());
                    }
                }
            }
            
            $connection->commit();
            return $this->clubAssembly->toRepresentation($updatedClub, $permissions);
            
        } catch (\Exception $e) {
            // Rollback the transaction if any exception occurs
            $connection->rollBack();
            
            // Log the error with more details
            error_log("Failed to update club with rollback: " . $e->getMessage());
            
            // Re-throw the exception to be handled by the caller
            if ($e instanceof BrevetException) {
                throw $e;
            }
            
            throw new BrevetException("Det gick inte att uppdatera klubben: " . $e->getMessage(), 14, $e);
        }
    }

    public function deleteClub(string $currentuser_id, string $club_uid): bool
    {
        $permissions = $this->getPermissions($currentuser_id);

        $club = $this->clubrepository->getClubByUId($club_uid);
        if (!$club) {
            throw new BrevetException("Klubben hittades inte", 1, null);
        }

        // Get the database connection from the repository for transaction handling
        $connection = $this->clubrepository->getConnection();
        
        // Begin transaction
        $connection->beginTransaction();
        
        try {
            // First try to delete from the main VBApp database
            $success = $this->clubrepository->deleteClub($club_uid);
            
            if (!$success) {
                throw new BrevetException("Kunde inte ta bort klubben från databasen", 3, null);
            }

            // Then try to delete from loppservice if REST client is available
            if ($this->clubRestClient) {
                try {
                    $this->clubRestClient->deleteClub($club_uid);
                } catch (\Exception $e) {
                    // Log the error but don't fail the main transaction
                    error_log("Failed to delete club from loppservice: " . $e->getMessage());
                    // Could throw exception here if we want strict synchronization
                    // throw new BrevetException("Kunde inte ta bort klubben från loppservice: " . $e->getMessage(), 4, null);
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
            
            throw new BrevetException("Ett fel uppstod vid borttagning av klubben", 6, $e);
        }
    }

    /**
     * Create a ClubDTO from a Club domain object
     * 
     * @param Club $club The club domain object
     * @return ClubDTO The DTO for LoppService
     */
    private function createClubDTOFromClub(Club $club): ClubDTO
    {
        $dto = new ClubDTO();
        $dto->club_uid = $club->getClubUid();
        $dto->name = $club->getTitle(); // VBApp uses 'title' but loppservice uses 'name'
        $dto->acp_kod = $club->getAcpCode(); // Map ACP code
        $dto->description = null; // VBApp doesn't have description field, set to null
        $dto->official_club = false; // Default to false, could be made configurable
        
        return $dto;
    }

    public function getPermissions($user_uid): array
    {
        return $this->permissionrepoitory->getPermissionsTodata("CLUB", $user_uid);
    }
}