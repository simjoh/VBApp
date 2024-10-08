<?php

namespace App\Domain\Model\Club;

use App\common\Repository\BaseRepository;
use PDO;
use PDOException;
use Ramsey\Uuid\Uuid;

class ClubRepository extends BaseRepository
{





    /**
     * Constructor.
     *
     * @param PDO $connection The database connection
     */
    public function __construct(PDO $connection)
    {
         parent::__construct($connection);
        $this->connection = $connection;
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getAllClubs(): ?array
    {
        try {
        $statement = $this->connection->prepare($this->sqls('allClubs'));
        $statement->execute();
        $clubs = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Club::class, null);
        if (empty($clubs)) {
            return null;
        }
        return $clubs;

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return array();
    }


    public function getClubByUId(string $club_uid): ?Club
    {
        try {
            $statement = $this->connection->prepare($this->sqls('clubByUID'));
            $statement->bindParam(':club_uid', $club_uid);
            $statement->execute();
            $events = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Club\Club::class, null);

            if (empty($events)) {
                return null;
            }

            return $events[0];
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return null;

    }

    public function getClubByTitle(string $title): ?Club
    {
        try {
            $statement = $this->connection->prepare($this->sqls('clubByTitle'));
            $statement->bindParam(':title', $title);
            $statement->execute();

            $clubs = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Club\Club::class, null);

            if (empty($clubs)) {
                return null;
            }

            return $clubs[0];
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return null;

    }


    public function getClubByTitleLower(string $title): ?Club
    {
        try {

            $trimmmedandlover = strtolower(strlen($title));
            $statement = $this->connection->prepare($this->sqls('clubByTitleLower'));
            $statement->bindParam(':title', $trimmmedandlover);
            $statement->execute();

            $clubs = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Club\Club::class, null);

            if (empty($clubs)) {
                return null;
            }

            return $clubs[0];
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return null;

    }


    public function getClubByAcpKod(string $acpkod)
    {
        try {
            $acpint = intval($acpkod);
            $statement = $this->connection->prepare($this->sqls('clubByAcpkod'));
            $statement->bindParam(':acpkod', $acpint);
            $statement->execute();
            $events = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Club\Club::class, null);

            if (empty($events)) {
                return array();
            }

            return $events;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return array();

    }

    public function createClub(string $acp_kod, string $title): ?string
    {
        try {
            $club_uid = Uuid::uuid4();
            $acpkod = intval($acp_kod);
            $stmt = $this->connection->prepare($this->sqls('createClub'));
            $stmt->bindParam(':club_uid', $club_uid);
            $stmt->bindParam(':acpkod', $acpkod);
            $stmt->bindParam(':title', $title);
            $stmt->execute();


        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return $club_uid;
    }


    public function updateClub(Club $club): ?Club
    {
        try {
            $club_uid = $club->getClubUid();
            $acpkod = intval($club->getAcpKod());
            $title = $club->getTitle();
            $stmt = $this->connection->prepare($this->sqls('updateClub'));
            $stmt->bindParam(':club_uid', $club_uid);
            $stmt->bindParam(':acpkod', $acpkod);
            $stmt->bindParam(':title', $title);
            $status = $stmt->execute();

            if($status){
                return $club;
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return $club;
    }

    public function sqls($type)
    {
        $clubsql['clubByUID'] = 'select * from club e where club_uid=:club_uid;';
        $clubsql['clubByTitle'] = 'select * from club e where title=:title;';
        $clubsql['clubByTitleLower'] = 'select * from club e where REPLACE(TRIM(lower(title))," ","")=:title;';
        $clubsql['allClubs'] = 'select * from club;';
        $clubsql['clubByAcpkod'] = 'select * from club e where acp_kod=:acpkod;';
        $clubsql['createClub'] = 'INSERT INTO club(club_uid, acp_kod, title) VALUES (:club_uid, :acpkod, :title)';
        $clubsql['updateClub'] = 'UPDATE club set acp_kod=:acp_kod, title=:title where club_uid=:club_uid';
        return $clubsql[$type];
    }
}