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

    /**
     * Constructor.
     *
     * @param PDO $connection The database connection
     */
    public function __construct(PDO $connection, ParticipantRepository $participantRepository)
    {
        parent::__construct($connection);
        $this->connection = $connection;
        $this->participantRepository = $participantRepository;
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

        $participant = $this->participantRepository->participantFor($credok['participant_uid']);


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

    public function createCompetitorFromLoppservice(string $givenName, string $familyName, string $userName, string $birthdate, string $person_uid): Competitor
    {
        $this->connection->beginTransaction();
        try {
            $uid = $person_uid;
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


    public function sqls($type)
    {
        $competitorsqls['login'] = 'select * from competitors where user_name = :user_name and password = :password';
        $competitorsqls['competitor_by_uid'] = 'select * from competitors where competitor_uid = :competitor_uid';
        $competitorsqls['participant_credential'] = 'select * from competitor_credential where user_name = :user_name and password = :password';
        $competitorsqls['delete_credential'] = 'delete from competitor_credential where participant_uid=:participant_uid and competitor_uid=:competitor_uid';
        $competitorsqls['getbynameandbirth'] = 'select * from competitors where given_name=:givenname and family_name=:familyname and birthdate=:birthdate;';
        $competitorsqls['createCompetitor'] = "INSERT INTO competitors(competitor_uid, user_name, given_name, family_name, role_id, password, birthdate) VALUES (:uid, :username, :givenname, :familyname, :role_id, :password , :birthdate)";
        $competitorsqls['createCompetitorCredential'] = "INSERT INTO competitor_credential(credential_uid, competitor_uid, participant_uid, user_name, password) VALUES (:uid, :competitor_uid, :participant_uid, :user_name, :password)";
        return $competitorsqls[$type];

    }

}