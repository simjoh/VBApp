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


    public function getResultsForEvent(string $event_uid, string $year, bool $showtrackinfo): ?array {

        $statement = $this->connection->prepare($this->sqls('resultsForEvent'));
        $statement->bindParam(':event_uid', $event_uid);
        $statement->bindParam(':year', $year);
        $statement->execute();
        $resultset = $statement->fetchAll(PDO::FETCH_ASSOC);


        $dnsstatement = $this->connection->prepare($this->sqls('dnsOnEventandtrack'));
        $dnsstatement->bindParam(':event_uid', $event_uid);
        $dnsstatement->bindParam(':year', $year);
        $dnsstatement->execute();
        $dnsresultset = $dnsstatement->fetchAll(PDO::FETCH_ASSOC);

        $dnfstatement = $this->connection->prepare($this->sqls('dnfonEvent'));
        $dnfstatement->bindParam(':event_uid', $event_uid);
        $dnfstatement->bindParam(':year', $year);
        $dnfstatement->execute();
        $dnfresultset = $dnfstatement->fetchAll(PDO::FETCH_ASSOC);




        $dnfresults =  $this->getResultArrayDynamic($dnfresultset,$showtrackinfo);
        $dnsresults =  $this->getResultArrayDynamic($dnsresultset,$showtrackinfo);
        $resultarray = $this->getResultArrayDynamic($resultset, $showtrackinfo);
        $resultarray =array_merge($resultarray,$dnfresults);
        return array_merge($resultarray,$dnsresults);


    }


    public function trackParticipantsOnTrack(string $event_uid, array $track_uid):array{


        $track_uids = array();
        foreach ($track_uid as $track){
            array_push($track_uids,$track->getTrackUid());
        }

        $in  = str_repeat('?,', count($track_uids) - 1) . '?';


        $sql = " select revent.startnumber AS ID, revent.bana as Bana, revent.finished as mal, revent.given_name as Fornamn, revent.family_name as Efternamn,revent.club as Klubb,revent.time as Tid, revent.dnf as DNF, revent.DNS as DNS, revent.adress as Sista, revent.passeded_date_time as passedtime  from v_track_contestant_on_event_and_track revent where revent.track_uid  IN ($in);";

        $statement = $this->connection->prepare($sql);
        $statement->execute($track_uids);
        $resultset = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $this->getTrackArray($resultset);

    }

    public function trackParticipantOnTrack(?string $competitorUid, ?string $trackUid)
    {
        $statement = $this->connection->prepare('select * from  v_track_contestant_on_event_and_track revent  where revent.competitor_uid=:competitor_uid and revent.track_uid=:track_uid');
        $statement->bindParam(':competitor_uid', $competitorUid);
        $statement->bindParam(':track_uid', $trackUid);

        $statement->execute();
        $trackinginfo = $statement->fetchAll(PDO::FETCH_ASSOC);
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
            $files['Last checkpoint'] = $item['Sista'];

            if($item['mal'] == true){
                if($item['Tid'] == null){
                    $files['Tid'] = "";
                } else {
                    $lastcheckpoint_adress = $this->lastCheckpointOnTrack($item['track_uid']);
                    if($lastcheckpoint_adress != $item['Sista']){
                        $files['last checkpoint'] = $lastcheckpoint_adress;
                    }
                    if(strlen($item['Tid']) == 4){
                        $tidarray = explode(":",$item['Tid']);
                        if($tidarray[1] == 0){
                            $files['Tid'] = $tidarray[0] . ":" . "00";
                        } else {
                            $files['Tid'] = $tidarray[0] . ":"  . "0" . $tidarray[1] ;
                        }

                    } else {
                        $files['Tid'] = $item['Tid'];
                    }
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
            $files['Förnamn'] = $item['Fornamn'];
            $files['Efternamn'] = $item['Efternamn'];
            if($bana == true){
                $files['Bana'] = $item['bana'];
            }

            $files['Klubb'] = $item['Klubb'];
            $files['Last checkpoint'] = $item['Sista'];

            if($item['mal'] == true){
                if($item['Tid'] == null){
                    $files['Tid'] = "";
                } else {
                    $lastcheckpoint_adress = $this->lastCheckpointOnTrack($item['track_uid']);
                    if($lastcheckpoint_adress != $item['Sista'] && $lastcheckpoint_adress != null){
                        $files['Last checkpoint'] = $lastcheckpoint_adress;
                    } else {
                        $files['Last checkpoint'] = $item['Sista'];
                    }

                    if(strlen($item['Tid']) == 4){
                        $tidarray = explode(":",$item['Tid']);
                        if($tidarray[1] == 0){
                            $files['Tid'] = $tidarray[0] . ":" . "00";
                        } else {
                            $files['Tid'] = $tidarray[0] . ":"  . "0" . $tidarray[1] ;
                        }

                    } else {
                        $files['Tid'] = $item['Tid'];
                    }



                }

            }
            if($time == null & $item['mal'] == false){
                $files['Tid'] = "";
                if($dnf == true){
                    $files['Tid'] = 'DNF';
                }
                if($dns == true){
                    $files['Tid'] = 'DNS';
                    $files['Last checkpoint'] = '';
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
            $files['Förnamn'] = $item['Fornamn'];
            $files['Efternamn'] = $item['Efternamn'];
            $files['Klubb'] = $item['Klubb'];
            $files['Bana'] = $item['Bana'];
            $files['Kontroll'] = $item['Sista'];
            $files['Stämplat'] = $item['passedtime'];
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

    public function lastCheckpointOnTrack(string $trackUid): ?string {

        $track_checkpoint_statement = $this->connection->prepare($this->sqls('lastCheckpointOnTrack'));
        $track_checkpoint_statement->bindParam(':track_uid', $trackUid);
        $track_checkpoint_statement->execute();
        $lastcheckpointresult = $track_checkpoint_statement->fetch();
        if($lastcheckpointresult == null){
            return null;
        } else {
            return $lastcheckpointresult['adress'];
        }

    }

    public function isSuperrandonneur(): bool {

        "SELECT * FROM `participant` p inner join track t on t.track_uid = p.track_uid inner join event e on e.event_uid = t.event_uid where finished = true and t.distance in (200,300,400,600)  and competitor_uid = '43aef45b-851b-4e5c-993b-222b0fc8af8b'";

        return true;
    }


    public function sqls($type)
    {
        $resultsqls['resultsForEvent'] = 'select revent.startnumber AS ID, revent.finished as mal, revent.bana, revent.given_name as Fornamn, revent.family_name as Efternamn,revent.club as Klubb,revent.time as Tid, revent.dnf as DNF, revent.DNS as DNS, revent.adress as Sista, revent.track_uid  from v_result_for_event_and_track revent where revent.event_uid=:event_uid and YEAR(revent.eventstart) >=:year and YEAR(revent.eventend) <=:year and revent.finished = true';
        $resultsqls['baseResultSql'] = 'select revent.startnumber AS ID, revent.bana , revent.finished as mal, revent.given_name as Fornamn, revent.family_name as Efternamn,revent.club as Klubb,revent.time as Tid, revent.dnf as DNF, revent.DNS as DNS, revent.adress as Sista  from v_result_for_event_and_track revent ';
        $resultsqls['dnsOnEventandtrack'] = 'select revent.startnumber AS ID,revent.bana, revent.finished as mal, revent.given_name as Fornamn, revent.family_name as Efternamn,revent.club as Klubb,revent.time as Tid, revent.dnf as DNF, revent.DNS as DNS, revent.adress as Sista  from v_dns_on_event_and_track revent where revent.event_uid=:event_uid and YEAR(revent.eventstart) >=:year and YEAR(revent.eventend) <=:year;';
        $resultsqls['lastCheckpointOnTrack']  =  "select s.adress from track t inner join track_checkpoint tc on tc.track_uid = t.track_uid inner join checkpoint c on c.checkpoint_uid = tc.checkpoint_uid inner join site s on s.site_uid = c.site_uid where tc.track_uid=:track_uid and c.distance in (select max(distance) from checkpoint);";
        $resultsqls['dnfonEvent'] = 'select revent.startnumber AS ID, revent.finished as mal, revent.bana, revent.given_name as Fornamn, revent.family_name as Efternamn,revent.club as Klubb,revent.time as Tid, revent.dnf as DNF, revent.DNS as DNS, revent.adress as Sista, revent.track_uid  from v_result_for_event_and_track revent where revent.event_uid=:event_uid and YEAR(revent.eventstart) >=:year and YEAR(revent.eventend) <=:year and revent.finished = false and revent.dnf = true';
        return $resultsqls[$type];
        // TODO: Implement sqls() method.
    }


}