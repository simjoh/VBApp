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

    public function authenticate(): User
    {
        return new User(1, 'admin','Bengt', 'Hellström', '');


    }


    public function sqls()
    {
        // TODO: Implement sqls() method.
    }
}