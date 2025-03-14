<?php

namespace App\Domain\Model\Checkpoint\Repository;

use App\common\Repository\BaseRepository;
use App\Domain\Model\CheckPoint\Checkpoint;
use DateTime;
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
                if($checkpoint->getOpens() != null && $checkpoint->getOpens() != '-'){
                 //   $opens =  date('Y-m-d H:i:s',floatval(date('Y-m-d') . $checkpoint->getOpens()));
                    $opens =  $checkpoint->getOpens();
                }
            } else {
                $closing = null;
            }
            if(!empty($checkpoint->getClosing())){
                if($checkpoint->getClosing() != null && $checkpoint->getClosing() != '-'){
                    $closing =    $checkpoint->getClosing();
                  //  $closing =   date('Y-m-d H:i:s',floatval($checkpoint->getClosing()));
                }

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
        $checkpoint->setCheckpointUid($checkpoint_uid);
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

    public function isStartCheckpoin(array $checkpoints_uids) : ?string{
        try {


            $in  = str_repeat('?,', count($checkpoints_uids) - 1) . '?';
            $sql = " SELECT  min(distance), checkpoint_uid from checkpoint where checkpoint_uid  IN ($in) group by distance";

            $test = [];
            foreach ($checkpoints_uids as $s => $ro){
                $test[] = $ro;
            }

            $statement = $this->connection->prepare($sql);
            $statement->execute($test);
            $checkpoint = $statement->fetch();
            return $checkpoint[1];
        }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }
        return null;
    }

    public function isEndCheckpoin(array $checkpoints_uids) : ?string{
        try {

            $in  = str_repeat('?,', count($checkpoints_uids) - 1) . '?';
            $sql = " SELECT  max(distance), checkpoint_uid from checkpoint where checkpoint_uid  IN ($in) group by checkpoint_uid order by distance desc ";

            $test = [];
            foreach ($checkpoints_uids as $s => $ro){
                $test[] = $ro;
            }
            $statement = $this->connection->prepare($sql);
            $statement->execute($test);
            $checkpoint = $statement->fetch();

            return $checkpoint[1];
        }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }
        return null;
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

    public function countCheckpointsForTrack(string $track_uid) : int{

        try {
            $statement = $this->connection->prepare($this->sqls('countCheckpointforTrack'));
            $statement->bindParam(':track_uid', $track_uid);
            $statement->execute();
            $count = $statement->fetchColumn();
            if (empty($count)) {
                return 0;
            }

            return $count;
        }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }
        return 0;
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

    public function existsBySiteUidAndDistance(string $getSiteUid,  $getDistance, $opens, $closing)
    {
        try {
            $dist = $getDistance;
            $statement = $this->connection->prepare($this->sqls('existsBySiteUidAndDistance'));
            $statement->bindParam(':site_uid', $getSiteUid);
            $statement->bindParam(':distance', $dist, PDO::PARAM_STR);
            $statement->bindParam(':opens', $opens, PDO::PARAM_STR);
            $statement->bindParam(':closing', $closing, PDO::PARAM_STR);
            $statement->execute();
            $checkpoints = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\CheckPoint\Checkpoint::class, null);
            if (empty($checkpoints)) {
                return array();
            }
            // Antar att det redan finns
            return $checkpoints[0];
        }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }
        return null;
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
        $tracksqls['deleteCheckpoint'] = 'delete from checkpoint  where checkpoint_uid=:checkpoint_uid;';
        $tracksqls['getCheckpointByTrackUid'] = 'select checkpoint_uid from track_checkpoint where track_uid=:track_uid;';
        $tracksqls['countCheckpointforTrack'] = 'select count(checkpoint_uid) from track_checkpoint where track_uid=:track_uid;';
        $tracksqls['existsBySiteUidAndDistance'] = 'select * from checkpoint e where e.site_uid=:site_uid and e.distance=:distance and opens=:opens and closing=:closing;';
        $tracksqls['addCheckpointToTrack'] = 'INSERT INTO track_checkpoint(track_uid, checkpoint_uid) VALUES (:track_uid, :checkpoint_uid)';
        return $tracksqls[$type];
    }

    public function addCheckpointToTrack(string $track_uid, string $checkpoint_uid): void
    {
        try {
            $stmt = $this->connection->prepare($this->sqls('addCheckpointToTrack'));
            $stmt->bindParam(':track_uid', $track_uid);
            $stmt->bindParam(':checkpoint_uid', $checkpoint_uid);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

}