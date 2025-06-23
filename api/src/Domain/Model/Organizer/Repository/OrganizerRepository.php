<?php

declare(strict_types=1);

namespace App\Domain\Model\Organizer\Repository;

use App\common\Repository\BaseRepository;
use App\Domain\Model\Organizer\Organizer;
use PDO;
use PDOException;

class OrganizerRepository extends BaseRepository
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

    public function getAllOrganizers(): ?array
    {
        try {
            $statement = $this->connection->prepare($this->sqls('allOrganizers'));
            $statement->execute();
            $data = $statement->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($data)) {
                return null;
            }
            
            $organizers = [];
            foreach ($data as $row) {
                $organizers[] = Organizer::fromArray($row);
            }
            
            return $organizers;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return array();
    }

    public function getOrganizerById(int $id): ?Organizer
    {
        try {
            $statement = $this->connection->prepare($this->sqls('organizerById'));
            $statement->bindParam(':id', $id);
            $statement->execute();
            $data = $statement->fetch(PDO::FETCH_ASSOC);

            if (empty($data)) {
                return null;
            }

            return Organizer::fromArray($data);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return null;
    }

    public function getOrganizerByClubUid(string $clubUid): ?Organizer
    {
        try {
            $statement = $this->connection->prepare($this->sqls('organizerByClubUid'));
            $statement->bindParam(':club_uid', $clubUid);
            $statement->execute();
            $data = $statement->fetch(PDO::FETCH_ASSOC);

            if (empty($data)) {
                return null;
            }

            return Organizer::fromArray($data);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return null;
    }

    public function createOrganizer(Organizer $organizer): ?Organizer
    {
        try {
            $statement = $this->connection->prepare($this->sqls('createOrganizer'));
            $organizationName = $organizer->getOrganizationName();
            $description = $organizer->getDescription();
            $website = $organizer->getWebsite();
            $websitePay = $organizer->getWebsitePay();
            $logoSvg = $organizer->getLogoSvg();
            $contactPersonName = $organizer->getContactPersonName();
            $email = $organizer->getEmail();
            $active = $organizer->isActive();
            $clubUid = $organizer->getClubUid();

            $statement->bindParam(':organization_name', $organizationName);
            $statement->bindParam(':description', $description);
            $statement->bindParam(':website', $website);
            $statement->bindParam(':website_pay', $websitePay);
            $statement->bindParam(':logo_svg', $logoSvg);
            $statement->bindParam(':contact_person_name', $contactPersonName);
            $statement->bindParam(':email', $email);
            $statement->bindParam(':active', $active, PDO::PARAM_BOOL);
            $statement->bindParam(':club_uid', $clubUid);
            $statement->execute();

            $id = (int)$this->connection->lastInsertId();
            return $this->getOrganizerById($id);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return null;
    }

    public function updateOrganizer(Organizer $organizer): ?Organizer
    {
        try {
            $statement = $this->connection->prepare($this->sqls('updateOrganizer'));
            $id = $organizer->getId();
            $organizationName = $organizer->getOrganizationName();
            $description = $organizer->getDescription();
            $website = $organizer->getWebsite();
            $websitePay = $organizer->getWebsitePay();
            $logoSvg = $organizer->getLogoSvg();
            $contactPersonName = $organizer->getContactPersonName();
            $email = $organizer->getEmail();
            $active = $organizer->isActive();
            $clubUid = $organizer->getClubUid();

            $statement->bindParam(':id', $id, PDO::PARAM_INT);
            $statement->bindParam(':organization_name', $organizationName);
            $statement->bindParam(':description', $description);
            $statement->bindParam(':website', $website);
            $statement->bindParam(':website_pay', $websitePay);
            $statement->bindParam(':logo_svg', $logoSvg);
            $statement->bindParam(':contact_person_name', $contactPersonName);
            $statement->bindParam(':email', $email);
            $statement->bindParam(':active', $active, PDO::PARAM_BOOL);
            $statement->bindParam(':club_uid', $clubUid);
            $statement->execute();

            return $this->getOrganizerById($organizer->getId());
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return null;
    }

    public function deleteOrganizer(int $id): bool
    {
        try {
            $statement = $this->connection->prepare($this->sqls('deleteOrganizer'));
            $statement->bindParam(':id', $id);
            return $statement->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return false;
    }

    /**
     * Get the database connection
     * 
     * @return PDO The database connection
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }

    public function sqls($type)
    {
        $sqls = [
            'allOrganizers' => 'SELECT * FROM organizers ORDER BY organization_name',
            'organizerById' => 'SELECT * FROM organizers WHERE id = :id',
            'organizerByClubUid' => 'SELECT * FROM organizers WHERE club_uid = :club_uid',
            'createOrganizer' => 'INSERT INTO organizers (organization_name, description, website, website_pay, logo_svg, contact_person_name, email, active, club_uid) 
                                VALUES (:organization_name, :description, :website, :website_pay, :logo_svg, :contact_person_name, :email, :active, :club_uid)',
            'updateOrganizer' => 'UPDATE organizers 
                                SET organization_name = :organization_name,
                                    description = :description,
                                    website = :website,
                                    website_pay = :website_pay,
                                    logo_svg = :logo_svg,
                                    contact_person_name = :contact_person_name,
                                    email = :email,
                                    active = :active,
                                    club_uid = :club_uid
                                WHERE id = :id',
            'deleteOrganizer' => 'DELETE FROM organizers WHERE id = :id'
        ];

        return $sqls[$type] ?? '';
    }
} 