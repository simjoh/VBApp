<?php

namespace App\Domain\Model\Club;

use App\common\Repository\BaseRepository;
use PDO;
use PDOException;
use Ramsey\Uuid\Uuid;
use App\common\Exceptions\BrevetException;

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


    public function getClubByAcpCode(string $acpcode)
    {
        try {
            $statement = $this->connection->prepare($this->sqls('clubByAcpCode'));
            $statement->bindParam(':acpcode', $acpcode);
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

    public function createClub(Club $club): void
    {
        try {
            error_log("Creating club in database with ACP kod: " . $club->getAcpKod(), 0);
            $statement = $this->connection->prepare($this->sqls('createClub'));
            $club_uid = $club->getClubUid();
            $title = $club->getTitle();
            $acp_kod = $club->getAcpKod();
            
            error_log("Bound parameters - club_uid: $club_uid, title: $title, acp_kod: $acp_kod", 0);
            
            $statement->bindParam(':club_uid', $club_uid);
            $statement->bindParam(':title', $title);
            $statement->bindParam(':acp_kod', $acp_kod);
            
            $statement->execute();
            error_log("Club created successfully in database", 0);
        } catch (PDOException $e) {
            error_log("Error creating club in database: " . $e->getMessage(), 0);
            throw new BrevetException("Det gick inte att skapa klubben: " . $e->getMessage());
        }
    }


    public function updateClub(Club $club): void
    {
        try {
            error_log("Updating club in database with ACP kod: " . $club->getAcpKod(), 0);
            $statement = $this->connection->prepare($this->sqls('updateClub'));
            $club_uid = $club->getClubUid();
            $title = $club->getTitle();
            $acp_kod = $club->getAcpKod();
            
            error_log("Bound parameters - club_uid: $club_uid, title: $title, acp_kod: $acp_kod", 0);
            
            $statement->bindParam(':club_uid', $club_uid);
            $statement->bindParam(':title', $title);
            $statement->bindParam(':acp_kod', $acp_kod);
            
            $statement->execute();
            error_log("Club updated successfully in database", 0);
        } catch (PDOException $e) {
            error_log("Error updating club in database: " . $e->getMessage(), 0);
            throw new BrevetException("Det gick inte att uppdatera klubben: " . $e->getMessage());
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

    public function sqls($type): string
    {
        $clubsql['clubByUID'] = 'select * from club e where club_uid=:club_uid;';
        $clubsql['clubByTitle'] = 'select * from club e where title=:title;';
        $clubsql['clubByTitleLower'] = 'select * from club e where REPLACE(TRIM(lower(title))," ","")=:title;';
        $clubsql['allClubs'] = 'select * from club;';
        $clubsql['clubByAcpCode'] = 'select * from club e where acp_kod=:acpcode;';
        $clubsql['createClub'] = 'INSERT INTO club(club_uid, acp_kod, title) VALUES (:club_uid, :acp_kod, :title)';
        $clubsql['updateClub'] = 'UPDATE club set acp_kod=:acp_kod, title=:title where club_uid=:club_uid';
        $clubsql['deleteClub'] = 'DELETE FROM club WHERE club_uid = :club_uid';
        return $clubsql[$type];
    }
}