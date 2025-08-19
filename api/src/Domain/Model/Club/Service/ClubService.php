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
use Ramsey\Uuid\Uuid;

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
        try {
            $permissions = $this->getPermissions($currentuser_id);
            
            $club = new Club();
            $club->setClubUid(Uuid::uuid4()->toString());
            $club->setTitle($clubRepresentation->getTitle());
            $club->setAcpKod($clubRepresentation->getAcpKod());
            
            $this->clubrepository->createClub($club);
            
            return $this->clubAssembly->toRepresentation($club, $permissions);
        } catch (\Exception $e) {
            throw new BrevetException("Det gick inte att skapa klubben: " . $e->getMessage(), 1, $e);
        }
    }

    public function updateClub(string $currentuser_id, ClubRepresentation $clubRepresentation): ?ClubRepresentation
    {
        try {
            $permissions = $this->getPermissions($currentuser_id);
            $club = $this->clubrepository->getClubByUId($clubRepresentation->getClubUid());
            if ($club != null) {
                $club->setTitle($clubRepresentation->getTitle());
                $club->setAcpKod($clubRepresentation->getAcpKod());
                
                $this->clubrepository->updateClub($club);
                
                return $this->clubAssembly->toRepresentation($club, $permissions);
            }
            return null;
        } catch (\Exception $e) {
            throw new BrevetException("Det gick inte att uppdatera klubben: " . $e->getMessage(), 1, $e);
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
                    // Failed to delete club from loppservice
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
        $dto->acp_kod = $club->getAcpKod(); // Fixed: Using correct method name
        $dto->description = null; // VBApp doesn't have description field, set to null
        $dto->official_club = false; // Default to false, could be made configurable
        
        return $dto;
    }

    public function getPermissions($user_uid): array
    {
        return $this->permissionrepoitory->getPermissionsTodata("CLUB", $user_uid);
    }
}