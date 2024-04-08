<?php

namespace App\Domain\Model\Loppservice\Rest;


use JsonSerializable;

class ClubRepresentation implements JsonSerializable
{
    public string $club_uid;
    public string $name;
    public string $description;
    public bool $official_club;

    public function jsonSerialize(): mixed
    {
        return [
            'club_uid' => $this->club_uid,
            'name' => $this->name,
            'description' => $this->description,
            'official_club' => $this->official_club
        ];
    }
}