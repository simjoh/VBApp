<?php

namespace App\Domain\Model\Partisipant\Rest;


use App\Domain\Model\Club\Rest\ClubRepresentation;
use App\Domain\Model\Competitor\Rest\CompetitorInforepresentation;
use App\Domain\Model\Competitor\Rest\CompetitorRepresentation;
use JsonSerializable;

class ParticipantInformationRepresentation implements JsonSerializable
{

    private ParticipantRepresentation $participant;
    private CompetitorRepresentation $competitorRepresentation;
    private CompetitorInforepresentation $competitorInforepresentation;
    private ClubRepresentation $clubRepresentation;







    /**
     * @return ParticipantRepresentation
     */
    public function getParticipant(): ParticipantRepresentation
    {
        return $this->participant;
    }

    /**
     * @param ParticipantRepresentation $participant
     */
    public function setParticipant(ParticipantRepresentation $participant): void
    {
        $this->participant = $participant;
    }

    /**
     * @return CompetitorRepresentation
     */
    public function getCompetitorRepresentation(): CompetitorRepresentation
    {
        return $this->competitorRepresentation;
    }

    /**
     * @param CompetitorRepresentation $competitorRepresentation
     */
    public function setCompetitorRepresentation(CompetitorRepresentation $competitorRepresentation): void
    {
        $this->competitorRepresentation = $competitorRepresentation;
    }

    /**
     * @return CompetitorInforepresentation
     */
    public function getCompetitorInforepresentation(): CompetitorInforepresentation
    {
        return $this->competitorInforepresentation;
    }

    /**
     * @param CompetitorInforepresentation $competitorInforepresentation
     */
    public function setCompetitorInforepresentation(CompetitorInforepresentation $competitorInforepresentation): void
    {
        $this->competitorInforepresentation = $competitorInforepresentation;
    }

    /**
     * @return ClubRepresentation
     */
    public function getClubRepresentation(): ClubRepresentation
    {
        return $this->clubRepresentation;
    }

    /**
     * @param ClubRepresentation $clubRepresentation
     */
    public function setClubRepresentation(ClubRepresentation $clubRepresentation): void
    {
        $this->clubRepresentation = $clubRepresentation;
    }


    public function jsonSerialize()
    {
        return (object)get_object_vars($this);
    }
}