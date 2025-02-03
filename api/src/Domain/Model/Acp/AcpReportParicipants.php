<?php

namespace App\Domain\Model\Acp;

class AcpReportParicipants
{
    private string $participant_uid;
    private string $report_uid;
    private bool $medaille = false;
    private string $temps;
    private string $sexe;
    private string $nom;
    private string $prenom;
    private string $naissance;
    private string $codeclub;
    private string $nomclub;
    private bool $delivered = false;
    private ?string $createdAt;
    private ?int $organizer_id;



    private ?string $updatedAt;

    // Constructor
    public function __construct(string $participant_uid, string $report_uid, int $medaille, string $temps, string $sexe, string $nom, string $prenom, string $naissance, string $codeclub, string $nomclub, bool $delivered, string $createdAt, string $updatedAt, int $organizer_id)
    {
        $this->medaille = $medaille;
        $this->temps = $temps;
        $this->sexe = $sexe;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->naissance = $naissance;
        $this->codeclub = $codeclub;
        $this->nomclub = $nomclub;
        $this->report_uid = $report_uid;
        $this->participant_uid = $participant_uid;
        $this->delivered = $delivered;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->organizer_id = $organizer_id;
    }


    // Getter and Setter methods for each property
    public function getMedaille(): int
    {
        return $this->medaille;
    }

    public function setMedaille(int $medaille): void
    {
        $this->medaille = $medaille;
    }

    public function getTemps(): string
    {
        return $this->temps;
    }

    public function setTemps(string $temps): void
    {
        $this->temps = $temps;
    }

    public function getSexe(): string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): void
    {
        $this->sexe = $sexe;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): void
    {
        $this->prenom = $prenom;
    }

    public function getNaissance(): string
    {
        return $this->naissance;
    }

    public function setNaissance(string $naissance): void
    {
        $this->naissance = $naissance;
    }

    public function getCodeclub(): string
    {
        return $this->codeclub;
    }

    public function setCodeclub(string $codeclub): void
    {
        $this->codeclub = $codeclub;
    }

    public function getNomclub(): string
    {
        return $this->nomclub;
    }

    public function setNomclub(string $nomclub): void
    {
        $this->nomclub = $nomclub;
    }


    public function getParticipantUid(): string
    {
        return $this->participant_uid;
    }

    public function setParticipantUid(string $participant_uid): void
    {
        $this->participant_uid = $participant_uid;
    }

    public function getReportUid(): string
    {
        return $this->report_uid;
    }

    public function setReportUid(string $report_uid): void
    {
        $this->report_uid = $report_uid;
    }

    public function isDelivered(): bool
    {
        return $this->delivered;
    }

    public function setDelivered(bool $delivered): void
    {
        $this->delivered = $delivered;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?string $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getOrganizerId(): ?int
    {
        return $this->organizer_id;
    }

    public function setOrganizerId(?int $organizer_id): void
    {
        $this->organizer_id = $organizer_id;
    }




    public static function fromArray(array $data): self
    {
        return new self(
            (string)$data['report_uid'],
            (string)$data['participant_uid'],
            (bool)$data['medaille'],
            (string)$data['temps'],
            (string)$data['sexe'],
            (string)$data['nom'],
            (string)$data['prenom'],
            (string)$data['naissance'],
            (string)$data['codeclub'],
            (string)$data['nomclub'],
            (bool)$data['delivered'],
            (string)$data['createdAt'],
            (string)$data['updatedAt'],
            (int)$data['$organizer_id']

        );
    }
}