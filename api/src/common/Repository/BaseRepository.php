<?php

namespace App\common\Repository;

use App\common\CurrentOrganizer;
use App\common\CurrentUser;
use App\common\Database;
use PDO;

abstract class BaseRepository extends Database
{

    /**
     * @var PDO The database connection
     */
    public PDO $connection;

    function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    abstract public function sqls($type);

    public function gets(): PDO
    {
        return $this::getConnection();
    }

    public function getOrganizer(): int
    {
        return CurrentOrganizer::getUser()->getOrganizerId();
    }

    public function getCreatedAt(): string
    {
        return date('Y-m-d H:i:s');

    }

    public function getUpdatedAt(): string
    {
        return date('Y-m-d H:i:s');
    }

    public function changedBy(): string
    {
        return CurrentUser::getUser()->getId();
    }
}