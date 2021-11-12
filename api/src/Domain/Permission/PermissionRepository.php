<?php

namespace App\Domain\Permission;

use App\common\Repository\BaseRepository;
use PDO;
use PDOException;

class PermissionRepository extends BaseRepository
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


    public function getPermissionsFor(string $user_uid): ?array {
        try {
            $statement = $this->connection->prepare($this->sqls('permissionsfor'));
            $statement->bindParam(':user_uid', $user_uid);
            $statement->execute();
            $data = $statement->fetchAll();

            if(empty($data)){
                return array();
            }
            $permissionArray = [];
            foreach ($data as $x =>  $row) {
                array_push($permissionArray, (object) new Permission($row['perm_id'], $row['perm_mod'], $row['type'], $row['perm_desc']));
            }
            return $permissionArray;

        }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }
        return array();
    }

    public function getPermissionsForRole(int  $role_id): ?array {
        try {
            $statement = $this->connection->prepare($this->sqls('permissionsforrole'));
            $statement->bindParam(':role_id', $role_id);
            $statement->execute();
            $data = $statement->fetchAll();

            if(empty($data)){
                return array();
            }
            $permissionArray = [];
            foreach ($data as $x =>  $row) {
                array_push($permissionArray, (object) new Permission($row['perm_id'], $row['perm_mod'], $row['type'], $row['perm_desc']));
            }
            return $permissionArray;
        }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }
        return array();
    }

    public function getPermissionsTodata(string $datatype, string $user_uid): ?array {
        try {
            $statement = $this->connection->prepare($this->sqls('permissionstodata'));
            $statement->bindParam(':user_uid', $user_uid);
            $statement->bindParam(':data_type', $datatype);
            $statement->execute();
            $data = $statement->fetchAll();

            if(empty($data)){
                return array();
            }
            $permissionArray = [];
            foreach ($data as $x =>  $row) {
                array_push($permissionArray, (object) new Permission($row['perm_id'], $row['perm_mod'], $row['type'], $row['perm_desc']));
            }
            return $permissionArray;
        }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }
        return array();
    }

    public function sqls($type)
    {
        $permissionssqls['permissionsfor'] = 'select p.perm_id, rp.perm_mod, p.perm_desc, pt.type from user_role ur inner join roles_permissions rp on rp.role_id = ur.role_id inner join permissions p on p.perm_id = rp.perm_id inner join permission_type pt on pt.type_id = p.type_id where ur.user_uid = :user_uid';
        $permissionssqls['permissionsforrole'] = 'select rpm.perm_id, rpm.perm_mod , pm.perm_desc, pt.type from roles_permissions rpm inner join permissions pm on pm.perm_id = rpm.perm_id inner join permission_type pt on pt.type_id = pm.type_id where rpm.role_id = :role_id';
        $permissionssqls['permissionstodata'] = 'select rpm.perm_id, rpm.perm_mod , pm.perm_desc, pt.type from roles_permissions rpm inner join permissions pm on pm.perm_id = rpm.perm_id inner join permission_type pt on pt.type_id = pm.type_id inner join user_role ur on ur.role_id = rpm.role_id where ur.user_uid = :user_uid and pm.perm_desc = :data_type';
        return $permissionssqls[$type];
    }
}