<?php

namespace App\common\Repository;

use App\common\CurrentOrganizer;
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

     abstract public function sqls($type);

     public function gets() :PDO{
         return $this::getConnection();
    }

    public function getOrganizer(): int
    {
         return CurrentOrganizer::getUser()->getOrganizerId();
    }

}