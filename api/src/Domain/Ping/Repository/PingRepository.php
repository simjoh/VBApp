<?php

namespace App\Domain\Ping\Repository;

use App\common\Repository\BaseRepository;
use PDO;

class PingRepository extends BaseRepository
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

    public function ping(): bool
    {
        try {
            $this->connection->query($this->sqls());
        } catch (PDOException $e) {
            $bool = False;
            return $bool;
        }

        $bool = True;
        return $bool;
    }

    public function sqls():string
    {
        return "SELECT 1";
    }
}