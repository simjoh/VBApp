<?php

namespace App\Domain\Model\Competitor\Repository;


use App\common\Repository\BaseRepository;

use App\Domain\Model\Competitor\Competitor;
use Exception;
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
    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }


    public function authenticate($usernamne, $password): ?Competitor
    {

        $passwordsha =sha1($password);
        $statement = $this->connection->prepare($this->sqls('login'));
        $statement->bindParam(':user_name', $usernamne, PDO::PARAM_STR);
        $statement->bindParam(':password', $passwordsha , PDO::PARAM_STR);
        $statement->execute();
        $competitor = $statement->fetch();

        if(empty($competitor)){
            return null;
        }

        $competitor = new Competitor($competitor['competitor_uid'],$competitor['user_name'], $competitor['given_name'],$competitor['family_name'], '');
        $competitor->setRoles(array("COMPETITOR"));
        return $competitor;
    }

    public function getCompetitorByNameAndBirthDate(string $givenname, string $familyname, string $birthdate ) {

        try {

            $statement = $this->connection->prepare($this->sqls('getbynameandbirth'));

            $statement->bindParam(':givenname', $givenname);
            $statement->bindParam(':familyname', $familyname);
           $statement->bindParam(':birthdate', $birthdate);
            $statement->execute();
            ;
            $competitor = $statement->fetch();
            $statement->rowCount();
            if($statement->rowCount() > 0){
              return  new Competitor($competitor['competitor_uid'],$competitor['user_name'], $competitor['given_name'],$competitor['family_name'], '');
            }

            if(!empty($event)){
                return new Competitor($competitor['competitor_uid'],$competitor['user_name'], $competitor['given_name'],$competitor['family_name'], '');
            }
        }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }

        return null;

    }

    public function createCompetitor(string $givenName, string $familyName, string $userName, string $birthdate): Competitor {

        try {
            $uid = Uuid::uuid4();
            $role_id = 4;
            $password = sha1("pass");
            $stmt = $this->connection->prepare($this->sqls('createCompetitor'));
            $stmt->bindParam(':familyname', $familyName);
            $stmt->bindParam(':username',$userName );
            $stmt->bindParam(':givenname', $givenName);
            $stmt->bindParam(':birthdate', $birthdate);
            $stmt->bindParam(':role_id', $role_id);
            $stmt->bindParam(':uid', $uid);
            $stmt->bindParam(':password', $uid);
            $stmt->execute();
        }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }

        return  new Competitor($uid,$userName,$givenName,$familyName,"");
    }


    public function sqls($type)
    {
        $competitorsqls['login'] = 'select * from competitors where user_name = :user_name and password = :password';
        $competitorsqls['getbynameandbirth'] = 'select * from competitors where given_name=:givenname and family_name=:familyname and birthdate=:birthdate;';
        $competitorsqls['createCompetitor']  = "INSERT INTO competitors(competitor_uid, user_name, given_name, family_name, role_id, password, birthdate) VALUES (:uid, :username, :givenname, :familyname, :role_id, :password , :birthdate)";
        return $competitorsqls[$type];

    }
}