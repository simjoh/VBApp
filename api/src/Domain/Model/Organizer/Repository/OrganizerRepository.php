<?php

namespace App\Domain\Model\Organizer\Repository;

use App\common\Repository\BaseRepository;
use PDO;
use PDOException;
use App\Domain\Model\Organizer\Organizer;

class OrganizerRepository extends BaseRepository
{

    /**
     * Constructor.
     *
     * @param PDO $connection The database connection
     */
    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function createOrganizer(Organizer $organizer): ?Organizer
    {
        $countsql = "select max(organizer_id) AS max_id from organizers";

        try {
            $stmtss = $this->connection->prepare($countsql);
            $stmtss->execute();
            $row = $stmtss->fetch(PDO::FETCH_ASSOC);
            $next_id = $row['max_id'] + 1;

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }

        $sql = "INSERT INTO organizers (organizer_id, name, contact_person ,email, phone, created_at, updated_at) 
                VALUES (:organizer_id, :name, :contactperson, :email, :phone, :created_at, :updated_at)";

        try {
            $stmt = $this->connection->prepare($sql);

            $name = $organizer->getName();
            $contactperson = $organizer->getContactPerson();
            $email = $organizer->getEmail();
            $phone = $organizer->getPhone();
            $createdat = getCreatedAt();
            $updatedat = getUpdatedAt();


            $stmt->bindParam(':organizer_id', $next_id);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':contactperson', $contactperson);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':created_at', $createdat);
            $stmt->bindParam(':updated_at', $updatedat);

            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                return new Organizer(
                    $row['organizer_id'],
                    $row['name'],
                    $row['contact_person'],
                    $row['email'],
                    $row['phone'],
                    $row['created_at'],
                    $row['updated_at']
                );
            }

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }

        return null;
    }

// Method to update the data in the database
    public
    function update(PDO $pdo)
    {
        $sql = "UPDATE organizers SET name = :name, contact_person = contactperson, email = :email, phone = :phone, updated_at = :updated_at
                WHERE organizer_id = :organizer_id";

        $stmt = $this->connection->prepare($sql);

        $stmt->bindParam(':organizer_id', $this->organizer_id);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':contactperson', $this->contact_person);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':updated_at', $this->updated_at);


        return $stmt->execute();
    }

// Method to delete the organizer
    public
    function delete($organizer_id)
    {
        $sql = "DELETE FROM organizers WHERE organizer_id = :organizer_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':organizer_id', $organizer_id);

        return $stmt->execute();
    }

// Method to get the organizer by ID
    public
    function getById($organizer_id)
    {
        $sql = "SELECT * FROM organizers WHERE organizer_id = :organizer_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':organizer_id', $organizer_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Organizer(
                $row['organizer_id'],
                $row['name'],
                $row['contact_person'],
                $row['email'],
                $row['phone'],
                $row['created_at'],
                $row['updated_at']
            );
        }
        return null;
    }

// Method to get all organizers
    public
    function getAll()
    {
        $sql = "SELECT * FROM organizers";
        $stmt = $this->connection->query($sql);

        $organizers = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $organizers[] = new Organizer(
                $row['organizer_id'],
                $row['name'],
                $row['contact_person'],
                $row['email'],
                $row['phone'],
                $row['created_at'],
                $row['updated_at']
            );
        }
        return $organizers;
    }

    public
    function sqls($type)
    {
        // TODO: Implement sqls() method.
    }
}