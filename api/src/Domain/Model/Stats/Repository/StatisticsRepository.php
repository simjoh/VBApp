<?php

namespace App\Domain\Model\Stats\Repository;

use App\common\Repository\BaseRepository;
use App\Domain\Model\Stats\TrackStatistics;
use Exception;
use PDO;
use PDOException;


class StatisticsRepository extends BaseRepository
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


    public function statsForTrack(string $track_uid): ?TrackStatistics {

        try {
            $statement = $this->connection->prepare($this->sqls('trackstats'));
            $statement->bindParam(':track_uid', $track_uid);
            $statement->execute();
            $tracks = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Stats\TrackStatistics::class, null);
            if($statement->rowCount() > 0){
                return $tracks[0];
            } else {
                return null;
            }

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return null;
        }
    }


    public function sqls($type)
    {
        $statssqls['trackstats'] = 'select countparticipants as countParticipants, dns as countDns, dnf as countDnf, completed as  countFinished from v_race_statistic where track_uid=:track_uid';

        return $statssqls[$type];
    }
}