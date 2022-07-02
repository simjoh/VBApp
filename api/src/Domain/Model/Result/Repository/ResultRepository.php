<?php

namespace App\Domain\Model\Result\Repository;

use App\common\Repository\BaseRepository;
use PDO;

class ResultRepository  extends BaseRepository
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


    public function getResultsForEvent(string $event_uid, string $year): ?array {


        $statement = $this->connection->prepare($this->sqls('resultsForEvent'));
        $statement->bindParam(':event_uid', $event_uid);
        $statement->bindParam(':year', $year);
        $statement->execute();
        $resultset = $statement->fetchAll(PDO::FETCH_ASSOC);


//        $resultArray = array();
//
//        $files = array();
//        $files['ID'] = '';
//        $files['Fornamn'] = '';
//        $files['Efternamn'] = '';
////        foreach ($resultset as $s => $trc) {
////           echo $trc;
////        }
//
//        foreach($resultset as $item) {
//
//            $dnf = $item['DNF'];
//            $dns = $item['DNS'];
//            $time = $item['Tid'];
//
//
//            $files = array();
//            $files['ID'] = $item['ID'];
//            $files['Fornamn'] = $item['Fornamn'];
//            $files['Efternamn'] = $item['Efternamn'];
//            $files['Klubb'] = $item['Klubb'];
//            $files['last checkpoint'] = $item['Sista'];
//
//            if($item['mal'] == true){
//                if($item['Tid'] == null){
//                    $files['Tid'] = "";
//                } else {
//                    $files['Tid'] = $item['Tid'];
//                }
//
//            }
//            if($time == null & $item['mal'] == false){
//                $files['Tid'] = "";
//                if($dnf == true){
//                    $files['Tid'] = 'DNF';
//                }
//                if($dns == true){
//                    $files['Tid'] = 'DNS';
//                    $files['last checkpoint'] = '';
//                }
//            }
//
//            array_push($resultArray,$files);
//        }

        return $this->getResultArray($resultset);
      //  return $resultArray;


    }


    public function trackParticipantsOnTrack(string $event_uid, array $track_uid):array{

        $track_uids = array();
        foreach ($track_uid as $track){
            array_push($track_uids,$track->getTrackUid());
        }

        $in  = str_repeat('?,', count($track_uids) - 1) . '?';


        $sql = " select revent.startnumber AS ID, revent.finished as mal, revent.given_name as Fornamn, revent.family_name as Efternamn,revent.club as Klubb,revent.time as Tid, revent.dnf as DNF, revent.DNS as DNS, revent.adress as Sista, revent.passeded_date_time as passedtime  from v_result_for_event_and_track revent where revent.track_uid  IN ($in);";

        $statement = $this->connection->prepare($sql);
        $statement->execute($track_uids);
        $resultset = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $this->getTrackArray($resultset);

    }

    private function getResultArray($resultset): array {

        $resultArray = array();

        $files = array();
        $files['ID'] = '';
        $files['Fornamn'] = '';
        $files['Efternamn'] = '';

//        foreach ($resultset as $s => $trc) {
//           echo $trc;
//        }

        foreach($resultset as $item) {

            $dnf = $item['DNF'];
            $dns = $item['DNS'];
            $time = $item['Tid'];


            $files = array();
            $files['ID'] = $item['ID'];
            $files['Fornamn'] = $item['Fornamn'];
            $files['Efternamn'] = $item['Efternamn'];
            $files['Klubb'] = $item['Klubb'];
            $files['last checkpoint'] = $item['Sista'];

            if($item['mal'] == true){
                if($item['Tid'] == null){
                    $files['Tid'] = "";
                } else {
                    $files['Tid'] = $item['Tid'];
                }

            }
            if($time == null & $item['mal'] == false){
                $files['Tid'] = "";
                if($dnf == true){
                    $files['Tid'] = 'DNF';
                }
                if($dns == true){
                    $files['Tid'] = 'DNS';
                    $files['last checkpoint'] = '';
                }
            }

            array_push($resultArray,$files);
        }

        return $resultArray;


    }


    private function getResultArrayDynamic($resultset, bool $bana): array {

        $resultArray = array();

        $files = array();
        $files['ID'] = '';
        $files['Fornamn'] = '';
        $files['Efternamn'] = '';

//        foreach ($resultset as $s => $trc) {
//           echo $trc;
//        }

        foreach($resultset as $item) {

            $dnf = $item['DNF'];
            $dns = $item['DNS'];
            $time = $item['Tid'];


            $files = array();
            $files['ID'] = $item['ID'];
            $files['Fornamn'] = $item['Fornamn'];
            $files['Efternamn'] = $item['Efternamn'];
            if($bana == true){
                $files['Bana'] = $item['bana'];
            }

            $files['Klubb'] = $item['Klubb'];
            $files['last checkpoint'] = $item['Sista'];

            if($item['mal'] == true){
                if($item['Tid'] == null){
                    $files['Tid'] = "";
                } else {
                    $files['Tid'] = $item['Tid'];
                }

            }
            if($time == null & $item['mal'] == false){
                $files['Tid'] = "";
                if($dnf == true){
                    $files['Tid'] = 'DNF';
                }
                if($dns == true){
                    $files['Tid'] = 'DNS';
                    $files['last checkpoint'] = '';
                }
            }

            array_push($resultArray,$files);
        }

        return $resultArray;


    }


    private function getTrackArray($resultset): array
    {

        $resultArray = array();

        $files = array();
        $files['ID'] = '';
        $files['Fornamn'] = '';
        $files['Efternamn'] = '';
//        foreach ($resultset as $s => $trc) {
//           echo $trc;
//        }

        foreach ($resultset as $item) {

            $dnf = $item['DNF'];
            $dns = $item['DNS'];
            $time = $item['Tid'];


            $files = array();
            $files['ID'] = $item['ID'];
            $files['Fornamn'] = $item['Fornamn'];
            $files['Efternamn'] = $item['Efternamn'];
            $files['Klubb'] = $item['Klubb'];
            $files['Senaste checkpoint'] = $item['Sista'];
            $files['Passerat'] = $item['passedtime'];
            $files['Status'] = '';

            if($item['mal'] == true){

                    $files['Status'] = 'Finish';
            }
            if($time == null & $item['mal'] == false){
                $files['Status'] = "";
                if($dnf == true){
                    $files['Status'] = 'DNF';
                }
                if($dns == true){
                    $files['Status'] = 'DNS';
                    $files['Senaste checkpoint'] = '';
                }
            }

            array_push($resultArray, $files);


        }
        return $resultArray;
    }

    public function trackParticipantsOnEvent(string $track_uid):array{
        return array();

    }

    public function resultForContestant(string $competitor_uid, string $track_uid, $event_uid)
    {

        $sql = $this->sqls('baseResultSql');

        if($track_uid != null || $track_uid != ""){
           $sql = $sql . 'where revent.track_uid=:track_uid';
        }

        if($event_uid != null || $event_uid != ""){
            if (strpos($sql, 'where') == true ) {
                $sql = $sql . ' and revent.event_uid=:event_uid';
            } else {
                $sql = $sql . ' where revent.event_uid=:event_uid';
            }
        }



        if($competitor_uid != null || $competitor_uid != ""){
            if (strpos($sql, 'where') == true ) {
                $sql = $sql . ' and revent.competitor_uid=:competitor_uid';
            } else {
                $sql = $sql . ' where revent.competitor_uid=:competitor_uid';
            }
        }
        $statement = $this->connection->prepare($sql);

        if (strpos($sql, 'track_uid=:track_uid') == true ){
            $statement->bindParam(':track_uid', $track_uid);
        }


        if (strpos($sql, 'competitor_uid=:competitor_uid') == true ){
            $statement->bindParam(':competitor_uid', $competitor_uid);
        }

        if (strpos($sql, 'event_uid=:event_uid') == true ){
            $statement->bindParam(':event_uid', $event_uid);
        }


        $statement->execute();
        $resultset = $statement->fetchAll(PDO::FETCH_ASSOC);


        return $this->getResultArrayDynamic($resultset, true);


    }

    public function sqls($type)
    {
        $resultsqls['resultsForEvent'] = 'select revent.startnumber AS ID, revent.finished as mal, revent.given_name as Fornamn, revent.family_name as Efternamn,revent.club as Klubb,revent.time as Tid, revent.dnf as DNF, revent.DNS as DNS, revent.adress as Sista  from v_result_for_event_and_track revent where revent.event_uid=:event_uid and YEAR(revent.eventstart) >=:year and YEAR(revent.eventend) <=:year and revent.dnf = true or revent.dns = true or revent.finished = true';
        $resultsqls['baseResultSql'] = 'select revent.startnumber AS ID, revent.bana , revent.finished as mal, revent.given_name as Fornamn, revent.family_name as Efternamn,revent.club as Klubb,revent.time as Tid, revent.dnf as DNF, revent.DNS as DNS, revent.adress as Sista  from v_result_for_event_and_track revent ';
        return $resultsqls[$type];
        // TODO: Implement sqls() method.
    }


}