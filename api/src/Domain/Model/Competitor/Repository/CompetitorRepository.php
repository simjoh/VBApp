<?php

namespace App\Domain\Model\Competitor\Repository;


use App\common\Repository\BaseRepository;
use App\Domain\Model\Competitor\Competitor;
use App\Domain\Model\Partisipant\Repository\ParticipantRepository;
use PDO;
use PDOException;
use Ramsey\Uuid\Uuid;

class CompetitorRepository extends BaseRepository
{

    public ParticipantRepository $participantrepository;
    public PDO $connection;
    /**
     * Constructor.
     *
     * @param PDO $connection The database connection
     */
    public function __construct(PDO $connection, ParticipantRepository $participantRepositoryy)
    {
        parent::__construct($connection);
        $this->connection = $connection;
        $this->participantrepository = $participantRepositoryy;
    }


    public function authenticate($usernamne, $password): ?Competitor
    {

        $passwordsha = sha1($password);
        $statement = $this->connection->prepare($this->sqls('login'));
        $statement->bindParam(':user_name', $usernamne, PDO::PARAM_STR);
        $statement->bindParam(':password', $passwordsha, PDO::PARAM_STR);
        $statement->execute();
        $competitor = $statement->fetch();

        if (empty($competitor)) {
            return null;
        }

        $competitor = new Competitor($competitor['competitor_uid'], $competitor['user_name'], $competitor['given_name'], $competitor['family_name'], '');
        $competitor->setRoles(array("COMPETITOR"));
        return $competitor;
    }

    public function authenticate2($usernamne, $password): ?Competitor
    {

        $passwordsha = sha1($password);
        $statement = $this->connection->prepare($this->sqls('participant_credential'));
        $statement->bindParam(':user_name', $usernamne, PDO::PARAM_STR);
        $statement->bindParam(':password', $passwordsha, PDO::PARAM_STR);
        $statement->execute();
        $credok = $statement->fetch();

        if (empty($credok)) {
            return null;
        }

        $participant = $this->participantrepository->participantFor($credok['participant_uid']);


        $stmt = $this->connection->prepare($this->sqls('competitor_by_uid'));
        $stmt->bindParam(':competitor_uid', $credok['competitor_uid'], PDO::PARAM_STR);
        $status = $stmt->execute();
        $competitor = $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Competitor\Competitor::class, null);

        if ($status) {
            if ($stmt->rowCount() > 0) {
                $competitor[0]->setStartnumber($credok['user_name']);
                $competitor[0]->setTrackuid($participant->getTrackUid());
                $competitor[0]->setRoles(array("COMPETITOR"));
                return $competitor[0];
            } else {
                return null;
            }
        }

        return null;
    }


    public function getCompetitorByNameAndBirthDate(string $givenname, string $familyname, string $birthdate)
    {
        try {

            $statement = $this->connection->prepare($this->sqls('getbynameandbirth'));
            $statement->bindParam(':givenname', $givenname);
            $statement->bindParam(':familyname', $familyname);
            $statement->bindParam(':birthdate', $birthdate);
            $statement->execute();;
            $competitor = $statement->fetch();
            $statement->rowCount();
            if ($statement->rowCount() > 0) {
                $competitor = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Competitor\Competitor::class, null);
                return $competitor[0];
            }
            if (!empty($event)) {
                $competitor = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Competitor\Competitor::class, null);
                return $competitor[0];
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }

        return null;

    }

    public function createCompetitor(string $givenName, string $familyName, string $userName, string $birthdate): Competitor
    {

        try {
            $uid = Uuid::uuid4();
            $role_id = 4;
            $password = sha1("pass");
            $stmt = $this->connection->prepare($this->sqls('createCompetitor'));
            $stmt->bindParam(':familyname', $familyName);
            $stmt->bindParam(':username', $userName);
            $stmt->bindParam(':givenname', $givenName);
            $stmt->bindParam(':birthdate', $birthdate);
            $stmt->bindParam(':role_id', $role_id);
            $stmt->bindParam(':uid', $uid);
            $stmt->bindParam(':password', $uid);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return $this->getCompetitorByUID($uid);
    }

    public function createCompetitorFromLoppservice(string $givenName, string $familyName, string $userName, string $birthdate, string $person_uid, $gender): Competitor
    {
        $this->connection->beginTransaction();
        try {
            $uid = $person_uid;
            $role_id = 4;
            $intgender = intval($gender);
            $password = sha1("pass");
            $stmt = $this->connection->prepare($this->sqls('createCompetitor'));
            $stmt->bindParam(':familyname', $familyName);
            $stmt->bindParam(':username', $userName);
            $stmt->bindParam(':givenname', $givenName);
            $stmt->bindParam(':birthdate', $birthdate);
            $stmt->bindParam(':gender', $intgender);
            $stmt->bindParam(':role_id', $role_id);
            $stmt->bindParam(':uid', $uid);
            $stmt->bindParam(':password', $uid);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }

        $this->connection->commit();

        return $this->getCompetitorByUID($person_uid);


    }

    public function creatCompetitorCredential(string $competitor_uid, string $participant_uid, string $user_name, string $password)
    {
        try {
            $uid = Uuid::uuid4();
            $shapass = sha1($password);
            $statement = $this->connection->prepare($this->sqls('createCompetitorCredential'));
            $statement->bindParam(':uid', $uid);
            $statement->bindParam(':competitor_uid', $competitor_uid);
            $statement->bindParam(':participant_uid', $participant_uid);
            $statement->bindParam(':user_name', $user_name);
            $statement->bindParam(':password', $shapass);
            $statement->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }


    public function getCompetitorByUID(string $competitor_uid)
    {

        $stmt = $this->connection->prepare($this->sqls('competitor_by_uid'));
        $stmt->bindParam(':competitor_uid', $competitor_uid, PDO::PARAM_STR);
        $status = $stmt->execute();
        $competitor = $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Competitor\Competitor::class, null);

        if ($status) {
            if ($stmt->rowCount() > 0) {
                return $competitor[0];
            } else {
                return null;
            }
        }
        $competitor[0]->setRoles(array("COMPETITOR"));
        return $competitor;
    }

    public function getCompetitorByUID2(string $competitor_uid)
    {

        try {
            $stmt = $this->connection->prepare($this->sqls('competitor_by_uid'));
            $stmt->bindParam(':competitor_uid', $competitor_uid, PDO::PARAM_STR);
            $status = $stmt->execute();


            $competitor = $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Competitor\Competitor::class, null);

            if ($status) {
                if ($stmt->rowCount() > 0) {
                    return $competitor[0];
                } else {
                    return null;
                }
            }
            $competitor[0]->setRoles(array("COMPETITOR"));

        } catch (PDOException $e) {
            echo 'Kunde inte uppdatera competitor_info: ' . $e->getMessage();
        }
        return $competitor[0];
    }


    public function deleteCompetitorCredentialForParticipant(string $participantUid, string $competitor_uid)
    {


        try {
            $stmt = $this->connection->prepare($this->sqls('delete_credential'));
            $stmt->bindParam(':participant_uid', $participantUid);
            $stmt->bindParam(':competitor_uid', $competitor_uid);
            $stmt->execute();

            return $stmt->rowCount();
        } catch (PDOException $e) {

            echo "Error: " . $e->getMessage();
        }

        return 0;

    }

    public function getPasswordByParticipantUid(string $participantUid): ?string
    {
        try {
            $stmt = $this->connection->prepare($this->sqls('get_password_by_participant'));
            $stmt->bindParam(':participant_uid', $participantUid);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result && isset($result['password'])) {
                return $result['password'];
            }
            
            return null;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return null;
        }
    }

    public function generateAndStoreRefNrForParticipant(string $participantUid, string $newStartNumber): string
    {
        try {
            // Generate a new reference number (5 digits like in loppservice)
            $refNr = mt_rand(10000, 99999);
            
            // Check if this ref_nr already exists in our database (as password hash)
            $hashedRefNr = sha1($refNr);
            $stmt = $this->connection->prepare($this->sqls('check_ref_nr_exists'));
            $stmt->bindParam(':ref_nr', $hashedRefNr);
            $stmt->execute();
            
            // If it exists, generate a new one
            while ($stmt->rowCount() > 0) {
                $refNr = mt_rand(10000, 99999);
                $hashedRefNr = sha1($refNr);
                $stmt->bindParam(':ref_nr', $hashedRefNr);
                $stmt->execute();
            }
            
            // Hash the ref_nr before storing it as password (SHA1 like the original system)
            $hashedRefNr = sha1($refNr);
            
            // Store the hashed ref_nr in the competitor_credential table
            $stmt = $this->connection->prepare($this->sqls('update_ref_nr_for_participant'));
            $stmt->bindParam(':participant_uid', $participantUid);
            $stmt->bindParam(':ref_nr', $hashedRefNr);
            $stmt->execute();
            
            // Also update the user_name to match the new start number
            $stmt = $this->connection->prepare($this->sqls('update_username_for_participant'));
            $stmt->bindParam(':participant_uid', $participantUid);
            $stmt->bindParam(':username', $newStartNumber);
            $stmt->execute();
            
            return $refNr;
        } catch (PDOException $e) {
            error_log("Error generating and storing ref_nr: " . $e->getMessage());
            // Return a fallback ref_nr if there's an error
            return '99999';
        }
    }


    public function sqls($type)
    {
        $competitorsqls['login'] = 'select * from competitors where user_name = :user_name and password = :password';
        $competitorsqls['competitor_by_uid'] = 'select * from competitors where competitor_uid = :competitor_uid';
        $competitorsqls['participant_credential'] = 'select * from competitor_credential where user_name = :user_name and password = :password';
        $competitorsqls['delete_credential'] = 'delete from competitor_credential where participant_uid=:participant_uid and competitor_uid=:competitor_uid';
        $competitorsqls['get_password_by_participant'] = 'select password from competitor_credential where participant_uid=:participant_uid';
        $competitorsqls['check_ref_nr_exists'] = 'select 1 from competitor_credential where password=:ref_nr limit 1';
        $competitorsqls['update_ref_nr_for_participant'] = 'update competitor_credential set password=:ref_nr where participant_uid=:participant_uid';
        $competitorsqls['update_username_for_participant'] = 'update competitor_credential set user_name=:username where participant_uid=:participant_uid';
        $competitorsqls['getbynameandbirth'] = 'select * from competitors where given_name=:givenname and family_name=:familyname and birthdate=:birthdate;';
        $competitorsqls['createCompetitor'] = "INSERT INTO competitors(competitor_uid, user_name, given_name, family_name, role_id, password, birthdate, gender) VALUES (:uid, :username, :givenname, :familyname, :role_id, :password , :birthdate, :gender)";
        $competitorsqls['createCompetitorCredential'] = "INSERT INTO competitor_credential(credential_uid, competitor_uid, participant_uid, user_name, password) VALUES (:uid, :competitor_uid, :participant_uid, :user_name, :password)";
        return $competitorsqls[$type];

    }

}