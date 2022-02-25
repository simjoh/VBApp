<?php

namespace App\Domain\Model\Checkpoint\Repository;

use App\common\Repository\BaseRepository;
use App\Domain\Model\CheckPoint\Checkpoint;
use Exception;
use PDO;
use PDOException;
use Ramsey\Uuid\Uuid;

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


    public function allCheckpoints(): ?array {
        try {
            $statement = $this->connection->prepare($this->sqls('allChecpoints'));
            $statement->execute();
            $checkpoints = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\CheckPoint\Checkpoint::class, null);
            if (empty($checkpoints)) {
                return array();
            }
            return $checkpoints;
        }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }
        return null;
    }

    public function updateCheckpoint($checkpoint_uid ,Checkpoint $checkpoint): Checkpoint {

        $checkpoint_uid = $checkpoint->getCheckpointUid();
        $site_uid = $checkpoint->getSiteUid();
        $title = $checkpoint->getTitle();
        $distance = $checkpoint->getDistance();
        $description = $checkpoint->getDescription();
        if(!empty($checkpoint->getOpens())){
            $opens = strtotime("g:i a",$checkpoint->getOpens());
        } else {
            $closing = null;
        }
        if(!empty($checkpoint->getClosing())){
            $closing = strtotime("g:i a",$checkpoint->getClosing());
        } else {
            $closing = null;
        }
        try {
            $statement = $this->connection->prepare($this->sqls('updateCheckpoint'));
            $statement->bindParam(':checkpoint_uid', $checkpoint_uid);
            $statement->bindParam(':site_uid',$site_uid );
            $statement->bindParam(':title', $title);
            $statement->bindParam(':distance', $distance);
            $statement->bindParam(':description', $description);
            $statement->bindParam(':opens', $opens);
            $statement->bindParam(':closing', $closing);
            $statement->execute();
        } catch (PDOException $e) {
            echo 'Kunde inte uppdatera site: ' . $e->getMessage();
        }

        return $checkpoint;
    }

    public function createCheckpoint($checkpoint_uid, Checkpoint $checkpoint): Checkpoint {
        try {
            $checkpoint_uid = Uuid::uuid4();
            $site_uid = $checkpoint->getSiteUid();
            $title = $checkpoint->getTitle();
            $distance = $checkpoint->getDistance();
            $description = $checkpoint->getDescription();
            if(!empty($checkpoint->getOpens())){
                $opens = strtotime("g:i a",$checkpoint->getOpens());
            } else {
                $closing = null;
            }
            if(!empty($checkpoint->getClosing())){
                $closing = strtotime("g:i a",$checkpoint->getClosing());
            } else {
                $closing = null;
            }

            $stmt = $this->connection->prepare($this->sqls('createCheckpoint'));
            $stmt->bindParam(':checkpoint_uid', $checkpoint_uid);
            $stmt->bindParam(':site_uid',$site_uid );
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':distance', $distance);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':opens', $opens);
            $stmt->bindParam(':closing', $closing);
            $stmt->execute();
        }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }
        return $checkpoint;
    }


    public function checkpointFor(string $checkpoint_uid) : ?Checkpoint{

        try {
            $statement = $this->connection->prepare($this->sqls('getCheckpointByUID'));
            $statement->bindParam(':checkpoint_uid', $checkpoint_uid);
            $statement->execute();
            $checkpoint = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\CheckPoint\Checkpoint::class, null);

            if($statement->rowCount() > 1){

                // Fixa bätter felhantering
                throw new Exception();
            }
            if(!empty($checkpoint)){

                return $checkpoint[0];
            }
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
            $sql = " SELECT * from checkpoint where checkpoint_uid  IN ($in) order by opens asc";

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

    public function checkpointUidsForTrack(string $track_uid) : array{

        try {
            $statement = $this->connection->prepare($this->sqls('getCheckpointByTrackUid'));
            $statement->bindParam(':track_uid', $track_uid);
            $statement->execute();
            $checkpoints = $statement->fetchAll(\PDO::FETCH_ASSOC);
            if (empty($checkpoints)) {

                return array();
            }
            $checkpoint_uids = [];

            foreach ($checkpoints as $s => $trc) {

                array_push($checkpoint_uids,$trc["checkpoint_uid"]);
            }

            return $checkpoint_uids;
        }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }
        return array();
    }

    public function deleteCheckpoint(string $checkpoint_uid): void {
        try {
            $stmt = $this->connection->prepare($this->sqls('deleteCheckpoint'));
            $stmt->bindParam(':checkpoint_uid', $checkpoint_uid);
            $stmt->execute();
        }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }
    }



    public function stamp(){


    }

    public function sqls($type)
    {
        $tracksqls['allChecpoints'] = 'select * from checkpoint c;';
        $tracksqls['getCheckpointByUID'] = 'select *  from checkpoint t where t.checkpoint_uid=:checkpoint_uid;';
        $tracksqls['getCheckpointsFor'] = 'select * from checkpoint where checkpoint_uid in (?);';
        $tracksqls['createCheckpoint']  = "INSERT INTO checkpoint(checkpoint_uid, site_uid, title, description, distance, opens, closing) VALUES (:checkpoint_uid, :site_uid,:title,:description,:distance, :opens,:closing)";
        $tracksqls['updateCheckpoint']  = "UPDATE checkpoint SET  title=:title , site_uid=:site_uid description=:description , distance=:distance, opens=:opens, closing=:closing  WHERE checkpoint_uid=:checkpoint_uid";
        $tracksqls['deleteCheckpoint'] = 'delete from checkpoint c where c.checkpoint_uid=:checkpoint_uid;';
        $tracksqls['getCheckpointByTrackUid'] = 'select checkpoint_uid from track_checkpoint where track_uid=:track_uid;';
        return $tracksqls[$type];
        // TODO: Implement sqls() method.
    }
}