<?php

namespace App\Domain\Model\Volonteer\Repository;

use App\common\Repository\BaseRepository;
use PDO;
use PDOException;

class VolonteerRepository extends BaseRepository
{



    public function getCheckpointsForTrack(string $track_uid){

        try {
            $statement = $this->connection->prepare($this->sqls('getCheckpointsForTrack'));
            $statement->bindParam(':track_uid', $track_uid);
            $statement->execute();
            // Changed to fetch as associative array since we only need checkpoint_uid
            $results = $statement->fetchAll(PDO::FETCH_ASSOC);
            if (empty($results)) {
                return array();
            }
            
            // Extract just the checkpoint_uid values
            $checkpoints = array_map(function($row) {
                return $row['checkpoint_uid'];
            }, $results);
            
            return array_unique($checkpoints);
        }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }
        return array();

    }

    public function getRandoneurToPassCheckpoint(string $track_uid, string $checkpoint_uid ){

        try {
            $statement = $this->connection->prepare($this->sqls('participantToPassCheckpoint'));
            $statement->bindParam(':track_uid', $track_uid);
            $statement->bindParam(':checkpoint_uid', $checkpoint_uid);
            $statement->execute();
            
            // Fetch as associative array first
            $results = $statement->fetchAll(PDO::FETCH_ASSOC);
            
            // Debug: Log the column names from the first row
            
            if (empty($results)) {
                return array();
            }
            
            // Create objects manually to avoid dynamic property creation
            $participants = [];
            foreach ($results as $row) {
                $participant = new \App\Domain\Model\Volonteer\ParticipantToPassCheckpoint();
                // Set properties explicitly based on the columns in your view
                $participant->setProperties($row);
                $participants[] = $participant;
            }
            
            return $participants;
        }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }
        return array();

    }

    public function sqls($type)
    {
        $volonteer['participantToPassCheckpoint'] = 'select * from v_partisipant_to_pass_checkpoint e where track_uid=:track_uid and checkpoint_uid=:checkpoint_uid;';
        $volonteer['getCheckpointsForTrack'] = 'select distinct checkpoint_uid from v_partisipant_to_pass_checkpoint e where e.track_uid=:track_uid;';
        return $volonteer[$type];
        // TODO: Implement sqls() method.
    }
}