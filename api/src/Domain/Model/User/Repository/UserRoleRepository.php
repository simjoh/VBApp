<?php

namespace App\Domain\Model\User\Repository;

use App\common\Repository\BaseRepository;
use App\Domain\Model\User\Role;
use PDO;
use PDOException;
class UserRoleRepository extends BaseRepository
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


    public function deleteRoles($user_uid): void {
        try {
            $stmt = $this->connection->prepare($this->sqls('deleteRoles'));
            $stmt->bindParam(':user_uid', $user_uid);
            $stmt->execute();
        }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }
    }


    public function createUser(Role $roleTocreate, string $user_uid): Role
    {
        try {
            $role_id = $roleTocreate->getId();
            $stmt = $this->connection->prepare($this->sqls('createRoles'));
            $stmt->bindParam(':user_uid', $user_uid);
            $stmt->bindParam(':role_id', $role_id);

            $stmt->execute();
        }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }

        return $roleTocreate;
    }


    public function usersWithRole(int $role_id ): ?array
    {
        try {
            $stmt = $this->connection->prepare($this->sqls('usersWithRole'));
            $stmt->bindParam(':role_id', $role_id);

            $roles =$stmt->execute();

            $roleArray = [];
            if(!empty($roles)){
                foreach ($roles as $role) {
                    array_push($roleArray, $role['user_uid']);
                }
            }
        }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }

        return null;
    }

    public function sqls($type): string
    {
        $usersqls['deleteRoles'] = 'delete from user_role  where user_uid = :user_uid';
        $usersqls['rolesForUser'] = 'select * from user_role  where user_uid = :user_uid';
        $usersqls['usersWithRole'] = 'select * from user_role  where role_id = :role_id';
        $usersqls['createRoles']  = "INSERT INTO user_role(role_id, user_uid) VALUES (:role_id, :user_uid)";
        return $usersqls[$type];
    }
}