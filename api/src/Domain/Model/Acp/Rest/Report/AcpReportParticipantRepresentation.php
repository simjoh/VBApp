<?php

namespace App\Domain\Model\Acp\Rest\Report;

use JsonSerializable;


class AcpReportParticipantRepresentation implements JsonSerializable
{
    private int $medaille;
    private string $temps;
    private string $sexe;
    private string $nom;
    private string $prenom;
    private string $naissance;
    private string $codeclub;
    private string $nomclub;

    public function __construct($data)
    {
        $this->medaille = $data->medaille;
        $this->temps = $data->temps;
        $this->sexe = $data->sexe;
        $this->nom = $data->nom;
        $this->prenom = $data->prenom;
        $this->naissance = $data->naissance;
        $this->codeclub = $data->codeclub;
        $this->nomclub = $data->nomclub;
    }

    // Getters
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

    // Setters
    public function setMedaille(int $medaille)
    {
        $this->medaille = $medaille;
    }

    public function setTemps(string $temps)
    {
        $this->temps = $temps;
    }

    public function setSexe(string $sexe)
    {
        $this->sexe = $sexe;
    }

    public function setNom(string $nom)
    {
        $this->nom = $nom;
    }

    public function setPrenom(string $prenom)
    {
        $this->prenom = $prenom;
    }

    public function setNaissance(string $naissance)
    {
        $this->naissance = $naissance;
    }

    public function setCodeclub(string $codeclub)
    {
        $this->codeclub = $codeclub;
    }

    public function setNomclub(string $nomclub)
    {
        $this->nomclub = $nomclub;
    }

    // Implement JsonSerializable
    public function jsonSerialize(): array
    {
        return [
            'medaille' => $this->medaille,
            'temps' => $this->temps,
            'sexe' => $this->sexe,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'naissance' => $this->naissance,
            'codeclub' => $this->codeclub,
            'nomclub' => $this->nomclub,
        ];
    }
}