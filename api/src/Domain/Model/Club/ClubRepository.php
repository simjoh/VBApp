<?php

namespace App\Domain\Model\Club;

use PDO;
use PDOException;
use Ramsey\Uuid\Uuid;

class ClubRepository extends \App\common\Repository\BaseRepository
{


    /**
     * Constructor.
     *
     * @param PDO $connection The database connection
     */
    public function __construct(PDO $connection) {
        $this->connection = $connection;
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }


    public function getClubByUId(string $club_uid) {
        try {
            $statement = $this->connection->prepare($this->sqls('clubByUID'));
            $statement->bindParam(':club_uid', $club_uid);
            $statement->execute();
            $events = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,  \App\Domain\Model\Club\Club::class, null);

            if (empty($events)) {
                return array();
            }

            return $events;
        }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }
        return array();

    }

    public function getClubByTitle(string $title): ?Club {
        try {
            $statement = $this->connection->prepare($this->sqls('clubByTitle'));
            $statement->bindParam(':title', $title);
            $statement->execute();

            $clubs = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,  \App\Domain\Model\Club\Club::class, null);

            if (empty($clubs)) {
                return null;
            }

            return $clubs[0];
        }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }
        return null;

    }


    public function getClubByAcpKod(string $acpkod) {
        try {
            $acpint = intval($acpkod);
            $statement = $this->connection->prepare($this->sqls('clubByAcpkod'));
            $statement->bindParam(':acpkod', $acpint);
            $statement->execute();
            $events = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,  \App\Domain\Model\Club\Club::class, null);

            if (empty($events)) {
                return array();
            }

            return $events;
        }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }
        return array();

    }

    public function createClub(string $acp_kod, string $title): ?string {
        try {
            $club_uid = Uuid::uuid4();
            $acpkod = intval($acp_kod);
            $stmt = $this->connection->prepare($this->sqls('createClub'));
            $stmt->bindParam(':club_uid', $club_uid);
            $stmt->bindParam(':acpkod',$acpkod );
            $stmt->bindParam(':title', $title);
            $stmt->execute();
        }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }
        return $club_uid;
    }

    public function sqls($type)
    {
        $clubsql['clubByUID'] = 'select * from club e where club_uid=:club_uid;';
        $clubsql['clubByTitle'] = 'select * from club e where title=:title;';
        $clubsql['clubByAcpkod'] = 'select * from club e where acp_kod=:acpkod;';
        $clubsql['createClub'] = 'INSERT INTO club(club_uid, acp_kod, title) VALUES (:club_uid, :acpkod, :title)';
        return $clubsql[$type];
    }
}