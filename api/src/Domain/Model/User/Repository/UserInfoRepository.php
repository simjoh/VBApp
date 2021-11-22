<?php

namespace App\Domain\Model\User\Repository;

use App\common\Repository\BaseRepository;
use App\Domain\Model\User\Role;
use App\Domain\Model\User\UserInfo;
use PDO;
use PDOException;
use Ramsey\Uuid\Uuid;

class UserInfoRepository extends BaseRepository
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

    public function createUserInfo(UserInfo $userInfo, string $user_uid): UserInfo
    {
        try {
            $uid = Uuid::uuid4();
            $phone = $userInfo->getPhone();
            $email = $userInfo->getEmail();
            $stmt = $this->connection->prepare($this->sqls('createUserinfo'));
            $stmt->bindParam(':uid', $uid);
            $stmt->bindParam(':user_uid', $user_uid);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':email', $email);

            $stmt->execute();
        }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }

        return $userInfo;
    }

    public function userinfoFor(string $userUid): ?UserInfo {
        try {

            $statement = $this->connection->prepare($this->sqls('userInfoBuUserUid'));
            $statement->bindParam(':user_uid', $userUid);
            $statement->execute();
            $data = $statement->fetch();
            if(!empty($data)){
                return new UserInfo($data['user_uid'],$data['uid'],$data['phone'],$data['email']);
            }
        }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }

        return null;
    }


    public function sqls($type)
    {
        $usersqls['deleteUserInfoForUser'] = 'delete from user_info  where user_uid = :user_uid';
        $usersqls['deleteByUID'] = 'delete from user_info  where uid = :uid';
        $usersqls['userInfoByUid'] = 'select * from user_info  where uid = :uid';
        $usersqls['userInfoBuUserUid'] = 'select * from user_info  where user_uid = :user_uid';
        $usersqls['createUserinfo']  = "INSERT INTO user_info(uid, user_uid, phone, email) VALUES (:uid, :user_uid, :phone, :email)";
        return $usersqls[$type];
    }
}