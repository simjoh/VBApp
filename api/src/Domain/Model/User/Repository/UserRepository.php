<?php

namespace App\Domain\Model\User\Repository;

use App\common\Repository\BaseRepository;
use App\Domain\Model\User\Rest\UserRepresentation;
use App\Domain\Model\User\User;
use PDO;
use PDOException;
use Ramsey\Uuid\Uuid;

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

    public function authenticate($username, $password): ?User
    {

        $passwordsha = sha1($password);
        $statement = $this->connection->prepare($this->sqls('login2'));
        $statement->bindParam(':user_name', $username, PDO::PARAM_STR);
        $statement->bindParam(':password', $passwordsha, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetch();

        if (empty($result)) {
            return null;
        }

        $user = new User();
        $user->setId($result['user_uid']);
        $user->setGivenname($result['given_name']);
        $user->setFamilyname($result['family_name']);
        $user->setUsername($result['user_name']);
        $user->setToken('');
        $user->setRoles(array($result['role_name']));
        return $user;
    }

    public function getAllUSers(): ?array
    {
        $statement = $this->connection->prepare($this->sqls('allUsers'));
        $statement->execute();
        $data = $statement->fetchAll();

        if (empty($data)) {
            return array();
        }
        $users = [];
        foreach ($data as $row) {
            $user = new User();
            $user->setId($row['user_uid']);
            $user->setGivenname($row['given_name']);
            $user->setFamilyname($row['family_name']);
            $user->setUsername($row['user_name']);
            $user->setToken('');

            array_push($users, $user);
        }
        return $users;
    }

    public function getUserById($id): ?User
    {
        $statement = $this->connection->prepare($this->sqls('getUserById'));
        $statement->bindParam(':user_uid', $id, PDO::PARAM_STR);
        $statement->execute();
        $data = $statement->fetch();
        if (empty($data)) {
            return null;
        }
        $user = new User();
        $user->setId($data['user_uid']);
        $user->setGivenname($data['given_name']);
        $user->setFamilyname($data['family_name']);
        $user->setFamilyname($data['user_name']);
        $user->setToken('');

        return $user;
    }

    public function updateUser($id ,User $userParsed): void
    {

        $data = [
            'givenname' => $userParsed->getGivenname(),
            'familyname' => $userParsed->getFamilyname(),
            'username' => $userParsed->getUsername(),
            'user_uid' => $userParsed->getId(),
        ];

        try {
            $statement = $this->connection->prepare($this->sqls('updateUser'));
            $statement->execute($data);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            echo 'Kunde inte uppdatera användare: ' . $e->getMessage();
        }
    }

    public function createUser(User $userTocreate): User {
        try {
        $user_uid = Uuid::uuid4();
        $familyname = $userTocreate->getFamilyname();
        $givenname = $userTocreate->getGivenname();
        $username = $userTocreate->getUsername();
        $password = sha1("test");
        $roleid = 1;
        $stmt = $this->connection->prepare($this->sqls('createUser'));
        $stmt->bindParam(':user_uid', $user_uid);
        $stmt->bindParam(':family_name',$familyname );
        $stmt->bindParam(':user_name', $username);
        $stmt->bindParam(':given_name', $givenname);
            $stmt->bindParam(':role_id', $roleid);
        $stmt->bindParam(':password', $password);
        $stmt->execute();
        }
        catch(PDOException $e)
             {
                 echo "Error: " . $e->getMessage();
              }

              $userTocreate->setId($user_uid);
              return $userTocreate;
    }

    public function deleteUser($user_uid): void {
        try {
        $stmt = $this->connection->prepare($this->sqls('deleteUser'));
        $stmt->bindParam(':user_uid', $user_uid);
        $stmt->execute();
        }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }
    }


    public function sqls($type): string
    {
        $usersqls['login'] = 'select * from users where user_name = :user_name and password = :password';
        $usersqls['login2'] = 'select * from users s left join roles r on r.role_id = s.role_id where user_name = :user_name and password = :password';
        $usersqls['allUsers'] = 'select * from users s;';
        $usersqls['getUserById'] = 'select * from users s where s.user_uid = :user_uid;';
        $usersqls['updateUser']  = "UPDATE users SET given_name=:givenname, family_name=:familyname, username=:username WHERE user_uid=:user_uid";
        $usersqls['createUser']  = "INSERT INTO users(user_uid, user_name, given_name, family_name, role_id, password) VALUES (:user_uid, :user_name, :given_name, :family_name, :role_id, :password)";
        $usersqls['deleteUser'] = 'delete from users  where user_uid = :user_uid';
        return $usersqls[$type];
    }
}