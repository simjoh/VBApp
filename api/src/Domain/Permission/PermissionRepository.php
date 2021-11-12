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
                array_push($permissionArray, (object) new Permission($row['perm_id'], $row['perm_mod']));
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
        $sitesqls['permissionsfor'] = 'select p.perm_id, rp.perm_mod, p.perm_desc from user_role ur inner join roles_permissions rp on rp.role_id = ur.role_id inner join permissions p on p.perm_id = rp.perm_id where ur.user_uid = :user_uid';
        return $sitesqls[$type];
    }
}