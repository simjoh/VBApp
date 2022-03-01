<?php

namespace App\Domain\Model\Track\Repository;

use App\common\Repository\BaseRepository;
use App\Domain\Model\Track\Track;
use Exception;
use PDO;
use PDOException;
use Ramsey\Uuid\Uuid;

class TrackRepository extends BaseRepository
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


    public function allTracks(): array
    {
        try {
            $statement = $this->connection->prepare($this->sqls('allTracks'));
            $statement->execute();
            $tracks = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Track\Track::class, null);

            if (empty($tracks)) {
                return array();
            }
            foreach ($tracks as $x => $track) {
                $track_checkpoint_statement = $this->connection->prepare($this->sqls('getCheckpoints'));
                $trac_uid = $track->getTrackUid();
                $track_checkpoint_statement->bindParam(':track_uid', $trac_uid);
                $track_checkpoint_statement->execute();
                $trackss = $track_checkpoint_statement->fetchAll();
                if (!empty($trackss) && $track_checkpoint_statement->rowCount() > 0) {
                    $track_checkpoint_uids = [];
                    foreach ($trackss as $s => $trc) {
                        $track_checkpoint_uids = $trc;
                    }
                    $track->setCheckpoints($track_checkpoint_uids);
                }
            }
            return $tracks;

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return array();
    }

    public function getTrackByUid(string $trackUid): ?Track
    {
        try {
            $statement = $this->connection->prepare($this->sqls('trackByUid'));
            $statement->bindParam(':track_uid', $trackUid);
            $statement->execute();
            $tracks = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Track\Track::class, null);

            if($statement->rowCount() > 1){
                // Fixa bätter felhantering
                throw new Exception();
            }

            if(empty($tracks)){
                return null;
            }

            if(!empty($tracks)){
                $checkpoints = $this->checkpoints($tracks[0]->getTrackUid());
                $tracks[0]->setCheckpoints($checkpoints);
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return $tracks[0];
    }




    public function updateTrack(Track $track): Track
    {
        $track_uid = $track->getTrackUid();
        $distance = $track->getDistance();
        $title = $track->getTitle();
        $description = $track->getDescription();
        $event_uid = $track->getEventUid();
        $heightdifference = $track->getHeightdifference();
        $link = $track->getLink();
        try {
            $statement = $this->connection->prepare($this->sqls('updateTrack'));
            $statement->bindParam(':title', $title);
            $statement->bindParam(':heightdifference',$heightdifference );
            $statement->bindParam(':event_uid',$event_uid );
            $statement->bindParam(':description', $description);
            $statement->bindParam(':distance', $distance);
            $statement->bindParam(':track_uid', $track_uid);
            $statement->bindParam(':link', $link);
            $status =   $statement->execute();
        if($status){
                $sql = "UPDATE track_checkpoint SET checkpoint_uid=:checkpoint_uid WHERE track_uid=:track_uid";
                $query = $this->connection->prepare($sql);
                foreach ($track->getCheckpoints() as $s => $ro){
                    $query->bindparam(':checkpoint_uid', $ro);
                    $query->bindparam(':track_uid', $track_uid);
                    $query->execute();
                }
        }

        } catch (PDOException $e) {
            echo 'Kunde inte uppdatera Track: ' . $e->getMessage();
        }
        return $track;
    }

    public function createTrack(Track $track): Track
    {
        $track_uid = Uuid::uuid4()->toString();
        $distance = $track->getDistance();
        $title = $track->getTitle();
        $description = $track->getDescription();
        $event_uid = $track->getEventUid();
        $heightdifference = $track->getHeightdifference();
        $link = $track->getLink();

        try {
            $statement = $this->connection->prepare($this->sqls('createTrack'));
            $statement->bindParam(':title', $title);
            $statement->bindParam(':heightdifference',$heightdifference );
            $statement->bindParam(':event_uid',$event_uid );
            $statement->bindParam(':description', $description);
            $statement->bindParam(':distance', $distance);
            $statement->bindParam(':track_uid', $track_uid);
            $statement->bindParam(':link', $link);
            $data =   $statement->execute();
            if($data && !empty($track->getCheckpoints())){
                $query = $this->connection->prepare($this->sqls('createTrackCheckpoint'));
                foreach ($track->getCheckpoints() as $s => $ro){
                    $query->bindparam(':checkpoint_uid', $ro);
                    $query->bindparam(':track_uid', $track_uid);
                    $query->execute();
                }
            }
        } catch (PDOException $e) {
            echo 'Kunde inte uppdatera Track: ' . $e->getMessage();
        }
        $track->setTrackUid($track_uid);
        return $track;
    }




    public function checkpoints(string $track_uid): array
    {
        $track_checkpoint_statement = $this->connection->prepare($this->sqls('getCheckpoints'));
        $track_checkpoint_statement->bindParam(':track_uid', $track_uid);
        $track_checkpoint_statement->execute();
        $trackss = $track_checkpoint_statement->fetchAll();

        if (!empty($trackss) && $track_checkpoint_statement->rowCount() > 0) {
            $track_checkpoint_uids = [];
            foreach ($trackss as $s => $trc) {
                foreach ($trc as $s2 => $trc2){
                    $track_checkpoint_uids[] = $trc2;
                }

            }
            return $track_checkpoint_uids;
        }
        return array();
    }

    public function trackAndCheckpointsExists(string $getEventUid, string $getTitle, string $getDistance, array $getCheckpoints): ?Track
    {
        // inga checkpoints då förutsätts det som att de redan existerar
        if(empty($getCheckpoints)){
            return null;
        }


        try {
            $in  = str_repeat('?,', count($getCheckpoints) - 1) . '?';
            $sql = " SELECT * from track t inner join track_checkpoint tc on t.track_uid=tc.track_uid where  tc.checkpoint_uid  IN ($in);";

            $test = [];
            foreach ($getCheckpoints as $s => $ro){
                $test[] = $ro;
            }

            $statement = $this->connection->prepare($sql);
           $status = $statement->execute($test);
           if($status){
               return $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Track\Track::class,  null)[0];
           }

        }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }
        return null;

    }


    public function sqls($type)
    {
        $tracksqls['allTracks'] = 'select * from track t;';
        $tracksqls['trackByUid'] = 'select * from track where track_uid=:track_uid;';
        $tracksqls['getCheckpoints'] = 'select checkpoint_uid  from track_checkpoint where track_uid=:track_uid;';
        $tracksqls['updateTrack'] = "UPDATE track SET  title=:title, link=:link , heightdifference=:heightdifference, event_uid=:event_uid , description=:description, distance=:distance  WHERE track_uid=:track_uid";
        $tracksqls['updateTrackCheckpoint'] = 'UPDATE track_checkpoint SET checkpoint_uid=:checkpoint_uid where track_uid=:track_uid';
        $tracksqls['createTrack']  = "INSERT INTO track(track_uid, title ,link, heightdifference , event_uid,description, distance) VALUES (:track_uid,:title ,:link, :heightdifference ,:event_uid, :description ,:distance)";
        $tracksqls['createTrackCheckpoint']  = "INSERT INTO track_checkpoint(track_uid, checkpoint_uid) VALUES (:track_uid,:checkpoint_uid)";
        return $tracksqls[$type];
    }




}

