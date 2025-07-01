<?php

namespace App\Domain\Model\Club\Rest;

use App\common\Rest\Link;
use App\Domain\Model\Club\Club;
use App\Domain\Model\Club\Rest\ClubRepresentation;
use App\Domain\Model\Club\Rest\ClubRepresentationTransformer;
use App\Domain\Model\Club\Service\ClubService;
use App\Domain\Model\Partisipant\Repository\ParticipantRepository;
use App\Domain\Permission\PermissionRepository;
use Psr\Container\ContainerInterface;
use PDO;
use PDOException;
use Exception;

class ClubAssembly
{
    private $permissinrepository;
    private $settings;
    private $connection;
    private $participantRepository;

    public function __construct(PermissionRepository $permissionRepository, ContainerInterface $c, PDO $connection)
    {
        $this->permissinrepository = $permissionRepository;
        $this->settings = $c->get('settings');
        $this->connection = $connection;
        $this->participantRepository = new ParticipantRepository($connection);
    }

    public function toRepresentations(array $clubsArray, string $currentUserUid): array {
        $permissions = $this->getPermissions($currentUserUid);
        $clubs = array();
        foreach ($clubsArray as $x =>  $club) {
            array_push($clubs, (object) $this->toRepresentation($club,$permissions));
        }
        return $clubs;
    }

    public function toRepresentation( $club,  array $permissions): ?ClubRepresentation {
        $clubrepr = new ClubRepresentation();

        $clubrepr->setClubUid($club->getClubUid());
        $clubrepr->setTitle($club->getTitle());
        $clubrepr->setAcpKod($club->getAcpKod());

        $linkArray = array();

        array_push($linkArray, new Link("self", 'GET', $this->settings['path'] . 'club/' . $club->getClubUid()));
        
        // Check if club is in use by participants before adding delete link
        if (!$this->isClubInUseByParticipants($club->getClubUid())) {
            array_push($linkArray, new Link("relation.club.delete", 'DELETE', $this->settings['path'] . 'club/' . $club->getClubUid()));
        }
        
        $clubrepr->setLinks($linkArray);

        return $clubrepr;
    }

    private function isClubInUseByParticipants(string $clubUid): bool
    {
        try {
            return $this->participantRepository->isClubInUseByParticipants($clubUid);
        } catch (\Exception $e) {
            // Log the error but don't prevent deletion if we can't check
            error_log("Error checking if club is in use: " . $e->getMessage());
            return false; // Allow deletion if we can't determine usage
        }
    }

    public function getPermissions($user_uid): array
    {
        return $this->permissinrepository->getPermissionsTodata("CLUB",$user_uid);
    }
}