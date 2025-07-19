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

    public function participantWithStartNumberExists(string $startnumber): bool
    {
        try {
            $stmt = $this->connection->prepare($this->sqls('participantWithStartNumberExists'));
            $stmt->bindParam(':startnumber', $startnumber);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error checking if participant with start number exists: " . $e->getMessage());
            return false;
        }
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
            $finished_timestamp = $participanttoCreate->getFinishedTimestamp();
            $additional_information = $participanttoCreate->getAdditionalInformation();
            $use_physical_brevet_card = $participanttoCreate->getUsePhysicalBrevetCard();
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
            $stmt->bindParam(':finished_timestamp', $finished_timestamp);
            $stmt->bindParam(':additional_information', $additional_information);
            $stmt->bindParam(':use_physical_brevet_card', $use_physical_brevet_card, PDO::PARAM_BOOL);
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
        $finished_timestamp = $participantToUpdate->getFinishedTimestamp();
        $additional_information = $participantToUpdate->getAdditionalInformation();
        $use_physical_brevet_card = $participantToUpdate->getUsePhysicalBrevetCard();
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
            $stmt->bindParam(':finished_timestamp', $finished_timestamp);
            $stmt->bindParam(':additional_information', $additional_information);
            $stmt->bindParam(':use_physical_brevet_card', $use_physical_brevet_card, PDO::PARAM_BOOL);


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
        $eventqls['participantWithStartNumberExists'] = 'select 1 from participant e where e.startnumber=:startnumber limit 1;';
        $eventqls['participantByTrackAndClub'] = 'select *  from participant e where e.track_uid=:track_uid and club_uid=:club_uid;';
        $eventqls['deleteParticipant'] = 'delete from participant  where participant_uid=:participant_uid;';
        $eventqls['deleteParticipantcheckpointbyparticipantuid'] = 'delete from participant_checkpoint where participant_uid=:participant_uid;';
        $eventqls['updateParticipant'] = "UPDATE participant SET  track_uid=:track_uid , competitor_uid=:competitor_uid , startnumber=:startnumber, finished=:finished, acpkod=:acpcode, club_uid=:club_uid , dns=:dns, dnf=:dnf , started=:started, register_date_time=:register_date_time , time=:times , brevenr=:brevenr, dns_timestamp=:dns_timestamp, dnf_timestamp=:dnf_timestamp, finished_timestamp=:finished_timestamp, additional_information=:additional_information, use_physical_brevet_card=:use_physical_brevet_card WHERE participant_uid=:participant_uid";
        $eventqls['updateBrevenr'] = "UPDATE participant SET  brevenr=:brevenr WHERE participant_uid=:participant_uid";
        $eventqls['updateTime'] = "UPDATE participant SET  time=:newtime WHERE participant_uid=:participant_uid and track_uid=:track_uid";
        $eventqls['createParticipant'] = 'INSERT INTO participant(participant_uid, track_uid, competitor_uid, startnumber, finished,  acpkod, club_uid , time ,dns, dnf, medal, brevenr,register_date_time, dns_timestamp, dnf_timestamp, finished_timestamp, additional_information, use_physical_brevet_card) VALUES (:participant_uid, :track_uid,:competitor_uid,:startnumber,:finished , :acpcode, :club_uid, :time, :dns, :dnf, :medal, :brevenr, :register_date_time, :dns_timestamp, :dnf_timestamp, :finished_timestamp, :additional_information, :use_physical_brevet_card)';
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

    /**
     * Move a participant from one track to another
     * This includes updating the participant's track_uid and recreating checkpoint associations
     */
    public function moveParticipantToTrack(string $participant_uid, string $new_track_uid): bool
    {
        try {
            // Begin transaction
            $this->connection->beginTransaction();
            
            // Get the participant to verify it exists
            $participant = $this->participantFor($participant_uid);
            if (!$participant) {
                throw new BrevetException("Participant not found with UID: " . $participant_uid, 404);
            }
            
            $old_track_uid = $participant->getTrackUid();
            
            // Check if participant has already started (has stamps, DNF, DNS, or finished)
            if ($participant->isStarted() || $participant->isFinished() || $participant->isDnf() || $participant->isDns()) {
                throw new BrevetException("Cannot move participant who has already started, finished, DNF, or DNS", 9);
            }
            
            // Delete existing participant checkpoints for this participant first
            $this->deleteParticipantCheckpointOnTrackByParticipantUid($participant_uid);
            
            // Update the participant's track_uid using the existing updateParticipant method
            $participant->setTrackUid($new_track_uid);
            $updatedParticipant = $this->updateParticipant($participant);
            
            if (!$updatedParticipant) {
                throw new BrevetException("Failed to update participant track", 5);
            }
            
            // Get checkpoints for the new track
            $trackCheckpoints = $this->getTrackCheckpoints($new_track_uid);
            
            // Create new participant checkpoints for the new track
            if (!empty($trackCheckpoints)) {
                $this->createTrackCheckpointsFor($updatedParticipant, $trackCheckpoints);
            }
            
            // Commit transaction
            $this->connection->commit();
            
            return true;
            
        } catch (PDOException $e) {
            // Rollback transaction on error
            $this->connection->rollBack();
            throw new BrevetException("Database error while moving participant: " . $e->getMessage(), 5, $e);
        } catch (BrevetException $e) {
            // Rollback transaction on BrevetException
            $this->connection->rollBack();
            throw $e;
        }
    }
    
    /**
     * Get checkpoint UIDs for a track
     */
    private function getTrackCheckpoints(string $track_uid): array
    {
        try {
            $stmt = $this->connection->prepare("SELECT tc.checkpoint_uid FROM track_checkpoint tc JOIN checkpoint c ON tc.checkpoint_uid = c.checkpoint_uid WHERE tc.track_uid = :track_uid ORDER BY c.distance");
            $stmt->bindParam(':track_uid', $track_uid);
            $stmt->execute();
            
            $checkpoints = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $checkpoints[] = $row['checkpoint_uid'];
            }
            
            return $checkpoints;
        } catch (PDOException $e) {
            throw new BrevetException("Error fetching track checkpoints: " . $e->getMessage(), 5, $e);
        }
    }

    /**
     * Move all participants from one track to another track
     * This includes updating all participants' track_uid and recreating checkpoint associations
     */
    public function moveAllParticipantsToTrack(string $from_track_uid, string $to_track_uid): array
    {
        try {
            // Get all participants from source track
            $participants = $this->participantsOnTrack($from_track_uid);
            $movedParticipants = [];
            
            foreach ($participants as $participant) {
                // Check if participant has already started
                if ($participant->isStarted() || $participant->isFinished() || $participant->isDnf() || $participant->isDns()) {
                    continue; // Skip participants who have already started
                }
                
                // Check for start number conflicts
                $existingParticipant = $this->participantOntRackAndStartNumber($to_track_uid, $participant->getStartnumber());
                if ($existingParticipant) {
                    // Generate new start number
                    $newStartNumber = $this->findNextAvailableStartNumber($to_track_uid);
                    $participant->setStartnumber($newStartNumber);
                }
                
                // Move participant to new track
                $participant->setTrackUid($to_track_uid);
                $updatedParticipant = $this->updateParticipant($participant);
                
                if ($updatedParticipant) {
                    // Recreate checkpoints for the new track
                    $this->moveParticipantToTrack($participant->getParticipantUid(), $to_track_uid);
                    $movedParticipants[] = $updatedParticipant;
                }
            }
            
            return $movedParticipants;
            
        } catch (PDOException $e) {
            throw new BrevetException("Error moving participants: " . $e->getMessage(), 1, $e);
        }
    }

    /**
     * Get daily participant statistics
     */
    public function getDailyStats(string $date, ?int $organizerId = null): array
    {
        try {
            $sql = "SELECT 
                    COUNT(DISTINCT CASE WHEN DATE(p.register_date_time) = DATE(:date) THEN p.participant_uid END) as countparticipants,
                    COALESCE(SUM(CASE WHEN DATE(p.register_date_time) = DATE(:date) AND p.started = 1 THEN 1 ELSE 0 END), 0) as started,
                    COALESCE(SUM(CASE WHEN DATE(p.finished_timestamp) = DATE(:date) THEN 1 ELSE 0 END), 0) as completed,
                    COALESCE(SUM(CASE WHEN DATE(p.dnf_timestamp) = DATE(:date) THEN 1 ELSE 0 END), 0) as dnf,
                    COALESCE(SUM(CASE WHEN DATE(p.dns_timestamp) = DATE(:date) THEN 1 ELSE 0 END), 0) as dns
                FROM participant p
                JOIN track t ON t.track_uid = p.track_uid";

            // Add organizer filter if provided
            if ($organizerId !== null) {
                $sql .= " WHERE t.organizer_id = :organizer_id";
            }

            $stmt = $this->connection->prepare($sql);
            $stmt->bindParam(':date', $date);
            
            // Bind organizer_id parameter if filtering is needed
            if ($organizerId !== null) {
                $stmt->bindParam(':organizer_id', $organizerId, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result;
            
        } catch (PDOException $e) {
            throw new BrevetException("Error getting daily stats: " . $e->getMessage(), 1, $e);
        }
    }

    /**
     * Get weekly participant statistics
     */
    public function getWeeklyStats(string $date, ?int $organizerId = null): array
    {
        try {
            $sql = "SELECT 
                    COUNT(DISTINCT CASE WHEN p.register_date_time >= DATE_SUB(:date, INTERVAL 6 DAY) AND p.register_date_time < DATE_ADD(DATE(:date), INTERVAL 1 DAY) THEN p.participant_uid END) as countparticipants,
                    COALESCE(SUM(CASE WHEN p.register_date_time >= DATE_SUB(:date, INTERVAL 6 DAY) AND p.register_date_time < DATE_ADD(DATE(:date), INTERVAL 1 DAY) AND p.started = 1 THEN 1 ELSE 0 END), 0) as started,
                    COALESCE(SUM(CASE WHEN p.finished_timestamp >= DATE_SUB(:date, INTERVAL 6 DAY) AND p.finished_timestamp < DATE_ADD(DATE(:date), INTERVAL 1 DAY) THEN 1 ELSE 0 END), 0) as completed,
                    COALESCE(SUM(CASE WHEN p.dnf_timestamp >= DATE_SUB(:date, INTERVAL 6 DAY) AND p.dnf_timestamp < DATE_ADD(DATE(:date), INTERVAL 1 DAY) THEN 1 ELSE 0 END), 0) as dnf,
                    COALESCE(SUM(CASE WHEN p.dns_timestamp >= DATE_SUB(:date, INTERVAL 6 DAY) AND p.dns_timestamp < DATE_ADD(DATE(:date), INTERVAL 1 DAY) THEN 1 ELSE 0 END), 0) as dns
                FROM participant p
                JOIN track t ON t.track_uid = p.track_uid";

            // Add organizer filter if provided
            if ($organizerId !== null) {
                $sql .= " WHERE t.organizer_id = :organizer_id";
            }
            
            $statement = $this->connection->prepare($sql);
            $statement->bindParam(':date', $date);
            
            // Bind organizer_id parameter if filtering is needed
            if ($organizerId !== null) {
                $statement->bindParam(':organizer_id', $organizerId, PDO::PARAM_INT);
            }
            
            $statement->execute();
            
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            
            return $result ?: [
                'countparticipants' => 0,
                'started' => 0,
                'completed' => 0,
                'dnf' => 0,
                'dns' => 0
            ];
            
        } catch (PDOException $e) {
            throw new BrevetException("Error getting weekly stats: " . $e->getMessage(), 1, $e);
        }
    }

    /**
     * Get yearly participant statistics
     */
    public function getYearlyStats(string $date, ?int $organizerId = null): array
    {
        try {
            $sql = "SELECT 
                    COUNT(DISTINCT CASE WHEN YEAR(p.register_date_time) = YEAR(:date) THEN p.participant_uid END) as countparticipants,
                    COALESCE(SUM(CASE WHEN YEAR(p.register_date_time) = YEAR(:date) AND p.started = 1 THEN 1 ELSE 0 END), 0) as started,
                    COALESCE(SUM(CASE WHEN YEAR(p.finished_timestamp) = YEAR(:date) THEN 1 ELSE 0 END), 0) as completed,
                    COALESCE(SUM(CASE WHEN YEAR(p.dnf_timestamp) = YEAR(:date) THEN 1 ELSE 0 END), 0) as dnf,
                    COALESCE(SUM(CASE WHEN YEAR(p.dns_timestamp) = YEAR(:date) THEN 1 ELSE 0 END), 0) as dns
                FROM participant p
                JOIN track t ON t.track_uid = p.track_uid";

            // Add organizer filter if provided
            if ($organizerId !== null) {
                $sql .= " WHERE t.organizer_id = :organizer_id";
            }
            
            $statement = $this->connection->prepare($sql);
            $statement->bindParam(':date', $date);
            
            // Bind organizer_id parameter if filtering is needed
            if ($organizerId !== null) {
                $statement->bindParam(':organizer_id', $organizerId, PDO::PARAM_INT);
            }
            
            $statement->execute();
            
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            
            return $result ?: [
                'countparticipants' => 0,
                'started' => 0,
                'completed' => 0,
                'dnf' => 0,
                'dns' => 0
            ];
            
        } catch (PDOException $e) {
            throw new BrevetException("Error getting yearly stats: " . $e->getMessage(), 1, $e);
        }
    }

    /**
     * Get latest registration
     */
    public function getLatestRegistration(?int $organizerId = null): ?array
    {
        try {
            $sql = "SELECT 
                    p.*,
                    c.given_name,
                    c.family_name,
                    cl.title as club_name,
                    t.title as track_name
                FROM participant p
                JOIN track t ON t.track_uid = p.track_uid
                LEFT JOIN competitors c ON c.competitor_uid = p.competitor_uid
                LEFT JOIN club cl ON cl.club_uid = p.club_uid";

            // Add organizer filter if provided
            if ($organizerId !== null) {
                $sql .= " WHERE t.organizer_id = :organizer_id";
            }

            $sql .= " ORDER BY p.register_date_time DESC LIMIT 1";

            $statement = $this->connection->prepare($sql);
            
            // Bind organizer_id parameter if filtering is needed
            if ($organizerId !== null) {
                $statement->bindParam(':organizer_id', $organizerId, PDO::PARAM_INT);
            }
            
            $statement->execute();
            
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            
            if (!$result) {
                return null;
            }

            return [
                'participant_uid' => $result['participant_uid'],
                'name' => $result['given_name'] . ' ' . $result['family_name'],
                'club' => $result['club_name'] ?? 'No Club',
                'track' => $result['track_name'],
                'registration_time' => $result['register_date_time']
            ];
            
        } catch (PDOException $e) {
            throw new BrevetException("Error getting latest registration: " . $e->getMessage(), 1, $e);
        }
    }

    /**
     * Get top tracks statistics
     */
    public function getTopTracks(?int $organizerId = null): array
    {
        try {
            $sql = "WITH recent_registrations AS (
                SELECT 
                    t.track_uid,
                    t.title as track_name,
                    COUNT(DISTINCT p.participant_uid) as total_participants,
                    COUNT(DISTINCT CASE WHEN p.register_date_time >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN p.participant_uid END) as recent_registrations,
                    MIN(CASE WHEN p.register_date_time >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN p.register_date_time END) as recent_first_registration,
                    MAX(CASE WHEN p.register_date_time >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN p.register_date_time END) as recent_last_registration,
                    o.organization_name as organizer_name,
                    t.active,
                    t.organizer_id
                FROM track t
                LEFT JOIN participant p ON t.track_uid = p.track_uid
                LEFT JOIN organizers o ON t.organizer_id = o.id";

            // Add organizer filter if provided
            if ($organizerId !== null) {
                $sql .= " WHERE t.organizer_id = :organizer_id";
            }

            $sql .= " GROUP BY t.track_uid, t.title, o.organization_name, t.active, t.organizer_id
            )
            SELECT 
                track_name,
                total_participants as participant_count,
                recent_registrations as registrations_last_30_days,
                recent_first_registration as first_registration,
                recent_last_registration as last_registration,
                organizer_name,
                active,
                organizer_id
            FROM recent_registrations 
            WHERE recent_registrations > 0";

            // Add additional organizer filter in the main query if provided
            if ($organizerId !== null) {
                $sql .= " AND organizer_id = :organizer_id2";
            }

            $sql .= " ORDER BY total_participants DESC LIMIT 5";
            
            $statement = $this->connection->prepare($sql);
            
            // Bind organizer_id parameter if filtering is needed
            if ($organizerId !== null) {
                $statement->bindParam(':organizer_id', $organizerId, PDO::PARAM_INT);
                $statement->bindParam(':organizer_id2', $organizerId, PDO::PARAM_INT);
            }
            
            $statement->execute();
            
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            
            return $result;
            
        } catch (PDOException $e) {
            throw new BrevetException("Error getting top tracks: " . $e->getMessage(), 1, $e);
        }
    }

    /**
     * Find the next available start number on a track by finding gaps in the sequence
     * If there are gaps (e.g., 1001, 1003, 1004), it will return 1002
     * If no gaps, it will return the next number after the highest
     * Handles cases where start numbers begin at higher values (e.g., 4001, 4002, 4003)
     */
    public function findNextAvailableStartNumber(string $track_uid): string
    {
        $participants = $this->participantsOnTrack($track_uid);
        $usedStartNumbers = [];
        
        foreach ($participants as $participant) {
            $usedStartNumbers[] = (int)$participant->getStartnumber();
        }
        
        if (empty($usedStartNumbers)) {
            return "1001"; // Default start number if no participants exist
        }
        
        // Sort the used start numbers
        sort($usedStartNumbers);
        
        // Find the actual sequence range
        $minNumber = $usedStartNumbers[0];
        $maxNumber = $usedStartNumbers[count($usedStartNumbers) - 1];
        
        // If the sequence starts at a higher number (e.g., 4001), work within that range
        // Otherwise, start from 1001 as the minimum
        $sequenceStart = max(1001, $minNumber);
        
        // Find the first gap in the sequence starting from the sequence start
        $expectedNumber = $sequenceStart;
        
        foreach ($usedStartNumbers as $usedNumber) {
            if ($usedNumber > $expectedNumber) {
                // Found a gap, return the expected number
                return (string)$expectedNumber;
            }
            $expectedNumber = $usedNumber + 1;
        }
        
        // No gaps found, return the next number after the highest
        return (string)$expectedNumber;
    }
}