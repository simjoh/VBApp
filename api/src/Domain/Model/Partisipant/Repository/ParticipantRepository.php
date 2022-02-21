<?php

namespace App\Domain\Model\Partisipant\Repository;
use App\common\Repository\BaseRepository;
use App\Domain\Model\Event\Event;
use App\Domain\Model\Partisipant\Participant;
use Exception;
use PDO;
use PDOException;
use Ramsey\Uuid\Uuid;

class ParticipantRepository extends BaseRepository
{

    /**
     * Constructor.
     *
     * @param PDO $connection The database connection
     */
    public function __construct(PDO $connection) {
        $this->connection = $connection;
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }


    public function allParticipants(): ?array {
        try {
            $statement = $this->connection->prepare($this->sqls('allParticipantsOnTrack'));
            $statement->bindParam(':track_uid', $track_uid);
            $statement->execute();
            $events = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,  \App\Domain\Model\Partisipant\Participant::class, null);

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


    public function getPArticipantsByTrackUids($track_uids): ?array {
            try {
                $in  = str_repeat('?,', count($track_uids) - 1) . '?';
                $sql = "select *  from participant  where  track_uid IN ($in)";
                $test = [];


                foreach ($track_uids as $s => $ro){

                    $test[] = $ro["track_uid"];
                }

                $statement = $this->connection->prepare($sql);
                $statement->execute($test);
                $checkpoint = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,  \App\Domain\Model\Partisipant\Participant::class, null);

                return $checkpoint;
            }
            catch(PDOException $e)
            {
                echo "Error: " . $e->getMessage();
            }
            return array();

    }

    public function participantFor(string $participant_uid): ?Participant
    {
        try {

            $statement = $this->connection->prepare($this->sqls('getEventByUid'));
            $statement->bindParam(':participant_uid', $participant_uid);
            $statement->execute();
            $event = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Event\Event::class, null);

            if($statement->rowCount() > 1){
                // Fixa bätter felhantering
                throw new Exception();
            }
            if(!empty($event)){
                return $event[0];
            }
        }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }

        return null;
    }

    public function participantsOnTrack(string $track_uid) {
        try {
            $statement = $this->connection->prepare($this->sqls('allParticipantsOnTrack'));
            $statement->bindParam(':track_uid', $track_uid);
            $statement->execute();
            $events = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,  \App\Domain\Model\Partisipant\Participant::class, null);

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

    public function participantsOnTrackDns(string $track_uid) {
        try {
            $dns = true;
            $statement = $this->connection->prepare($this->sqls('dns'));
            $statement->bindParam(':track_uid', $track_uid);
            $statement->bindParam(':dns', $dns);
            $statement->execute();
            $events = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,  \App\Domain\Model\Partisipant\Participant::class, null);

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

    public function participantsOnTrackDnf(string $track_uid) {
        try {
            $dnf = true;
            $statement = $this->connection->prepare($this->sqls('dnf'));
            $statement->bindParam(':track_uid', $track_uid);
            $statement->bindParam(':dnf', $dnf);
            $statement->execute();
            $events = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,  \App\Domain\Model\Partisipant\Participant::class, null);

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


    public function participantsbyTrackAndClub(string $track_uid, $club_uid) {
        try {
            $statement = $this->connection->prepare($this->sqls('allParticipants'));
            $statement->bindParam(':track_uid', $track_uid);
            $statement->bindParam(':club_uid', $club_uid);
            $statement->execute();
            $events = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,  \App\Domain\Model\Partisipant\Participant::class, null);

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

    public function participantsForCompetitor(string $competitor_uid) {
        try {
            $statement = $this->connection->prepare($this->sqls('allParticipants'));
            $statement->bindParam(':$competitor_uid', $competitor_uid);
            $statement->execute();
            $events = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,  \App\Domain\Model\Partisipant\Participant::class, null);

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

    public function participantOntRackAndStartNumber(string $track_uid, $startnumber): ?Participant {
        try {

            $statement = $this->connection->prepare($this->sqls('participantonTrackWithStartnumber'));
            $statement->bindParam(':track_uid', $track_uid);
            $statement->bindParam(':startnumber', $startnumber);
            $statement->execute();

            $event = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Partisipant\Participant::class, null);

            if($statement->rowCount() > 1){
                // Fixa bätter felhantering

                throw new Exception();
            }
            if(!empty($event)){

                return $event[0];
            }
        }
        catch(PDOException $e)
        {

            echo "Error: " . $e->getMessage();
        }

        return null;
    }


    public function createparticipant(Participant $participanttoCreate): ?Participant {
        try {
            $participant_uid = Uuid::uuid4();
            $track_uid = $participanttoCreate->getTrackUid();
            $competitor_uid = $participanttoCreate->getCompetitorUid();
            $startnumber = $participanttoCreate->getStartnumber();
            $finished = $participanttoCreate->isFinished();
            $acpkod = $participanttoCreate->getAcpcode();
            $club_uid = $participanttoCreate->getClubUid();
            $dns = $participanttoCreate->isDns();
            $dnf = $participanttoCreate->isDnf();
            $brevenr = $participanttoCreate->getBrevenr();
            $time = $participanttoCreate->getTime();
            $null = null;
            $stmt = $this->connection->prepare($this->sqls('createSite'));
            $stmt->bindParam(':participant_uid', $participant_uid);
            $stmt->bindParam(':track_uid',$track_uid );
            $stmt->bindParam(':competitor_uid',$competitor_uid);
            $stmt->bindParam(':startnumber', $startnumber);
            $stmt->bindParam(':finished', $finished);
            $stmt->bindParam(':acpcode', $acpkod);
            $stmt->bindParam(':club_uid', $club_uid);
            $stmt->bindParam(':dns', $dns);
            $stmt->bindParam(':time', $time);
            $stmt->bindParam(':dnf', $dnf);
            $stmt->bindParam(':brevenr', $brevenr);


            $stmt->execute();
        }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }

        return $participanttoCreate;
    }

    public function updateParticipant(Participant $participantToUpdate): ?Participant {
        $participant_uid = $participantToUpdate->getParticipantUid();
        $track_uid = $participantToUpdate->getTrackUid();
        $competitor_uid = $participantToUpdate->getCompetitorUid();
        $startnumber = $participantToUpdate->getStartnumber();
        $finished = $participantToUpdate->isFinished();
        $acpkod = $participantToUpdate->getAcpcode();
        $club_uid = $participantToUpdate->getClubUid();
        $dns = $participantToUpdate->isDns();
        $dnf = $participantToUpdate->isDnf();
        $brevenr = $participantToUpdate->getBrevenr();
        $time = $participantToUpdate->getTime();
        try {
            $stmt = $this->connection->prepare($this->sqls('updateParticipant'));
            $stmt->bindParam(':participant_uid', $participant_uid);
            $stmt->bindParam(':track_uid',$track_uid );
            $stmt->bindParam(':competitor_uid',$competitor_uid);
            $stmt->bindParam(':startnumber', $startnumber);
            $stmt->bindParam(':finished', $finished);
            $stmt->bindParam(':acpcode', $acpkod);
            $stmt->bindParam(':club_uid', $club_uid);
            $stmt->bindParam(':dns', $dns);
            $stmt->bindParam(':time', $time);
            $stmt->bindParam(':dnf', $dnf);
            $stmt->bindParam(':brevenr', $brevenr);

            $status = $stmt->execute();
            if($status){
                return $participantToUpdate;
            }
        } catch (PDOException $e) {
            echo 'Kunde inte uppdatera site: ' . $e->getMessage();
        }
        return $event;
    }


    //görs i efterhand
    public function updateBrevenr(string $brevenr, string $participant_uid): bool {

        $participant_uid = $participant_uid;
        $brevenr = $brevenr;
        try {
            $stmt = $this->connection->prepare($this->sqls('updateBrevenr'));
            $stmt->bindParam(':participant_uid', $participant_uid);
            $stmt->bindParam(':brevenr',$brevenr );
            $status = $stmt->execute();
            if($status){
                return true;
            }
        } catch (PDOException $e) {
            echo 'Kunde inte uppdatera site: ' . $e->getMessage();
        }
        return $event;
    }



    public function sqls($type)
    {
        $eventqls['allParticipants'] = 'select * from participant e;';
        $eventqls['dns'] = 'select *  from participant e where e.track_uid=:track_uid and dns=:dns;';
        $eventqls['dnf'] = 'select *  from participant e where e.track_uid=:track_uid and dnf=:dnf;';
        $eventqls['allParticipantsOnTrack'] = 'select *  from participant e where e.track_uid=:track_uid;';
        $eventqls['allParticipantsForCompetitor'] = 'select *  from participant e where e.competitor_uid=:competitor_uid;';
        $eventqls['participantonTrackWithStartnumber'] = 'select *  from participant e where e.track_uid=:track_uid and startnumber=:startnumber;';
        $eventqls['participantByTrackAndClub'] = 'select *  from participant e where e.track_uid=:track_uid and club_uid=:club_uid;';
        $eventqls['deleteParticipant'] = 'delete from participant  where participant_uid=:participant_uid;';
        $eventqls['updateParticipant']  = "UPDATE participant SET  track_uid=:track_uid , competitor_uid=:competitor_uid , startnumber=:startnumber, finished=:finished, acpkod=:acpcode, club_uid=:club_uid , dns=:dns, dnf=:dnf WHERE participant_uid=:participant_uid";
        $eventqls['updateBrevenr']  = "UPDATE participant SET  brevenr=:brevenr WHERE participant_uid=:participant_uid";
        $eventqls['createParticipant'] = 'INSERT INTO participant(participant_uid, track_uid, competitor_uid, startnumber, finished, acpkod, club_uid ,time,dns, dnf, brevenr) VALUES (:participant_uid, :track_uid,:competitor_uid,:startnumber,:finished, :canceled, :acpcode, :club_uid, :time, :dns, :dnf, :brevenr)';
        return $eventqls[$type];
        // TODO: Implement sqls() method.
    }
}