<?php

namespace App\Domain\Model\Partisipant\Rest;

use App\Domain\Model\Club\Rest\ClubRepresentation;
use App\Domain\Model\Competitor\Rest\CompetitorInforepresentation;
use App\Domain\Model\Competitor\Rest\CompetitorRepresentation;
use App\Domain\Model\Partisipant\Participant;
use App\Domain\Permission\PermissionRepository;
use Psr\Container\ContainerInterface;

class ParticipantInformationAssembly
{

    private $permissinrepository;
    private $settings;
    public function __construct(ContainerInterface $c, PermissionRepository $permissionRepository)
    {
        $this->permissinrepository = $permissionRepository;
        $this->settings = $c->get('settings');
    }


    public function toRepresentation(ParticipantRepresentation $participantrepresentation, CompetitorRepresentation $competitorRepresentation,
                                     ClubRepresentation $clubRepresentation,
                                     CompetitorInforepresentation $competitorInforepresentation,
                                     array $permissions): ?ParticipantInformationRepresentation {

        $participantinformationrepresentation = new ParticipantInformationRepresentation;
        $participantinformationrepresentation->setParticipant($participantrepresentation);
        $participantinformationrepresentation->setCompetitorRepresentation($competitorRepresentation);
        $participantinformationrepresentation->setClubRepresentation($clubRepresentation);
        $participantinformationrepresentation->setCompetitorInforepresentation($competitorInforepresentation);

        return $participantinformationrepresentation;
    }

}