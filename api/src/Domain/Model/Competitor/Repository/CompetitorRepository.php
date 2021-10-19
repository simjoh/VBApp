<?php

namespace App\Domain\Model\Competitor\Repository;


use App\common\Repository\BaseRepository;

use App\Domain\Model\Competitor\Competitor;
use PDO;

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

        return new Competitor($competitor['competitor_uid'],$competitor['user_name'], $competitor['given_name'],$competitor['family_name'], '');
    }


    public function sqls($type)
    {
        $competitorsqls['login'] = 'select * from competitors where user_name = :user_name and password = :password';
        return $competitorsqls[$type];

    }
}