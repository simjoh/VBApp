<?php

namespace App\Domain\Model\Partisipant\Repository;
use App\common\Repository\BaseRepository;
use App\Domain\Model\Partisipant\Participant;
use Exception;
use PDO;
use PDOException;

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
//        try {
//            $statement = $this->connection->prepare($this->sqls('allSites'));
//            $statement->execute();
//            $data = $statement->fetchAll();
//
//            if (empty($data)) {
//                return array();
//            }
//            $sites = [];
//            foreach ($data as $x =>  $row) {
//                // fixa lat long
//                $site = new Site($row["site_uid"], $row["place"],
//                    $row["adress"],$row['description'],
//                    is_null($row["location"]) ? "" : $row["location"],
//                    is_null($row["lat"])? new DecimalNumber("0") : new DecimalNumber($row["lat"]), is_null($row["lng"])   ? new DecimalNumber("0")  : new DecimalNumber($row["lng"]), is_null($row["picture"]) ? "": $row["picture"] );
//                array_push($sites,  $site);
//            }
//            return $sites;
//
//        }
//        catch(PDOException $e)
//        {
//            echo "Error: " . $e->getMessage();
//        }

        return null;
    }

    public function partisipantsOnTrack(){

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



    public function sqls($type)
    {
        $eventqls['allParticipants'] = 'select * from participant e;';
        $eventqls['allParticipantsOnTrack'] = 'select *  from participant e where e.track_uid=:track_uid;';
        $eventqls['participantonTrackWithStartnumber'] = 'select *  from participant e where e.track_uid=:track_uid and startnumber=:startnumber;';
        $eventqls['deleteEvent'] = 'delete from participant  where participant_uid=:participant_uid;';
        return $eventqls[$type];
        // TODO: Implement sqls() method.
    }
}