<?php

namespace App\Domain\Model\Volonteer\Repository;

use App\common\Repository\BaseRepository;
use PDO;
use PDOException;

class VolonteerRepository extends BaseRepository
{



    public function getRandoneurToPassCheckpoint(string $track_uid, string $checkpoint_uid ){

        try {
            $statement = $this->connection->prepare($this->sqls('participantToPassCheckpoint'));
            $statement->bindParam(':track_uid', $track_uid);
            $statement->bindParam(':checkpoint_uid', $checkpoint_uid);
            $statement->execute();
            $events = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,  \App\Domain\Model\Volonteer\ParticipantToPassCheckpoint::class, null);
           // $events = $statement->fetchAll();

            if (empty($events)) {
                return array();
            }

            return $events;
        }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }
        return array();

    }

    public function sqls($type)
    {
        $volonteer['participantToPassCheckpoint'] = 'select * from v_partisipant_to_pass_checkpoint e where track_uid=:track_uid and checkpoint_uid=:checkpoint_uid and passed=1;';
        return $volonteer[$type];
        // TODO: Implement sqls() method.
    }
}