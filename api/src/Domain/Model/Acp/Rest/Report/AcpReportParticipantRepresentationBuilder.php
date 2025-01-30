<?php

namespace App\Domain\Model\Acp\Rest\Report;


class AcpReportParticipantRepresentationBuilder
{
    private int $medaille;
    private string $temps;
    private string $sexe = "";
    private string $nom;
    private string $prenom;
    private string $naissance;
    private string $codeclub;
    private string $nomclub;

    // Setters for builder
    public function setMedaille(int $medaille): self
    {
        $this->medaille = $medaille;
        return $this;
    }

    public function setTemps(string $temps): self
    {
        $this->temps = $temps;
        return $this;
    }

    public function setSexe(string $sexe): self
    {
        $this->sexe = $sexe;
        return $this;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function setNaissance(string $naissance): self
    {
        $this->naissance = $naissance;
        return $this;
    }

    public function setCodeclub(string $codeclub): self
    {
        $this->codeclub = $codeclub;
        return $this;
    }

    public function setNomclub(string $nomclub): self
    {
        $this->nomclub = $nomclub;
        return $this;
    }

    // Build and return the Athlete object
    public function build(): AcpReportParticipantRepresentation
    {
        return new AcpReportParticipantRepresentation($this);
    }

    // Getters (only used internally by Athlete constructor)
    public function getMedaille(): int
    {
        return $this->medaille;
    }

    public function getTemps(): string
    {
        return $this->temps;
    }

    public function getSexe(): string
    {
        return $this->sexe;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function getNaissance(): string
    {
        return $this->naissance;
    }

    public function getCodeclub(): string
    {
        return $this->codeclub;
    }

    public function getNomclub(): string
    {
        return $this->nomclub;
    }
}


//$athlete = Athlete::builder()
//    ->setMedaille(1)
//    ->setTemps("21:40:00")
//    ->setSexe("M")
//    ->setNom("RIMOULET")
//    ->setPrenom("Marcel")
//    ->setNaissance("05/11/1965")
//    ->setCodeclub("FR72C005")
//    ->setNomclub("AC Belmontaise")
//    ->build();

