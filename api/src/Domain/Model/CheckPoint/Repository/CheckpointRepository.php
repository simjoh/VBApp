<?php

namespace App\Domain\Model\Checkpoint\Repository;

use App\common\Repository\BaseRepository;
use App\Domain\Model\CheckPoint\Checkpoint;
use PDO;
use Opis\String\UnicodeString as wstring;
use PDOException;

class CheckpointRepository extends BaseRepository
{

    /**
     * Constructor.
     *
     * @param PDO $connection The database connection
     */
    public function __construct(PDO $connection) {
        $this->connection = $connection;
    }


    public function checkpointFor(string $checkpoint_uid) : ?Checkpoint{
        try {
            $statement = $this->connection->prepare($this->sqls('getCheckpointByTrackUid'));
            $statement->bindParam(':site_uid', $checkpoint_uid);
            $statement->execute();
             $checkpoint = $statement->fetch(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\CheckPoint\Checkpoint::class,  null);
             return $checkpoint;
        }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }
        return null;
    }

    public function checkpointsFor(array $checkpoints_uids) : array{
        try {
            $sql = $this->sqls('getCheckpointsFor');

            $in  = str_repeat('?,', count($checkpoints_uids) - 1) . '?';
            $sql = " SELECT * from checkpoint where checkpoint_uid  IN ($in)";

            $test = [];
            foreach ($checkpoints_uids as $s => $ro){
                $test[] = $ro;
            }
            $statement = $this->connection->prepare($sql);
            $statement->execute($test);
            $checkpoint = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\CheckPoint\Checkpoint::class,  null);

            return $checkpoint;
        }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }
        return array();
    }

    public function sqls($type)
    {
        $tracksqls['allChecpoints'] = 'select * from checkpoint c;';
        $tracksqls['getCheckpointByTrackUid'] = 'select checkpoint_uid  from track_checkpoint where track_uid=:track_uid;';
        $tracksqls['getCheckpointsFor'] = 'select * from checkpoint where checkpoint_uid in (?);';
        return $tracksqls[$type];
        // TODO: Implement sqls() method.
    }
}