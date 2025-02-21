<?php

namespace App\Domain\Model\Track\Repository;

use App\common\Exceptions\BrevetException;
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


    public function tracksOnEvent(?array $track_uids)
    {

        try {
            $in = str_repeat('?,', count($track_uids) - 1) . '?';
            $sql = "SELECT * from track where track_uid  IN ($in);";

            $test = [];
            foreach ($track_uids as $s => $ro) {
                $test[] = $ro;
            }

            $statement = $this->connection->prepare($sql);
            $statement->execute($test);

            $tracks = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Track\Track::class, null);
            return $tracks;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return array();
    }

    public function tracksbyEvent(string $event_uid)
    {

        try {
            $statement = $this->connection->prepare($this->sqls('tracksByEvent'));
            $statement->bindParam(':event_uid', $event_uid);
            $statement->execute();
            $tracks = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Track::class, null);
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

            if ($statement->rowCount() > 1) {
                // Fixa bätter felhantering
                throw new Exception();
            }

            if (empty($tracks)) {
                return null;
            }

            if (!empty($tracks)) {
                $checkpoints = $this->checkpoints($tracks[0]->getTrackUid());
                $tracks[0]->setCheckpoints($checkpoints);
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return $tracks[0];
    }

    public function isRacePassed(string $trackUid): bool
    {

        $statement = $this->connection->prepare($this->sqls('trackdatesPassed'));
        $statement->bindParam(':track_uid', $trackUid);
        $statement->execute();
        $row = $statement->fetch();

        if ($statement->rowCount() > 1) {
            throw new BrevetException("Should not b", 5, null);
        }

        $today = date('Y-m-d');
        $enddate = $row['closing'];
        $startdate = $row['opens'];

        if ($today > $enddate) {
            return true;
        }


        return false;


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
            $this->connection->beginTransaction();

            // Update track data
            $statement = $this->connection->prepare($this->sqls('updateTrack'));
            $statement->bindParam(':title', $title);
            $statement->bindParam(':heightdifference', $heightdifference);
            $statement->bindParam(':event_uid', $event_uid);
            $statement->bindParam(':description', $description);
            $statement->bindParam(':distance', $distance);
            $statement->bindParam(':track_uid', $track_uid);
            $statement->bindParam(':link', $link);
            $statement->execute();

            // Delete existing track-checkpoint associations
            $deleteStmt = $this->connection->prepare("DELETE FROM track_checkpoint WHERE track_uid = :track_uid");
            $deleteStmt->bindParam(':track_uid', $track_uid);
            $deleteStmt->execute();

            // Insert new track-checkpoint associations
            if (!empty($track->getCheckpoints())) {
                $insertStmt = $this->connection->prepare("INSERT IGNORE INTO track_checkpoint (track_uid, checkpoint_uid) VALUES (:track_uid, :checkpoint_uid)");
                foreach ($track->getCheckpoints() as $checkpoint_uid) {
                    $insertStmt->bindParam(':track_uid', $track_uid);
                    $insertStmt->bindParam(':checkpoint_uid', $checkpoint_uid);
                    $insertStmt->execute();
                }
            }

            $this->connection->commit();
        } catch (PDOException $e) {
            $this->connection->rollBack();
            error_log('Could not update Track: ' . $e->getMessage());
            throw new BrevetException("Failed to update track: " . $e->getMessage(), 5);
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
        $start_date_time = $track->getStartDateTime();

        try {
            $this->connection->beginTransaction();

            // Create track
            $statement = $this->connection->prepare($this->sqls('createTrack'));
            $statement->bindParam(':title', $title);
            $statement->bindParam(':heightdifference', $heightdifference);
            $statement->bindParam(':event_uid', $event_uid);
            $statement->bindParam(':description', $description);
            $statement->bindParam(':distance', $distance);
            $statement->bindParam(':track_uid', $track_uid);
            $statement->bindParam(':link', $link);
            $statement->bindParam(':start_date_time', $start_date_time);
            $statement->execute();

            // Insert track-checkpoint associations
            if (!empty($track->getCheckpoints())) {
                $insertStmt = $this->connection->prepare("INSERT IGNORE INTO track_checkpoint (track_uid, checkpoint_uid) VALUES (:track_uid, :checkpoint_uid)");
                foreach ($track->getCheckpoints() as $checkpoint_uid) {
                    $insertStmt->bindParam(':track_uid', $track_uid);
                    $insertStmt->bindParam(':checkpoint_uid', $checkpoint_uid);
                    $insertStmt->execute();
                }
            }

            $this->connection->commit();
            $track->setTrackUid($track_uid);
            return $track;
        } catch (PDOException $e) {
            $this->connection->rollBack();
            error_log('Could not create Track: ' . $e->getMessage());
            throw new BrevetException("Failed to create track: " . $e->getMessage(), 5);
        }
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
                foreach ($trc as $s2 => $trc2) {
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
        if (isset($getCheckpoints)) {
            return null;
        }

        try {
            $in = str_repeat('?,', count($getCheckpoints) - 1) . '?';
            $sql = " SELECT * from track t inner join track_checkpoint tc on t.track_uid=tc.track_uid where  tc.checkpoint_uid  IN ($in);";

            $test = [];
            foreach ($getCheckpoints as $s => $ro) {
                $test[] = $ro;
            }

            $statement = $this->connection->prepare($sql);
            $status = $statement->execute($test);
            if ($status) {
                return $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Track\Track::class, null)[0];
            }

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return null;

    }

    public function trackWithStartdateExists(string $getEventUid, string $getTitle, string $startDateTime): ?Track
    {

        try {

            $statement = $this->connection->prepare($this->sqls('trackWithStartdateExists'));
            $statement->bindParam(':event_uid', $getEventUid);
            $statement->bindParam(':title', $getTitle);
            $statement->bindParam(':start_date_time', $startDateTime);
            $statement->execute();
            $tracks = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Track\Track::class, null);

            if ($statement->rowCount() > 1) {
                // Fixa bätter felhantering
                throw new Exception();
            }
            if (empty($tracks)) {
                return null;
            }

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }


        return $tracks[0];
    }

    public function lastCheckpointOnTrack(string $trackUid): ?string {

        $track_checkpoint_statement = $this->connection->prepare($this->sqls('lastCheckpointOnTrack'));
        $track_checkpoint_statement->bindParam(':track_uid', $track_uid);
        $track_checkpoint_statement->execute();
        $lastcheckpointresult = $track_checkpoint_statement->fetch();

        if($lastcheckpointresult == null){
            return null;
        } else {
            return $lastcheckpointresult['adress'];
        }

    }


    public function deleteTrack(?string $track_uid)
    {

        try {
            $stmt = $this->connection->prepare($this->sqls('deleteTrack'));
            $stmt->bindParam(':track_uid', $track_uid);
            $stmt->execute();
        }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }
    }


    public function deleteTrackCheckpoint(array $checkpoints)
    {

        // inga checkpoints då förutsätts det som att de redan existerar
        if (isset($getCheckpoints)) {
            return null;
        }

        try {
            $in = str_repeat('?,', count($checkpoints) - 1) . '?';
            $sql = " DELETE from track_checkpoint where  track_uid  IN ($in);";

            $test = [];
            foreach ($checkpoints as $s => $ro) {
                $test[] = $ro;
            }

            $statement = $this->connection->prepare($sql);
            $status = $statement->execute($test);

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return null;
    }

    public function setInactive(?string $track_uid ,  $publish)

    {
        $active = $publish;
        try {
        $statement = $this->connection->prepare($this->sqls('setStatus'));
        $statement->bindParam(':active', $active, PDO::PARAM_BOOL);
        $statement->bindParam(':track_uid', $track_uid);
        $status = $statement->execute();

            if (!$status) {
                throw new BrevetException("", 1, null);
            }

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }


    public function sqls($type)
    {
        $tracksqls['allTracks'] = 'select * from track t;';
        $tracksqls['deleteTrack'] = 'delete from track  where track_uid=:track_uid;';
        $tracksqls['trackByUid'] = 'select * from track where track_uid=:track_uid;';
        $tracksqls['tracksByEvent'] = 'select * from track where event_uid=:event_uid;';
        $tracksqls['getCheckpoints'] = 'select checkpoint_uid  from track_checkpoint where track_uid=:track_uid;';
        $tracksqls['updateTrack'] = "UPDATE track SET  title=:title, link=:link , heightdifference=:heightdifference, event_uid=:event_uid , description=:description, distance=:distance  WHERE track_uid=:track_uid";
        $tracksqls['updateTrackCheckpoint'] = 'UPDATE track_checkpoint SET checkpoint_uid=:checkpoint_uid where track_uid=:track_uid';
        $tracksqls['createTrack']  = "INSERT INTO track(track_uid, title ,link, heightdifference , event_uid,description, distance, start_date_time) VALUES (:track_uid,:title ,:link, :heightdifference ,:event_uid, :description ,:distance, :start_date_time)";
        $tracksqls['trackWithStartdateExists']  = "select * from track where event_uid=:event_uid and title=:title and start_date_time=:start_date_time;";
        $tracksqls['trackdatesPassed']  = "SELECT max(c.distance) as distance ,min(c.opens) as opens , c.checkpoint_uid, max(c.closing) as closing FROM `track` t inner join track_checkpoint tc on tc.track_uid = t.track_uid inner join checkpoint c on c.checkpoint_uid = tc.checkpoint_uid where t.track_uid =:track_uid;";
        $tracksqls['lastCheckpointOnTrack']  =  "select s.adress from track t inner join track_checkpoint tc on tc.track_uid = t.track_uid inner join checkpoint c on c.checkpoint_uid = tc.checkpoint_uid inner join site s on s.site_uid = c.site_uid where tc.track_uid=:track_uid and c.distance in (select max(distance) from checkpoint);";
        $tracksqls['setStatus'] = "UPDATE track SET  active=:active WHERE track_uid=:track_uid";

        return $tracksqls[$type];
    }




}



