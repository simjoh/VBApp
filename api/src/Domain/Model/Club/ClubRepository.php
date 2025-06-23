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

    public function getClubByTitle(?string $title): ?Club
    {
        try {
            if ($title === null) {
                return null;
            }
            
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


    public function getClubByTitleLower(?string $title): ?Club
    {
        try {
            if ($title === null) {
                return null;
            }

            $trimmmedandlover = strtolower(trim($title));
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
            $statement = $this->connection->prepare($this->sqls('clubByAcpkod'));
            $statement->bindParam(':acpkod', $acpkod);
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

    public function createClub(?string $acp_kod, ?string $title): ?string
    {
        try {
            $club_uid = Uuid::uuid4();
            $stmt = $this->connection->prepare($this->sqls('createClub'));
            $stmt->bindParam(':club_uid', $club_uid);
            $stmt->bindParam(':acp_kod', $acp_kod);
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
            $acpkod = $club->getAcpKod();
            $title = $club->getTitle();
            
            // Debug logging
            error_log("REPO DEBUG: Updating club " . $club_uid);
            error_log("REPO DEBUG: ACP kod value: " . ($club->getAcpKod() ?? 'null'));
            error_log("REPO DEBUG: Title: " . $title);
            error_log("REPO DEBUG: SQL: " . $this->sqls('updateClub'));
            
            $stmt = $this->connection->prepare($this->sqls('updateClub'));
            $stmt->bindParam(':club_uid', $club_uid);
            $stmt->bindParam(':acp_kod', $acpkod);
            $stmt->bindParam(':title', $title);
            $status = $stmt->execute();
            
            error_log("REPO DEBUG: SQL execution status: " . ($status ? 'true' : 'false'));

            if($status){
                return $club;
            } else {
                throw new PDOException("Failed to update club in database");
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            throw $e; // Re-throw to let the service handle the transaction rollback
        }
    }

    public function deleteClub(string $club_uid): bool
    {
        try {
            $stmt = $this->connection->prepare($this->sqls('deleteClub'));
            $stmt->bindParam(':club_uid', $club_uid);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Get the database connection for transaction handling
     * 
     * @return PDO
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }

    public function sqls($type)
    {
        $clubsql['clubByUID'] = 'select * from club e where club_uid=:club_uid;';
        $clubsql['clubByTitle'] = 'select * from club e where title=:title;';
        $clubsql['clubByTitleLower'] = 'select * from club e where REPLACE(TRIM(lower(title))," ","")=:title;';
        $clubsql['allClubs'] = 'select * from club;';
        $clubsql['clubByAcpkod'] = 'select * from club e where acp_kod=:acpkod;';
        $clubsql['createClub'] = 'INSERT INTO club(club_uid, acp_kod, title) VALUES (:club_uid, :acp_kod, :title)';
        $clubsql['updateClub'] = 'UPDATE club set acp_kod=:acp_kod, title=:title where club_uid=:club_uid';
        $clubsql['deleteClub'] = 'DELETE FROM club WHERE club_uid = :club_uid';
        return $clubsql[$type];
    }
}