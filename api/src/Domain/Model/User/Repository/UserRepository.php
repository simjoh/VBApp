<?php

namespace App\Domain\Model\User\Repository;

use App\common\Repository\BaseRepository;
use App\Domain\Model\User\User;
use PDO;

class UserRepository extends BaseRepository
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

    public function authenticate($username,$password): ?User
    {

        $passwordsha =sha1($password);
        $statement = $this->connection->prepare($this->sqls('login'));
        $statement->bindParam(':user_name', $username, PDO::PARAM_STR);
        $statement->bindParam(':password', $passwordsha , PDO::PARAM_STR);
        $statement->execute();
        $user = $statement->fetch();

        if(empty($user)){
            return null;
        }

       return new User($user['user_uid'], $user['user_name'],$user['given_name'], $user['family_name'], '');


    }


    public function sqls($type): string
    {
        $usersqls['login'] = 'select * from users where user_name = :user_name and password = :password';

        return $usersqls[$type];
        // TODO: Implement sqls() method.
    }
}