<?php

namespace App\Domain\Model\User\Repository;

use App\common\Repository\BaseRepository;
use App\Domain\Model\User\Rest\UserRepresentation;
use App\Domain\Model\User\Role;
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
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function authenticate($username, $password): ?User
    {

        $passwordsha = sha1($password);
        $statement = $this->connection->prepare($this->sqls('login2'));
        $statement->bindParam(':user_name', $username, PDO::PARAM_STR);
        $statement->bindParam(':password', $passwordsha, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetchAll();
        if (empty($result)) {
            return null;
        }

        $roleArray = [];
        foreach ($result as $row) {
            array_push($roleArray, new Role(intval($row['role_id']), $row['role_name']));
        }

        $userdetails = $result[0];

        $user = new User();
        $user->setId($userdetails['user_uid']);
        $user->setGivenname($userdetails['given_name']);
        $user->setFamilyname($userdetails['family_name']);
        $user->setUsername($userdetails['user_name']);
        $user->setToken('');
        $user->setRoles(array($userdetails['role_name']));
        
        // Set organizer_id if it exists
        if (isset($userdetails['organizer_id'])) {
            $user->setOrganizerId($userdetails['organizer_id']);
        }
        
        // Set timestamp and confirmation fields
        if (isset($userdetails['created_at'])) {
            $user->setCreatedAt(new \DateTime($userdetails['created_at']));
        }
        if (isset($userdetails['updated_at'])) {
            $user->setUpdatedAt(new \DateTime($userdetails['updated_at']));
        }
        if (isset($userdetails['confirmed'])) {
            $user->setConfirmed((bool)$userdetails['confirmed']);
        }
        if (isset($userdetails['confirmed_at']) && $userdetails['confirmed_at']) {
            $user->setConfirmedAt(new \DateTime($userdetails['confirmed_at']));
        }
        
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
            
            // Set organizer_id if it exists
            if (isset($row['organizer_id'])) {
                $user->setOrganizerId($row['organizer_id']);
            }
            
            // Set timestamp and confirmation fields
            if (isset($row['created_at'])) {
                $user->setCreatedAt(new \DateTime($row['created_at']));
            }
            if (isset($row['updated_at'])) {
                $user->setUpdatedAt(new \DateTime($row['updated_at']));
            }
            if (isset($row['confirmed'])) {
                $user->setConfirmed((bool)$row['confirmed']);
            }
            if (isset($row['confirmed_at']) && $row['confirmed_at']) {
                $user->setConfirmedAt(new \DateTime($row['confirmed_at']));
            }

            $user_roles_stmt = $this->connection->prepare($this->sqls('roles'));
            $user_roles_stmt->bindParam(':user_uid', $row['user_uid']);
            $user_roles_stmt->execute();
            $roles = $user_roles_stmt->fetchAll();

            $roleArray = [];
            if(!empty($roles)){
                foreach ($roles as $role) {
                    array_push($roleArray, new Role($role["role_id"], $role['role_name']));
                }
                $user->setRoles($roleArray);
            }
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
        $user->setUsername($data['user_name']);
        $user->setToken('');
        
        // Set organizer_id if it exists
        if (isset($data['organizer_id'])) {
            $user->setOrganizerId($data['organizer_id']);
        }
        
        // Set timestamp and confirmation fields
        if (isset($data['created_at'])) {
            $user->setCreatedAt(new \DateTime($data['created_at']));
        }
        if (isset($data['updated_at'])) {
            $user->setUpdatedAt(new \DateTime($data['updated_at']));
        }
        if (isset($data['confirmed'])) {
            $user->setConfirmed((bool)$data['confirmed']);
        }
        if (isset($data['confirmed_at']) && $data['confirmed_at']) {
            $user->setConfirmedAt(new \DateTime($data['confirmed_at']));
        }

        $user_roles_stmt = $this->connection->prepare($this->sqls('roles'));
        $user_roles_stmt->bindParam(':user_uid', $data['user_uid']);
        $user_roles_stmt->execute();
        $roles = $user_roles_stmt->fetchAll();

        $roleArray = [];
        if(!empty($roles)){
            foreach ($roles as $role) {
                array_push($roleArray, new Role($role["role_id"], $role['role_name']));
            }
            $user->setRoles($roleArray);
        }

        return $user;
    }

    public function isVolonteer(int $roleId): bool {
        $statement = $this->connection->prepare($this->sqls('isRole'));
        $statement->bindParam(':role_id', $roleId, PDO::PARAM_INT);
        $statement->execute();
        $count = $statement->rowCount();
        if($count == 1){
            return true;
        }
        return false;
    }


    public function getUserRoles(string $user_uid): array {
        $statement = $this->connection->prepare($this->sqls('userRoles'));
        $statement->bindParam(':role_id', $user_uid, PDO::PARAM_INT);
        $statement->execute();
        $role_ids = $statement->fetchAll();
        $count = $statement->rowCount();
        $roleArray = [];
        if($count > 0){
            foreach ($role_ids as $role_id) {
                array_push($roleArray, $role_id);
            }
            return $roleArray;
        }
        return array();
    }

    public function updateUser($id ,User $userParsed): User
    {
        $data = [
            'givenname' => $userParsed->getGivenname(),
            'familyname' => $userParsed->getFamilyname(),
            'username' => $userParsed->getUsername(),
            'user_uid' => $id,  // Use the ID parameter instead of trying to get it from the User object
            'organizer_id' => $userParsed->getOrganizerId(),
        ];
        $setPassword = '';
        if ($userParsed->getPassword()) {
            $data['password'] = sha1($userParsed->getPassword());
            $setPassword = ', password=:password';
        }
        $setOrganizer = '';
        if ($userParsed->getOrganizerId() !== null) {
            $setOrganizer = ', organizer_id=:organizer_id';
        }
        try {
            // Only update updated_at timestamp, leave created_at unchanged
            $sql = "UPDATE users SET given_name=:givenname, family_name=:familyname, user_name=:username{$setPassword}{$setOrganizer}, updated_at=CURRENT_TIMESTAMP WHERE user_uid=:user_uid";
            $statement = $this->connection->prepare($sql);
            $statement->execute($data);
            
            // Set the updated timestamp in the object
            $userParsed->setUpdatedAt(new \DateTime());
        } catch (PDOException $e) {
            echo 'Kunde inte uppdatera användare: ' . $e->getMessage();
        }
        $userParsed->setId($id);
        return $userParsed;
    }

    public function createUser(User $userTocreate): User {
        try {
            $user_uid = Uuid::uuid4();
            $familyname = $userTocreate->getFamilyname();
            $givenname = $userTocreate->getGivenname();
            $username = $userTocreate->getUsername();
            $password = $userTocreate->getPassword() ? sha1($userTocreate->getPassword()) : sha1("test");
            $organizer_id = $userTocreate->getOrganizerId();
            
            $stmt = $this->connection->prepare($this->sqls('createUser'));
            $stmt->bindParam(':user_uid', $user_uid);
            $stmt->bindParam(':family_name',$familyname );
            $stmt->bindParam(':user_name', $username);
            $stmt->bindParam(':given_name', $givenname);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':organizer_id', $organizer_id);
            $stmt->execute();
            
            // Set the timestamps - both created_at and updated_at are set when creating
            $userTocreate->setId($user_uid);
            $userTocreate->setCreatedAt(new \DateTime());
            $userTocreate->setUpdatedAt(new \DateTime());
            $userTocreate->setConfirmed(false);
            
        }
        catch(PDOException $e)
             {
                 echo "Error: " . $e->getMessage();
              }

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

    public function confirmUser($user_uid): bool {
        try {
            $stmt = $this->connection->prepare($this->sqls('confirmUser'));
            $stmt->bindParam(':user_uid', $user_uid);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    public function confirmUserAndReturn($user_uid): ?User {
        try {
            $stmt = $this->connection->prepare($this->sqls('confirmUser'));
            $stmt->bindParam(':user_uid', $user_uid);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                // Return the updated user
                return $this->getUserById($user_uid);
            }
            return null;
        }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
            return null;
        }
    }


    public function sqls($type): string
    {
        $usersqls['login'] = 'select * from users where user_name = :user_name and password = :password';
        $usersqls['login2'] = 'select s.*, ru.role_name, ru.role_id from users s inner join user_role r on r.user_uid = s.user_uid inner join roles ru on ru.role_id = r.role_id  where s.user_name = :user_name and password = :password';
        $usersqls['allUsers'] = 'select * from users s;';
        $usersqls['getUserById'] = 'select * from users s where s.user_uid = :user_uid;';
        $usersqls['updateUser']  = "UPDATE users SET given_name=:givenname, family_name=:familyname, user_name=:username, organizer_id=:organizer_id, updated_at=CURRENT_TIMESTAMP WHERE user_uid=:user_uid";
        $usersqls['createUser']  = "INSERT INTO users(user_uid, user_name, given_name, family_name, password, organizer_id, created_at, updated_at, confirmed) VALUES (:user_uid, :user_name, :given_name, :family_name, :password, :organizer_id, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0)";
        $usersqls['deleteUser'] = 'delete from users  where user_uid = :user_uid';
        $usersqls['roles'] = 'select distinct(r.role_name) , r.role_id from user_role ur inner join roles r on r.role_id = ur.role_id  where ur.user_uid = :user_uid';
        $usersqls['isRole'] = 'select role_id from roles s where s.role_id = :role_id;';
        $usersqls['userRoles'] = 'select role_id from user_role s where s.role_id = :role_id;';
        $usersqls['confirmUser'] = 'UPDATE users SET confirmed=1, confirmed_at=CURRENT_TIMESTAMP WHERE user_uid=:user_uid';
        return $usersqls[$type];
    }
}