<?php

namespace App\Domain\Model\Partisipant\Repository;

use App\common\Exceptions\BrevetException;
use App\common\Repository\BaseRepository;
use App\Domain\Model\Partisipant\Participant;
use App\Domain\Model\Partisipant\ParticipantCheckpoint;
use Exception;
use PDO;
use PDOException;
use PrestaShop\Decimal\DecimalNumber;
use Ramsey\Uuid\Uuid;

class ParticipantRepository extends BaseRepository
{


    /**
     * Constructor.
     *
     * @param PDO $connection The database connection
     */
    public function __construct(PDO $connection)
    {
        parent::__construct($connection);
        $this->connection = $connection;
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }


    public function allParticipants(): ?array
    {
        try {
            $statement = $this->connection->prepare($this->sqls('allParticipantsOnTrack'));
            $statement->bindParam(':track_uid', $track_uid);
            $statement->execute();
            $events = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Partisipant\Participant::class, null);

            if (empty($events)) {
                return array();
            }

            return $events;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return array();
    }


    public function getPArticipantsByTrackUids($track_uids): ?array
    {

        try {
            $in = str_repeat('?,', count($track_uids) - 1) . '?';
            $sql = "select *  from participant  where  track_uid IN ($in)";
            $test = [];


            foreach ($track_uids as $s => $ro) {

                $test[] = $ro;
            }


            $statement = $this->connection->prepare($sql);
            $statement->execute($test);
            $checkpoint = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Partisipant\Participant::class, null);
            return $checkpoint;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return array();

    }

    public function participantFor(string $participant_uid): ?Participant
    {
        try {

            $statement = $this->connection->prepare($this->sqls('participantByUID'));
            $statement->bindParam(':participant_uid', $participant_uid);
            $statement->execute();
            $event = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Partisipant\Participant::class, null);

            if ($statement->rowCount() > 1) {
                // Fixa bätter felhantering
                throw new BrevetException("Error fetching participant", 1, null);
            }
            if (!empty($event)) {
                return $event[0];
            }
        } catch (PDOException $e) {
            throw new BrevetException("Error fetching participant", 1, null);
            //  echo "Error: " . $e->getMessage();
        }

        return null;
    }

    public function participantForTrackAndCompetitor(string $track_uid, string $competitor_uid): ?Participant
    {
        try {

            $statement = $this->connection->prepare($this->sqls('participantByTrackAndCompetitorUid'));
            $statement->bindParam(':track_uid', $track_uid);
            $statement->bindParam(':competitor_uid', $competitor_uid);
            $statement->execute();
            $event = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Partisipant\Participant::class, null);

            if ($statement->rowCount() > 1) {
                // Fixa bätter felhantering
                throw new BrevetException("Error fetching participants", 1, null);
            }
            if (!empty($event)) {
                return $event[0];
            }
        } catch (PDOException $e) {
            throw new BrevetException("Error fetching participant", 1, null);
            // echo "Error: " . $e->getMessage();
        }

        return null;
    }

    public function participantsOnTrack(string $track_uid)
    {
        try {
            $statement = $this->connection->prepare($this->sqls('allParticipantsOnTrack'));
            $statement->bindParam(':track_uid', $track_uid);
            $statement->execute();
            $events = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Partisipant\Participant::class, null);

            if (empty($events)) {
                return array();
            }

            return $events;
        } catch (PDOException $e) {
            throw new BrevetException("Error fetching participants", 1, null);
            //  echo "Error: " . $e->getMessage();
        }


    }

    public function participantsOnTrackDns(string $track_uid)
    {
        try {
            $dns = true;
            $statement = $this->connection->prepare($this->sqls('dns'));
            $statement->bindParam(':track_uid', $track_uid);
            $statement->bindParam(':dns', $dns);
            $statement->execute();
            $events = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Partisipant\Participant::class, null);

            if (empty($events)) {
                return array();
            }

            return $events;
        } catch (PDOException $e) {
            throw new BrevetException("Error set dns for participant", 1, null);

        }

    }

    public function participantsOnTrackDnf(string $track_uid)
    {
        try {
            $dnf = true;
            $statement = $this->connection->prepare($this->sqls('dnf'));
            $statement->bindParam(':track_uid', $track_uid);
            $statement->bindParam(':dnf', $dnf);
            $statement->execute();
            $events = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Partisipant\Participant::class, null);

            if (empty($events)) {
                return array();
            }

            return $events;
        } catch (PDOException $e) {
            throw new BrevetException("Error set dnf for participant", 1, null);
        }


    }

    public function hasStampOnCheckpoint(string $participant_uid, string $checkpoint_uid): bool
    {


        try {
            $statement = $this->connection->prepare($this->sqls('hasStampOnCheckpoint'));
            $statement->bindParam(':participant_uid', $participant_uid);
            $statement->bindParam(':checkpoint_uid', $checkpoint_uid);
            $statement->execute();
            $result = $statement->fetch();

            if (empty($result)) {
                return false;
            }


            return true;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return true;
    }

    public function hasDnfOnCheckpoint(string $participant_uid, string $checkpoint_uid): bool
    {
        try {
            $statement = $this->connection->prepare($this->sqls('hasStampOnCheckpoint'));
            $statement->bindParam(':participant_uid', $participant_uid);
            $statement->bindParam(':checkpoint_uid', $checkpoint_uid);
            $statement->execute();
            $result = $statement->fetch();
            if (empty($result)) {
                return false;
            }
            return true;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return true;
    }

    public function hasDnf(string $participant_uid): bool
    {
        try {
            $statement = $this->connection->prepare($this->sqls('hasDnf'));
            $statement->bindParam(':participant_uid', $participant_uid);
            $statement->execute();
            $result = $statement->fetch();
            if (empty($result)) {
                return false;
            }
            return true;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return true;
    }

    public function hasCheckedOut(string $participant_uid, string $checkpoint_uid): bool
    {

        try {
            $statement = $this->connection->prepare($this->sqls('hasCheckout'));
            $statement->bindParam(':participant_uid', $participant_uid);
            $statement->bindParam(':checkpoint_uid', $checkpoint_uid);
            $statement->execute();
            $result = $statement->fetch();

            if (isset($result['checkout_date_time'])) {
                return true;
            }


            return false;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return false;
    }



    public function participantsbyTrackAndClub(string $track_uid, $club_uid)
    {
        try {
            $statement = $this->connection->prepare($this->sqls('participantByTrackAndClub'));
            $statement->bindParam(':track_uid', $track_uid);
            $statement->bindParam(':club_uid', $club_uid);
            $statement->execute();
            $events = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Partisipant\Participant::class, null);

            if (empty($events)) {
                return array();
            }

            return $events;
        } catch (PDOException $e) {
            throw new BrevetException("Error fetching participants", 1, null);
            //  echo "Error: " . $e->getMessage();
        }

    }

    public function participantsForCompetitor(string $competitor_uid)
    {
        try {
            $statement = $this->connection->prepare($this->sqls('participantsForCompetitor'));
            $statement->bindParam(':competitor_uid', $competitor_uid);
            $statement->execute();
            $events = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Partisipant\Participant::class, null);

            if (empty($events)) {
                return array();
            }

            return $events;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return array();

    }

    public function participantOntRackAndStartNumber(string $track_uid, $startnumber): ?Participant
    {
        try {

            $statement = $this->connection->prepare($this->sqls('participantonTrackWithStartnumber'));
            $statement->bindParam(':track_uid', $track_uid);
            $statement->bindParam(':startnumber', $startnumber);
            $statement->execute();

            $event = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Partisipant\Participant::class, null);

            if ($statement->rowCount() > 1) {
                // Fixa bätter felhantering

                throw new Exception();
            }
            if (!empty($event)) {

                return $event[0];
            }
        } catch (PDOException $e) {

            echo "Error: " . $e->getMessage();
        }

        return null;
    }

    public function participantHasStampOnAllExceptFinish(string $track_uid, string $finishCheckpoint_uid, string $participant_uid, int $totalcheckpointsfortrack): bool
    {


        $statement = $this->connection->prepare($this->sqls('participantHasStampOnAllExceptFinish'));
        $statement->bindParam(':track_uid', $track_uid);
        $statement->bindParam(':finishcheckpoint', $finishCheckpoint_uid);
        $statement->bindParam(':participant_uid', $participant_uid);
        $statement->execute();
        $count = $statement->fetchColumn();


        $statementlast = $this->connection->prepare($this->sqls('participantHasNotStampOnLastCheckpoint'));
        $statementlast->bindParam(':track_uid', $track_uid);
        $statementlast->bindParam(':finishcheckpoint', $finishCheckpoint_uid);
        $statementlast->bindParam(':participant_uid', $participant_uid);
        $statementlast->execute();

        $countlast = $statementlast->fetchColumn();

        if ($countlast + $count == $totalcheckpointsfortrack) {
            return true;
        }
        return false;
    }


    public function createparticipant(Participant $participanttoCreate): ?Participant
    {
        try {
            if ($participanttoCreate->getParticipantUid() === '') {
                $participant_uid = Uuid::uuid4();
            } else {
                $participant_uid = $participanttoCreate->getParticipantUid();
            }

            $track_uid = $participanttoCreate->getTrackUid();
            $competitor_uid = $participanttoCreate->getCompetitorUid();
            $startnumber = intval($participanttoCreate->getStartnumber());
            $finished = $participanttoCreate->isFinished();
            $acpkod = intval($participanttoCreate->getAcpkod());
            $club_uid = $participanttoCreate->getClubUid();
            $dns = $participanttoCreate->isDns();
            $dnf = $participanttoCreate->isDnf();
            $brevenr2 = $participanttoCreate->getBrevenr() == null ? null : $participanttoCreate->getBrevenr();
            $brevenr = intval($brevenr2);
            $time = $participanttoCreate->getTime();
            $medal = $participanttoCreate->getMedal();
            //  date("Y-m-d H:i:s");
            $register_date_time = $participanttoCreate->getRegisterDateTime();
            $dns_timestamp = $participanttoCreate->getDnsTimestamp();
            $dnf_timestamp = $participanttoCreate->getDnfTimestamp();
            $stmt = $this->connection->prepare($this->sqls('createParticipant'));
            $stmt->bindParam(':participant_uid', $participant_uid);
            $stmt->bindParam(':track_uid', $track_uid);
            $stmt->bindParam(':competitor_uid', $competitor_uid);
            $stmt->bindParam(':startnumber', $startnumber);
            $stmt->bindParam(':finished', $finished, PDO::PARAM_BOOL);
            $stmt->bindParam(':acpcode', $acpkod);
            $stmt->bindParam(':club_uid', $club_uid);
            $stmt->bindParam(':dns', $dns, PDO::PARAM_BOOL);
            $stmt->bindParam(':time', $time);
            $stmt->bindParam(':dnf', $dnf, PDO::PARAM_BOOL);
            $stmt->bindParam(':brevenr', $brevenr);
            $stmt->bindParam(':register_date_time', $register_date_time);
            $stmt->bindParam(':medal', $medal, PDO::PARAM_BOOL);
            $stmt->bindParam(':dns_timestamp', $dns_timestamp);
            $stmt->bindParam(':dnf_timestamp', $dnf_timestamp);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }

        $participanttoCreate->setParticipantUid($participant_uid);
        return $participanttoCreate;
    }

    public function updateParticipant(Participant $participantToUpdate): ?Participant
    {

        $participant_uid = $participantToUpdate->getParticipantUid();
        $track_uid = $participantToUpdate->getTrackUid();
        $competitor_uid = $participantToUpdate->getCompetitorUid();
        $startnumber = intval($participantToUpdate->getStartnumber());
        $finished = $participantToUpdate->isFinished();
        $acpkod = intval($participantToUpdate->getAcpkod());
        $club_uid = $participantToUpdate->getClubUid();
        $dns = $participantToUpdate->isDns();
        $dnf = $participantToUpdate->isDnf();
        $brevenr = intval($participantToUpdate->getBrevenr());
        $time = $participantToUpdate->getTime();
        $reg_date_time = $participantToUpdate->getRegisterDateTime();
        $started = $participantToUpdate->isStarted();
        $dns_timestamp = $participantToUpdate->getDnsTimestamp();
        $dnf_timestamp = $participantToUpdate->getDnfTimestamp();
        try {
            $stmt = $this->connection->prepare($this->sqls('updateParticipant'));
            $stmt->bindParam(':participant_uid', $participant_uid);
            $stmt->bindParam(':track_uid', $track_uid);
            $stmt->bindParam(':competitor_uid', $competitor_uid);
            $stmt->bindParam(':startnumber', $startnumber);
            $stmt->bindParam(':finished', $finished, PDO::PARAM_BOOL);
            $stmt->bindParam(':acpcode', $acpkod);
            $stmt->bindParam(':club_uid', $club_uid);
            $stmt->bindParam(':dns', $dns, PDO::PARAM_BOOL);
            $stmt->bindParam(':times', $time, PDO::PARAM_STR);
            $stmt->bindParam(':dnf', $dnf, PDO::PARAM_BOOL);
            $stmt->bindParam(':brevenr', $brevenr);
            $stmt->bindParam(':register_date_time', $reg_date_time);
            $stmt->bindParam(':started', $started, PDO::PARAM_BOOL);
            $stmt->bindParam(':dns_timestamp', $dns_timestamp);
            $stmt->bindParam(':dnf_timestamp', $dnf_timestamp);


            $status = $stmt->execute();
            if ($status) {
                return $participantToUpdate;
            }
        } catch (PDOException $e) {
            echo 'Kunde inte uppdatera site: ' . $e->getMessage();
        }
        return null;
    }


    //görs i efterhand
    public function updateBrevenr(string $brevenr, string $participant_uid): bool
    {

        $participant_uid = $participant_uid;
        $brevenr = $brevenr;
        print_r($brevenr);
        try {
            $stmt = $this->connection->prepare($this->sqls('updateBrevenr'));
            $stmt->bindParam(':participant_uid', $participant_uid);
            $stmt->bindParam(':brevenr', $brevenr);
            $status = $stmt->execute();
            if ($status) {
                return true;
            }
        } catch (PDOException $e) {
            echo 'Kunde inte uppdatera site: ' . $e->getMessage();
        }
        return false;
    }

    //görs i efterhand
    public function setDnf(string $participant_uid): bool
    {

        $participant_uid = $participant_uid;
        try {
            $dnf = 1;
            $dnf_timestamp = date('Y-m-d H:i:s');
            $stmt = $this->connection->prepare($this->sqls('setDnf'));
            $stmt->bindParam(':participant_uid', $participant_uid);
            $stmt->bindParam(':dnf', $dnf, PDO::PARAM_BOOL);
            $stmt->bindParam(':dnf_timestamp', $dnf_timestamp);
            $status = $stmt->execute();
            if ($status) {
                return true;
            }
        } catch (PDOException $e) {
            echo 'Kunde inte uppdatera site: ' . $e->getMessage();
        }
        return false;
    }


    //görs i efterhand
    public function rollbackDnf(string $participant_uid): bool
    {

        $participant_uid = $participant_uid;
        try {
            $dnf = 0;
            $dnf_timestamp = null;
            $stmt = $this->connection->prepare($this->sqls('setDnf'));
            $stmt->bindParam(':participant_uid', $participant_uid);
            $stmt->bindParam(':dnf', $dnf, PDO::PARAM_BOOL);
            $stmt->bindParam(':dnf_timestamp', $dnf_timestamp, PDO::PARAM_NULL);
            $status = $stmt->execute();
            if ($status) {
                return true;
            }
        } catch (PDOException $e) {
            echo 'Kunde inte uppdatera site: ' . $e->getMessage();
        }
        return false;
    }


    //görs i efterhand
    public function isDnf(string $participant_uid): bool
    {

        $participant_uid = $participant_uid;
        try {
            $dnf = true;
            $stmt = $this->connection->prepare($this->sqls('isDnf'));
            $stmt->bindParam(':participant_uid', $participant_uid);
            $stmt->bindParam(':dnf', $dnf, PDO::PARAM_BOOL);
            $status = $stmt->execute();
            if ($status) {
                return true;
            }
        } catch (PDOException $e) {
            echo 'Kunde inte uppdatera site: ' . $e->getMessage();
        }
        return false;
    }


    //görs i efterhand
    public function setDns(string $participant_uid): bool
    {

        try {
            $dns = true;
            $dns_timestamp = date('Y-m-d H:i:s');
            $stmt = $this->connection->prepare($this->sqls('setDns'));
            $stmt->bindParam(':participant_uid', $participant_uid);
            $stmt->bindParam(':dns', $dns, PDO::PARAM_BOOL);
            $stmt->bindParam(':dns_timestamp', $dns_timestamp);
            $status = $stmt->execute();
            if ($status) {
                return true;
            }
        } catch (PDOException $e) {
            echo 'Kunde inte uppdatera site: ' . $e->getMessage();
        }
        return false;
    }

    //görs i efterhand
    public function rollbackDns(string $participant_uid): bool
    {

        try {
            $dns = false;
            $dns_timestamp = null;
            $stmt = $this->connection->prepare($this->sqls('setDns'));
            $stmt->bindParam(':participant_uid', $participant_uid);
            $stmt->bindParam(':dns', $dns, PDO::PARAM_BOOL);
            $stmt->bindParam(':dns_timestamp', $dns_timestamp, PDO::PARAM_NULL);
            $status = $stmt->execute();
            if ($status) {
                return true;
            }
        } catch (PDOException $e) {
            echo 'Kunde inte uppdatera site: ' . $e->getMessage();
        }
        return false;
    }


    public function stamp(string $participant_uid, string $checkpoint_uid, bool $passed): bool
    {

        try {
            $passed_date_timestamp = date('Y-m-d H:i:s');
            $lat = null;
            $lng = null;
            $stmt = $this->connection->prepare($this->sqls('stampOnCheckpoint'));
            $stmt->bindParam(':participant_uid', $participant_uid);
            $stmt->bindParam(':checkpoint_uid', $checkpoint_uid);
            $stmt->bindParam(':passed', $passed);
            $stmt->bindParam(':passed_date_time', $passed_date_timestamp);
            $stmt->bindParam(':lat', $lat);
            $stmt->bindParam(':lng', $lng);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return true;
    }


    public function getParticipantCheckpointBy(string $participand_uid, string $checkpoint_uid)
    {
        try {
            $statement = $this->connection->prepare($this->sqls('allParticipants'));
            $statement->bindParam(':participant_uid', $participand_uid);
            $statement->bindParam(':checkpoint_uid', $checkpoint_uid);
            $statement->execute();
            $checkpoint = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Partisipant\ParticipantCheckpoint::class, null);

            if (empty($checkpoint)) {
                return array();
            }

            return $checkpoint[0];
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return array();
    }

    public function stampOnCheckpoint(string $participant_uid, string $checkpoint_uid, bool $started, bool $volonteercheckin, $lat, $lng): bool
    {
        try {

            $passed_date_timestamp = date('Y-m-d H:i:s');

            if ($lat) {
                $lat = new DecimalNumber($lat);
                $lat = $lat->toPrecision(7);
            } else {
                $lat = null;
            }

            if ($lng) {
                $lng = new DecimalNumber($lng);
                $lng = $lng->toPrecision(7);
            } else {
                $lng = null;
            }
            $passed = true;
            $stmt = $this->connection->prepare($this->sqls('updateCheckpoint'));
            $stmt->bindParam(':participant_uid', $participant_uid);
            $stmt->bindParam(':checkpoint_uid', $checkpoint_uid);
            $stmt->bindParam(':passed', $passed, PDO::PARAM_BOOL);
            $stmt->bindParam(':passed_date_time', $passed_date_timestamp);
            $stmt->bindParam(':volonteer_checkin', $volonteercheckin, PDO::PARAM_BOOL);
            $stmt->bindParam(':lat', $lat);
            $stmt->bindParam(':lng', $lng);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return true;
    }


    public function checkoutFromCheckpoint(string $participant_uid, string $checkpoint_uid, bool $started, bool $volonteercheckin): bool
    {
        try {
            $checkout_date_timestamp = date('Y-m-d H:i:s');

            $passed = true;
            $stmt = $this->connection->prepare($this->sqls('updateCheckpointWithCheckout'));
            $stmt->bindParam(':participant_uid', $participant_uid);
            $stmt->bindParam(':checkpoint_uid', $checkpoint_uid);
            $stmt->bindParam(':checkout_date_time', $checkout_date_timestamp);
            $stmt->bindParam(':volonteer_checkin', $volonteercheckin, PDO::PARAM_BOOL);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return true;
    }


    public function checkoutFromCheckpointWithTime(string $participant_uid, string $checkpoint_uid, bool $started, bool $volonteercheckin, string $datetime): bool
    {
        try {
            $checkout_date_timestamp = $datetime;

            $passed = true;
            $stmt = $this->connection->prepare($this->sqls('updateCheckpointWithCheckout'));
            $stmt->bindParam(':participant_uid', $participant_uid);
            $stmt->bindParam(':checkpoint_uid', $checkpoint_uid);
            $stmt->bindParam(':checkout_date_time', $checkout_date_timestamp);
            $stmt->bindParam(':volonteer_checkin', $volonteercheckin, PDO::PARAM_BOOL);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return true;
    }


    public function stampOnCheckpointWithTime(string $participant_uid, string $checkpoint_uid, string $datetime, bool $started, bool $volonteercheckin, $lat, $lng): bool
    {
        try {
            // Make sure we have a properly formatted MySQL datetime
            if (strpos($datetime, 'T') !== false || strpos($datetime, 'Z') !== false) {
                // This is an ISO-8601 format, convert it
                $dt = new \DateTime($datetime);
                
                // If the date has a 'Z' suffix (UTC time), convert to local time (GMT+2)
                if (strpos($datetime, 'Z') !== false) {
                    $dt->setTimezone(new \DateTimeZone('Europe/Stockholm'));
                }
                
                $passed_date_timestamp = $dt->format('Y-m-d H:i:s');
            } else {
                $passed_date_timestamp = $datetime;
            }

            if ($lat) {
                $lat = new DecimalNumber($lat);
                $lat = $lat->toPrecision(7);
            } else {
                $lat = null;
            }

            if ($lng) {
                $lng = new DecimalNumber($lng);
                $lng = $lng->toPrecision(7);
            } else {
                $lng = null;
            }

            $passed = true;
            $stmt = $this->connection->prepare($this->sqls('updateCheckpoint'));
            $stmt->bindParam(':participant_uid', $participant_uid);
            $stmt->bindParam(':checkpoint_uid', $checkpoint_uid);
            $stmt->bindParam(':passed', $passed, PDO::PARAM_BOOL);
            $stmt->bindParam(':passed_date_time', $passed_date_timestamp);
            $stmt->bindParam(':volonteer_checkin', $volonteercheckin, PDO::PARAM_BOOL);
            $stmt->bindParam(':lat', $lat);
            $stmt->bindParam(':lng', $lng);
            
            // Execute the statement
            $success = $stmt->execute();
            
            if (!$success) {
                throw new BrevetException("Failed to execute SQL statement: " . json_encode($stmt->errorInfo()), 5, null);
            }
            
            return true;
        } catch (PDOException $e) {
            // Instead of just echoing, throw an exception with the error message
            throw new BrevetException("Failed to update checkpoint time: " . $e->getMessage(), 5, $e);
        }
    }

    public function rollbackStamp(string $participant_uid, string $checkpoint_uid): bool
    {
        try {
            $passed_date_timestamp = null;
            $lat = null;
            $lng = null;
            $passed = 0;
            $checkout_date_time = null;
            $volonteer_checkin = 0;
            $stmt = $this->connection->prepare($this->sqls('updateCheckpoint'));
            $stmt->bindParam(':participant_uid', $participant_uid);
            $stmt->bindParam(':checkpoint_uid', $checkpoint_uid);
            $stmt->bindParam(':passed', $passed, PDO::PARAM_BOOL);
            $stmt->bindParam(':passed_date_time', $passed_date_timestamp);
            $stmt->bindParam(':volonteer_checkin', $volonteer_checkin, PDO::PARAM_BOOL);
            $stmt->bindParam(':lat', $lat);
            $stmt->bindParam(':lng', $lng);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return true;
    }


    public function rollbackStampAndCheckout(string $participant_uid, string $checkpoint_uid): bool
    {

        try {
            $passed_date_timestamp = null;
            $lat = null;
            $lng = null;
            $passed = 0;
            $checkout_date_time = null;
            $volonteer_checkin = 0;
            $stmt = $this->connection->prepare($this->sqls('undocheckinandchecnout'));
            $stmt->bindParam(':participant_uid', $participant_uid);
            $stmt->bindParam(':checkpoint_uid', $checkpoint_uid);
            $stmt->bindParam(':passed', $passed, PDO::PARAM_BOOL);
            $stmt->bindParam(':passed_date_time', $passed_date_timestamp);
            $stmt->bindParam(':checkout_date_time', $checkout_date_time);
            $stmt->bindParam(':volonteer_checkin', $volonteer_checkin, PDO::PARAM_BOOL);
            $stmt->bindParam(':lat', $lat);
            $stmt->bindParam(':lng', $lng);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return true;
    }



    public function undoCheckout(string $participant_uid, string $checkpoint_uid): bool
    {
        try {
            $passed_date_timestamp = null;
            $volonteer_checkin = 1;
            $stmt = $this->connection->prepare($this->sqls('updateCheckpointWithCheckout'));
            $stmt->bindParam(':participant_uid', $participant_uid);
            $stmt->bindParam(':checkpoint_uid', $checkpoint_uid);
            $stmt->bindParam(':checkout_date_time', $passed_date_timestamp);
            $stmt->bindParam(':volonteer_checkin', $volonteer_checkin, PDO::PARAM_BOOL);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }

        return true;
    }

    public function updateCheckoutTime(string $participant_uid, string $checkpoint_uid, string $checkout_date_time): bool
    {
        try {
            $volonteer_checkin = 1; // Mark as admin checkout
            $stmt = $this->connection->prepare($this->sqls('updateCheckpointWithCheckout'));
            $stmt->bindParam(':participant_uid', $participant_uid);
            $stmt->bindParam(':checkpoint_uid', $checkpoint_uid);
            $stmt->bindParam(':checkout_date_time', $checkout_date_time);
            $stmt->bindParam(':volonteer_checkin', $volonteer_checkin, PDO::PARAM_BOOL);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            throw new BrevetException("Failed to update checkout time: " . $e->getMessage(), 5, $e);
        }
    }

    public function stampTimeOnCheckpoint(string $participand_uid, $checkpoint_uid): ?ParticipantCheckpoint
    {

        try {
            $statement = $this->connection->prepare($this->sqls('participanCheckpointByParticipantUidAndCheckpointUid'));
            $statement->bindParam(':participant_uid', $participand_uid);
            $statement->bindParam(':checkpoint_uid', $checkpoint_uid);
            $statement->execute();
            $checkpoint = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Partisipant\ParticipantCheckpoint::class, null);

            if (empty($checkpoint)) {
                return null;
            }

            if (count($checkpoint) > 1) {
                throw new BrevetException("Error", 5, null);
            }

            return $checkpoint[0];
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return null;
    }

    public function createTrackCheckpointsFor(Participant $participant, array $checkpoints)
    {
        $participant_uid = $participant->getParticipantUid();
        $passed = false;
        $passed_date_timestamp = null;
        $lat = null;
        $lng = null;
        foreach ($checkpoints as $s => $ro) {

            $checkstmt = $this->connection->prepare('select * from participant_checkpoint where participant_uid=:participant_uid and checkpoint_uid=:checkpoint_uid;');
            $checkstmt->bindParam(':participant_uid', $participant_uid);
            $checkstmt->bindParam(':checkpoint_uid', $ro);
            $checkstmt->execute();

            if (!$checkstmt->rowCount() > 0) {
                try {
                    $stmt = $this->connection->prepare($this->sqls('createParticipantCheckpoint'));
                    $stmt->bindParam(':participant_uid', $participant_uid);
                    $stmt->bindParam(':checkpoint_uid', $ro);
                    $stmt->bindParam(':passed', $passed, PDO::PARAM_BOOL);
                    $stmt->bindParam(':passed_date_time', $passed_date_timestamp);
                    $stmt->bindParam(':lat', $lat);
                    $stmt->bindParam(':lng', $lng);
                    $stmt->execute();
                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
            }


        }

    }

    public function hasAnyoneStartedonTrack(?string $track_uid): bool
    {
        try {
            $stmt = $this->connection->prepare($this->sqls('hasAnyoneStartedOnTrack'));
            $stmt->bindParam(':track_uid', $track_uid);
            $status = $stmt->execute();
            if ($stmt->rowCount() == 0) {
                return true;
            }
        } catch (PDOException $e) {
            echo 'Ett fel har inträffat' . $e->getMessage();
        }
        return false;

    }

    public function deleteparticipantsOnTrack(?string $track_uid): int
    {
        try {
            $stmt = $this->connection->prepare($this->sqls('deleteParticipantsOnTrack'));
            $stmt->bindParam(':track_uid', $track_uid);
            $stmt->execute();
            return $stmt->rowCount();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }

        return 0;
    }

    public function deleteParticipantCheckpointOnTrack(?string $track_uid)
    {
        try {
            $stmt = $this->connection->prepare($this->sqls('deleteParticipantCheckpointOnTrack'));
            $stmt->bindParam(':participant_uid', $track_uid);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function deleteParticipantCheckpointOnTrackByParticipantUid(?string $participant_uid)
    {
        try {
            $stmt = $this->connection->prepare($this->sqls('deleteParticipantcheckpointbyparticipantuid'));
            $stmt->bindParam(':participant_uid', $participant_uid);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function deleteParticipantByUID(?string $participant_uid)
    {
        try {
            $stmt = $this->connection->prepare($this->sqls('deleteParticipant'));
            $stmt->bindParam(':participant_uid', $participant_uid);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function updateTime(?string $track_uid, ?string $participant_uid, string $newTime): bool
    {

        try {
            $stmt = $this->connection->prepare($this->sqls('updateTime'));
            $stmt->bindParam(':participant_uid', $participant_uid);
            $stmt->bindParam(':track_uid', $track_uid);
            $stmt->bindParam(':newtime', $newTime);
            $status = $stmt->execute();
            if ($status) {
                return true;
            }
        } catch (PDOException $e) {
            echo 'Kunde inte uppdatera site: ' . $e->getMessage();
        }
        return false;

    }

    // This method ensures we only clear the checkout time but preserve the check-in time
    public function clearCheckoutTimeOnly(string $participant_uid, string $checkpoint_uid): bool
    {
        try {
            // Simply set checkout_date_time to null
            $checkout_date_time = null;
            $volonteer_checkin = 1;
            
            $stmt = $this->connection->prepare($this->sqls('updateCheckpointWithCheckout'));
            $stmt->bindParam(':participant_uid', $participant_uid);
            $stmt->bindParam(':checkpoint_uid', $checkpoint_uid);
            $stmt->bindParam(':checkout_date_time', $checkout_date_time, PDO::PARAM_NULL);
            $stmt->bindParam(':volonteer_checkin', $volonteer_checkin, PDO::PARAM_BOOL);
            
            $status = $stmt->execute();
            return $status;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    public function sqls($type)
    {
        $eventqls['allParticipants'] = 'select * from participant e;';
        $eventqls['participantByUID'] = 'select * from participant e where participant_uid=:participant_uid;';
        $eventqls['participantsForCompetitor'] = 'select * from participant e where competitor_uid=:competitor_uid;';
        $eventqls['dns'] = 'select *  from participant e where e.track_uid=:track_uid and dns=:dns;';
        $eventqls['dnf'] = 'select *  from participant e where e.track_uid=:track_uid and dnf=:dnf;';
        $eventqls['allParticipantsOnTrack'] = 'select *  from participant e where e.track_uid=:track_uid;';
        $eventqls['allParticipantsForCompetitor'] = 'select *  from participant e where e.competitor_uid=:competitor_uid;';
        $eventqls['participantonTrackWithStartnumber'] = 'select *  from participant e where e.track_uid=:track_uid and startnumber=:startnumber;';
        $eventqls['participantByTrackAndClub'] = 'select *  from participant e where e.track_uid=:track_uid and club_uid=:club_uid;';
        $eventqls['deleteParticipant'] = 'delete from participant  where participant_uid=:participant_uid;';
        $eventqls['deleteParticipantcheckpointbyparticipantuid'] = 'delete from participant  where participant_uid=:participant_uid;';
        $eventqls['updateParticipant'] = "UPDATE participant SET  track_uid=:track_uid , competitor_uid=:competitor_uid , startnumber=:startnumber, finished=:finished, acpkod=:acpcode, club_uid=:club_uid , dns=:dns, dnf=:dnf , started=:started, register_date_time=:register_date_time , time=:times , brevenr=:brevenr, dns_timestamp=:dns_timestamp, dnf_timestamp=:dnf_timestamp WHERE participant_uid=:participant_uid";
        $eventqls['updateBrevenr'] = "UPDATE participant SET  brevenr=:brevenr WHERE participant_uid=:participant_uid";
        $eventqls['updateTime'] = "UPDATE participant SET  time=:newtime WHERE participant_uid=:participant_uid and track_uid=:track_uid";
        $eventqls['createParticipant'] = 'INSERT INTO participant(participant_uid, track_uid, competitor_uid, startnumber, finished,  acpkod, club_uid , time ,dns, dnf, medal, brevenr,register_date_time, dns_timestamp, dnf_timestamp) VALUES (:participant_uid, :track_uid,:competitor_uid,:startnumber,:finished , :acpcode, :club_uid, :time, :dns, :dnf, :medal, :brevenr, :register_date_time, :dns_timestamp, :dnf_timestamp)';
        $eventqls['participantByTrackAndCompetitorUid'] = 'select *  from participant e where e.track_uid=:track_uid and competitor_uid=:competitor_uid;';
        $eventqls['stampOnCheckpoint'] = 'INSERT INTO participant_checkpoint(participant_uid ,checkpoint_uid, passed, passeded_date_time, lat, lng) VALUES (:participant_uid ,:checkpoint_uid, :passed,:passed_date_time,:lat,:lng)';
        $eventqls['createParticipantCheckpoint'] = 'INSERT INTO participant_checkpoint(participant_uid ,checkpoint_uid, passed, passeded_date_time, lat, lng) VALUES (:participant_uid ,:checkpoint_uid, :passed,:passed_date_time,:lat,:lng)';
        $eventqls['updateCheckpoint'] = "UPDATE participant_checkpoint SET  passed=:passed, passeded_date_time=:passed_date_time, volonteer_checkin=:volonteer_checkin, lat=:lat, lng=:lng  WHERE participant_uid=:participant_uid and checkpoint_uid=:checkpoint_uid;";
        $eventqls['updateCheckpointWithCheckout'] = "UPDATE participant_checkpoint SET checkout_date_time=:checkout_date_time, volonteer_checkin=:volonteer_checkin WHERE participant_uid=:participant_uid and checkpoint_uid=:checkpoint_uid;";
        $eventqls['undocheckinandchecnout'] = "UPDATE participant_checkpoint SET  passed=:passed, passeded_date_time=:passed_date_time, checkout_date_time=:checkout_date_time ,volonteer_checkin=:volonteer_checkin, lat=:lat, lng=:lng  WHERE participant_uid=:participant_uid and checkpoint_uid=:checkpoint_uid;";
        $eventqls['setDnf'] = "UPDATE participant SET  dnf=:dnf, dnf_timestamp=:dnf_timestamp WHERE participant_uid=:participant_uid;";
        $eventqls['setDns'] = "UPDATE participant SET  dns=:dns, dns_timestamp=:dns_timestamp WHERE participant_uid=:participant_uid;";
        $eventqls['participanCheckpointByParticipantUidAndCheckpointUid'] = 'select *  from participant_checkpoint e where e.participant_uid=:participant_uid and checkpoint_uid=:checkpoint_uid;';
        $eventqls['hasStampOnCheckpoint'] = 'select passed from participant_checkpoint e where e.participant_uid=:participant_uid and checkpoint_uid=:checkpoint_uid and passed = true;';
        $eventqls['hasDnf'] = 'select dnf from participant e where e.participant_uid=:participant_uid and dnf = true;';
        $eventqls['hasCheckout'] = 'select checkout_date_time  from participant_checkpoint e where e.participant_uid=:participant_uid and e.checkpoint_uid=:checkpoint_uid and checkout_date_time  is not null;';
        $eventqls['participantHasStampOnAllExceptFinish'] = 'SELECT count(s.checkpoint_uid) FROM v_partisipant_to_pass_checkpoint s inner join track_checkpoint tc on s.checkpoint_uid = tc.checkpoint_uid  where s.passed = 1 and s.started = 1 and s.dnf = 0 and s.track_uid=:track_uid and s.checkpoint_uid !=:finishcheckpoint  and s.participant_uid =:participant_uid;';
        $eventqls['participantHasNotStampOnLastCheckpoint'] = 'SELECT count(s.checkpoint_uid) FROM v_partisipant_to_pass_checkpoint s  where s.passed = 0 and s.started = 1 and s.dnf = 0  and s.track_uid=:track_uid and s.checkpoint_uid=:finishcheckpoint  and s.participant_uid =:participant_uid;';
        $eventqls['hasAnyoneStartedOnTrack'] = 'select * from participant e where e.track_uid=:track_uid and e.started = true or e.dns = true or e.dnf = true or e.finished = true or e.time != null;';
        $eventqls['deleteParticipantsOnTrack'] = 'delete  from participant e where e.track_uid=:track_uid;';
        $eventqls['deleteParticipantCheckpointOnTrack'] = 'delete from participant_checkpoint e where e.participant_uid=:participant_uid;';
        $eventqls['stampCheckoutOnCheckpoint'] = "UPDATE participant_checkpoint SET  checkedout=:checkedout, checkout_date_time=:checkout_date_time, volonteer_checkout=:volonteer_checkout, lat=:lat, lng=:lng  WHERE participant_uid=:participant_uid and checkpoint_uid=:checkpoint_uid;";
        $eventqls['participantCountByClub'] = 'SELECT COUNT(*) as count FROM participant WHERE club_uid = :club_uid;';


        return $eventqls[$type];
        // TODO: Implement sqls() method.
    }

    public function isClubInUseByParticipants(string $club_uid): bool
    {
        try {
            $statement = $this->connection->prepare($this->sqls('participantCountByClub'));
            $statement->bindParam(':club_uid', $club_uid);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            
            return $result['count'] > 0;
        } catch (PDOException $e) {
            throw new BrevetException("Error checking if club is in use", 1, null);
        }
    }

    /**
     * Check if a competitor is already registered for a track
     */
    public function hasExistingRegistration(string $competitor_uid, string $track_uid): bool
    {
        try {
            $statement = $this->connection->prepare("SELECT COUNT(*) FROM participant WHERE competitor_uid = :competitor_uid AND track_uid = :track_uid");
            $statement->bindParam(':competitor_uid', $competitor_uid);
            $statement->bindParam(':track_uid', $track_uid);
            $statement->execute();
            return $statement->fetchColumn() > 0;
        } catch (PDOException $e) {
            throw new BrevetException("Error checking for existing registration: " . $e->getMessage(), 5, null);
        }
    }

}