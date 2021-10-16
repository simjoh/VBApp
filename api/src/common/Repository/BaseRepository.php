<?php

namespace App\common\Repository;

use App\common\Database;
use PDO;

abstract class BaseRepository extends Database
{

    /**
     * @var PDO The database connection
     */
    public PDO $connection;

     function __construct(PDO $connection) {
        $this->connection = $connection;
    }

     abstract public function sqls();

     public function gets() :PDO{
         return $this::getConnection();
    }

}