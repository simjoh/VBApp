<?php

namespace App\Domain\Model\Result\Repository;

use App\common\Repository\BaseRepository;
use PDO;

class ResultRepository extends BaseRepository
{

    /**
     * Constructor.
     *
     * @param PDO $connection The database connection
     */
    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }


    public function getResultsForEvent(string $event_uid, string $year, bool $showtrackinfo): ?array
    {

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


        $dnfresults = $this->getResultArrayDynamic($dnfresultset, $showtrackinfo);
        $dnsresults = $this->getResultArrayDynamic($dnsresultset, $showtrackinfo);
        $resultarray = $this->getResultArrayDynamic($resultset, $showtrackinfo);
        $resultarray = array_merge($resultarray, $dnfresults);
        return array_merge($resultarray, $dnsresults);


    }

    public function getResultsForYear(string $year, bool $showtrackinfo): ?array
    {

        $statement = $this->connection->prepare($this->sqls('resultsForYear'));
        $statement->bindParam(':year', $year);
        $statement->execute();
        $resultset = $statement->fetchAll(PDO::FETCH_ASSOC);

        $dnsstatement = $this->connection->prepare($this->sqls('dnsYear'));
        $dnsstatement->bindParam(':year', $year);
        $dnsstatement->execute();
        $dnsresultset = $dnsstatement->fetchAll(PDO::FETCH_ASSOC);

        $dnfstatement = $this->connection->prepare($this->sqls('dnfYear'));
        $dnfstatement->bindParam(':year', $year);
        $dnfstatement->execute();
        $dnfresultset = $dnfstatement->fetchAll(PDO::FETCH_ASSOC);


        $dnfresults = $this->getResultArrayDynamic($dnfresultset, $showtrackinfo);
        $dnsresults = $this->getResultArrayDynamic($dnsresultset, $showtrackinfo);
        $resultarray = $this->getResultArrayDynamic($resultset, $showtrackinfo);
        $resultarray = array_merge($resultarray, $dnfresults);
        return array_merge($resultarray, $dnsresults);


    }

    public function getResultsForEventNew(string $event_uid, bool $showtrackinfo): ?array
    {

        $statement = $this->connection->prepare($this->sqls('resultsForEventNew'));
        $statement->bindParam(':event_uid', $event_uid);
        $statement->execute();
        $resultset = $statement->fetchAll(PDO::FETCH_ASSOC);

        $dnsstatement = $this->connection->prepare($this->sqls('dnsOnEventandtrackNew'));
        $dnsstatement->bindParam(':event_uid', $event_uid);
        $dnsstatement->execute();
        $dnsresultset = $dnsstatement->fetchAll(PDO::FETCH_ASSOC);

        $dnfstatement = $this->connection->prepare($this->sqls('dnfonEventNew'));
        $dnfstatement->bindParam(':event_uid', $event_uid);
        $dnfstatement->execute();
        $dnfresultset = $dnfstatement->fetchAll(PDO::FETCH_ASSOC);

        // $dnfresults = $this->getResultArrayDynamic($dnfresultset, $showtrackinfo);
        //   $dnsresults = $this->getResultArrayDynamic($dnsresultset, $showtrackinfo);
        // $resultarray = $this->getResultArrayDynamic($resultset, $showtrackinfo);
        $resultarray = array_merge($resultset, $dnfresultset);
        return array_merge($resultarray, $dnsresultset);

    }


    public function trackParticipantsOnTrack(string $event_uid, array $track_uid): array
    {


        $track_uids = array();
        foreach ($track_uid as $track) {
            array_push($track_uids, $track->getTrackUid());
        }


        $in = str_repeat('?,', count($track_uids) - 1) . '?';


        $sql = "select revent.startnumber AS ID, revent.track_uid as TRACK_UID, revent.bana as Bana, revent.finished as mal,  revent.family_name as Efternamn, revent.given_name as Fornamn,revent.club as Klubb,revent.time as Tid, revent.dnf as DNF, revent.DNS as DNS, revent.adress as Sista, revent.passeded_date_time as passedtime , revent.competitor_uid as competitor from v_track_contestant_on_event_and_track revent where revent.track_uid  IN ($in) order by revent.family_name , revent.given_name;";

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
        return $trackinginfo;
    }

    public function trackParticipant(?string $competitorUid, ?string $trackUid)
    {
        $statement = $this->connection->prepare('select * from  participant_checkpoint  revent inner join checkpoint c on c.checkpoint_uid = revent.checkpoint_uid inner join site s on s.site_uid = c.site_uid inner join participant p on p.participant_uid = revent.participant_uid where revent.participant_uid=:participant_uid order by c.distance asc ');
        $statement->bindParam(':participant_uid', $competitorUid);
        $statement->execute();

        $trackinginfo = $statement->fetchAll(PDO::FETCH_ASSOC);


        return $trackinginfo;
    }


    public function resultOnEvent(array $track_uid)
    {
        $track_uids = array();
        foreach ($track_uid as $track) {
            array_push($track_uids, $track->track_uid);
        }
        $in = str_repeat('?,', count($track_uids) - 1) . '?';
        $statement = $this->connection->prepare("select p.startnumber as  startnumber, t.title, com.given_name as fornamn, cl.title as klubb, p.time as tid, p.dns, p.dnf , com.family_name as efternamn, t.track_uid, p.participant_uid, co.flag_url_svg as flagga, p.brevenr   from competitors c inner join participant p on p.competitor_uid = c.competitor_uid inner join competitor_info ci on ci.competitor_uid = c.competitor_uid inner join competitors com on com.competitor_uid = p.competitor_uid inner join club cl on cl.club_uid = p.club_uid left join countries co on co.country_id = ci.country_id inner join track t on t.track_uid = p.track_uid where t.track_uid in ($in) and t.active = false order by com.family_name, com.given_name, t.distance;");
        $statement->execute($track_uids);
        $resultset = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $resultset;

    }

    public function resultOnTrack(string $track_uid)
    {
        $statement = $this->connection->prepare("select distinct(p.startnumber) as  startnumber, t.title, com.given_name as fornamn, cl.title as klubb, p.time as tid, p.dns, p.dnf , com.family_name as efternamn, t.track_uid, p.participant_uid, co.flag_url_svg as flagga, p.brevenr  from competitors c inner join participant p on p.competitor_uid = c.competitor_uid left join competitor_info ci on ci.competitor_uid = c.competitor_uid inner join competitors com on com.competitor_uid = p.competitor_uid inner join club cl on cl.club_uid = p.club_uid left join countries co on co.country_id = ci.country_id inner join track t on t.track_uid = p.track_uid where t.track_uid=:track_uid and t.active = false order by com.family_name, com.given_name;");
        $statement->bindParam(':track_uid', $track_uid);
        $statement->execute();
        $resultset = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $resultset;
    }

    private function getResultArray($resultset): array
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
            $files['Efternamn'] = $item['Efternamn'];
            $files['Fornamn'] = $item['Fornamn'];
            $files['Klubb'] = $item['Klubb'];
            $files['Last checkpoint'] = $item['Sista'];

            if ($item['mal'] == true) {
                if ($item['Tid'] == null) {
                    $files['Tid'] = "";
                } else {
                    $lastcheckpoint_adress = $this->lastCheckpointOnTrack($item['track_uid']);
                    if ($lastcheckpoint_adress != $item['Sista']) {
                        $files['last checkpoint'] = $lastcheckpoint_adress;
                    }
                    if (strlen($item['Tid']) == 4) {
                        $tidarray = explode(":", $item['Tid']);
                        if ($tidarray[1] == 0) {
                            $files['Tid'] = $tidarray[0] . ":" . "00";
                        } else {
                            $files['Tid'] = $tidarray[0] . ":" . "0" . $tidarray[1];
                        }

                    } else {
                        $files['Tid'] = $item['Tid'];
                    }
                }

            }
            if ($time == null & $item['mal'] == false) {
                $files['Tid'] = "";
                if ($dnf == true) {
                    $files['Tid'] = 'DNF';
                }
                if ($dns == true) {
                    $files['Tid'] = 'DNS';
                    $files['last checkpoint'] = '';
                }
            }

            array_push($resultArray, $files);
        }

        return $resultArray;


    }


    private function getResultArrayDynamic($resultset, bool $bana): array
    {

        $resultArray = array();

        $files = array();
        $files['ID'] = '';
        $files['Efternamn'] = '';
        $files['Fornamn'] = '';


//        foreach ($resultset as $s => $trc) {
//           echo $trc;
//        }

        foreach ($resultset as $item) {

            $dnf = $item['DNF'];
            $dns = $item['DNS'];
            $time = $item['Tid'];


            $files = array();
            $files['ID'] = $item['ID'];
            $files['Efternamn'] = $item['Efternamn'];
            $files['Förnamn'] = $item['Fornamn'];
            if ($bana == true) {
                $files['Bana'] = $item['bana'];
            }

            $files['Klubb'] = $item['Klubb'];
            $files['Last checkpoint'] = $item['Sista'];

            if ($item['mal'] == true) {
                if ($item['Tid'] == null) {
                    $files['Tid'] = "";
                } else {
                    $lastcheckpoint_adress = $this->lastCheckpointOnTrack($item['track_uid']);
                    View
                    v_dns_on_event_and_track
                    View
                    v_partisipant_passed_checkpoints
                    View
                    v_partisipant_to_pass_checkpoint
                    View
                    v_race_statistic
                    View
                    v_result_for_event_and_track
                    View
                    v_track_contestant_on_event_and_track
        
            Server: s687.loopia.se
            Database: vasterbottenbrevet_se
            Table: competitors
        
            Browse Browse
            Structure Structure
            SQL SQL
            Search Search
            Insert Insert
            Export Export
            Import Import
            Operations Operations
        
        Page-related settings Click on the bar to scroll to top of page
        SQL Query Console Console
        ascending
        descending
        Order:
        Debug SQL
        Execution order
        Time taken
        Order by:
        Group queries
        Some error occurred while getting SQL debug info.
        Options
        Set default
        Always expand query messages
        Show query history at start
        Show current browsing query
        Execute queries on Enter and insert new line with Shift+Enter. To make this permanent, view settings.
        Switch to dark theme
        
        Current selection does not contain a unique column. Grid edit, checkbox, Edit, Copy and Delete features are not available. Documentation
        Showing rows 0 - 24 (35 total, Query took 0.0081 seconds.)
        select DISTINCT(p.startnumber) as startnumber, t.title, com.given_name as fornamn, cl.title as klubb, p.time as tid, p.dns, p.dnf , com.family_name as efternamn, t.track_uid, p.participant_uid, co.flag_url_svg as flagga, p.brevenr from competitors c inner join participant p on p.competitor_uid = c.competitor_uid left join competitor_info ci on ci.competitor_uid = c.competitor_uid inner join competitors com on com.competitor_uid = p.competitor_uid inner join club cl on cl.club_uid = p.club_uid left join countries co on co.country_id = ci.country_id inner join track t on t.track_uid = p.track_uid where t.track_uid= '3b48ab13-599a-43b8-8fb4-566be94820e3' and t.active = false order by com.family_name, com.given_name;
        Profiling [ Edit inline ] [ Edit ] [ Explain SQL ] [ Create PHP code ] [ Refresh ]
            
            
            
            |
            Show all 	|
                    Number of rows:
                Filter rows: 	Sort by key:
            
        startnumber 	title 	fornamn 	klubb 	tid 	dns 	dnf 	efternamn 	track_uid 	participant_uid 	flagga 	brevenr 	
        1035 	Södertörns Pärla 200 	Michael 	Independent Sweden 	NULL 	1 	0 	Ah-King 	3b48ab13-599a-43b8-8fb4-566be94820e3 	b37774dd-f21e-4307-917c-3babb34c075b 	https://flagcdn.com/se.svg 	0
        1018 	Södertörns Pärla 200 	Jonas 	Randonneur Stockholm 	8:11 	0 	0 	Ahlström 	3b48ab13-599a-43b8-8fb4-566be94820e3 	242e5e67-22db-4199-a71b-5e056f603698 	https://flagcdn.com/se.svg 	988752
        1029 	Södertörns Pärla 200 	Nicklas 	Fredrikshofs IF CK 	NULL 	1 	0 	Almlöf 	3b48ab13-599a-43b8-8fb4-566be94820e3 	e48a61be-13dd-4557-bed8-8c9bf700daf4 	https://flagcdn.com/se.svg 	0
        1006 	Södertörns Pärla 200 	Mats 	Randonneur Stockholm 	7:15 	0 	0 	Ängehult 	3b48ab13-599a-43b8-8fb4-566be94820e3 	226bec54-d2cb-4b5f-acb7-eba237c97fff 	https://flagcdn.com/se.svg 	988769
        1010 	Södertörns Pärla 200 	David 	Independent Sweden 	7:23 	0 	0 	Berling 	3b48ab13-599a-43b8-8fb4-566be94820e3 	15053a9f-1835-457a-883e-fa4f06fc5213 	https://flagcdn.com/se.svg 	988748
        1007 	Södertörns Pärla 200 	Sven 	Randonneur Stockholm 	8:40 	0 	0 	Cederberg 	3b48ab13-599a-43b8-8fb4-566be94820e3 	ddb6aca7-f92e-4f72-be4c-91db8bd9e069 	https://flagcdn.com/se.svg 	988753
        1030 	Södertörns Pärla 200 	Tomas 	Randonneur Stockholm 	10:57 	0 	0 	Clemmedsson 	3b48ab13-599a-43b8-8fb4-566be94820e3 	0562a41e-9e55-40b6-b40d-019ae1b70f9b 	https://flagcdn.com/se.svg 	988754
        1003 	Södertörns Pärla 200 	Niklas 	Randonneur Stockholm 	NULL 	1 	0 	Edvall 	3b48ab13-599a-43b8-8fb4-566be94820e3 	821da58e-8f1b-4030-9404-a3d811485d4a 	https://flagcdn.com/se.svg 	0
        1021 	Södertörns Pärla 200 	Erland 	Randonneur Stockholm 	8:11 	0 	0 	Ekheden 	3b48ab13-599a-43b8-8fb4-566be94820e3 	f05dc515-b62c-417d-9fe1-05a89ad3fceb 	https://flagcdn.com/se.svg 	988755
        1028 	Södertörns Pärla 200 	Magnus 	Randonneur Stockholm 	9:53 	0 	0 	Eklund 	3b48ab13-599a-43b8-8fb4-566be94820e3 	a2b978b7-5154-4e0e-abaf-82516b897045 	https://flagcdn.com/se.svg 	988756
        1015 	Södertörns Pärla 200 	Jenny 	CK Uni 	8:39 	0 	0 	Envall 	3b48ab13-599a-43b8-8fb4-566be94820e3 	657f154d-556e-49db-ad23-c21a54c1e6d1 	https://flagcdn.com/se.svg 	988745
        1033 	Södertörns Pärla 200 	Mikael 	CK Uni 	8:39 	0 	0 	Envall 	3b48ab13-599a-43b8-8fb4-566be94820e3 	de668d4c-7376-467c-9ce0-3b8dc00f2d85 	https://flagcdn.com/se.svg 	988746
        1013 	Södertörns Pärla 200 	Johan 	Randonneur Stockholm 	8:19 	0 	0 	Fernberger 	3b48ab13-599a-43b8-8fb4-566be94820e3 	52d2b7c1-8316-42cc-bc60-c176426c8184 	https://flagcdn.com/se.svg 	988757
        1008 	Södertörns Pärla 200 	Kjell 	Randonneur Stockholm 	8:12 	0 	0 	Fredriksson 	3b48ab13-599a-43b8-8fb4-566be94820e3 	d25851b9-d381-4c98-9881-88cb936d2776 	https://flagcdn.com/se.svg 	988758
                    if ($lastcheckpoint_adress != $item['Sista'] && $lastcheckpoint_adress != null) {
                        $files['Last checkpoint'] = $lastcheckpoint_adress;
                    } else {
                        $files['Last checkpoint'] = $item['Sista'];
                    }

                    if (strlen($item['Tid']) == 4) {
                        $tidarray = explode(":", $item['Tid']);
                        if ($tidarray[1] == 0) {
                            $files['Tid'] = $tidarray[0] . ":" . "00";
                        } else {
                            $files['Tid'] = $tidarray[0] . ":" . "0" . $tidarray[1];
                        }

                    } else {
                        $files['Tid'] = $item['Tid'];
                    }


                }

            }
            if ($time == null & $item['mal'] == false) {
                $files['Tid'] = "";
                if ($dnf == true) {
                    $files['Tid'] = 'DNF';
                }
                if ($dns == true) {
                    $files['Tid'] = 'DNS';
                    $files['Last checkpoint'] = '';
                }
            }

            array_push($resultArray, $files);
        }

        return $resultArray;


    }


    private function getTrackArray($resultset): array
    {

        $resultArray = array();

        $files = array();
        $files['ID'] = '';
        $files['Efternamn'] = '';
        $files['Fornamn'] = '';

//        foreach ($resultset as $s => $trc) {
//           echo $trc;
//        }

        foreach ($resultset as $item) {

            $dnf = $item['DNF'];
            $dns = $item['DNS'];
            $time = $item['Tid'];


            $files = array();
            $files['ID'] = $item['ID'];
            $files['Efternamn'] = $item['Efternamn'];
            $files['Förnamn'] = $item['Fornamn'];
            $files['Klubb'] = $item['Klubb'];
            $files['Bana'] = $item['Bana'];
            $files['Kontroll'] = $item['Sista'];
            $files['Stämplat'] = $item['passedtime'];
            $files['Status'] = '';

            if ($item['mal'] == true) {

                $files['Status'] = 'FIN';
            }
            if ($time == null & $item['mal'] == false) {
                $files['Status'] = "";
                if ($dnf == true) {
                    $files['Status'] = 'DNF';
                }
                if ($dns == true) {
                    $files['Status'] = 'DNS';
                    $files['Senaste checkpoint'] = '';
                }
            }
            $files['competitor_uid'] = $item['competitor'];
            $files['track_uid'] = $item['TRACK_UID'];


            array_push($resultArray, $files);


        }
        return $resultArray;
    }

    public function trackParticipantsOnEvent(string $track_uid): array
    {
        return array();

    }

    public function resultForContestant(string $competitor_uid, string $track_uid, $event_uid)
    {

        $sql = $this->sqls('baseResultSql');

        if ($track_uid != null || $track_uid != "") {
            $sql = $sql . 'where revent.track_uid=:track_uid';
        }

        if ($event_uid != null || $event_uid != "") {
            if (strpos($sql, 'where') == true) {
                $sql = $sql . ' and revent.event_uid=:event_uid';
            } else {
                $sql = $sql . ' where revent.event_uid=:event_uid';
            }
        }


        if ($competitor_uid != null || $competitor_uid != "") {
            if (strpos($sql, 'where') == true) {
                $sql = $sql . ' and revent.competitor_uid=:competitor_uid';
            } else {
                $sql = $sql . ' where revent.competitor_uid=:competitor_uid';
            }
        }
        $statement = $this->connection->prepare($sql);

        if (strpos($sql, 'track_uid=:track_uid') == true) {
            $statement->bindParam(':track_uid', $track_uid);
        }


        if (strpos($sql, 'competitor_uid=:competitor_uid') == true) {
            $statement->bindParam(':competitor_uid', $competitor_uid);
        }

        if (strpos($sql, 'event_uid=:event_uid') == true) {
            $statement->bindParam(':event_uid', $event_uid);
        }


        $statement->execute();
        $resultset = $statement->fetchAll(PDO::FETCH_ASSOC);


        return $this->getResultArrayDynamic($resultset, true);


    }

    public function lastCheckpointOnTrack(string $trackUid): ?string
    {

        $track_checkpoint_statement = $this->connection->prepare($this->sqls('lastCheckpointOnTrack'));
        $track_checkpoint_statement->bindParam(':track_uid', $trackUid);
        $track_checkpoint_statement->execute();
        $lastcheckpointresult = $track_checkpoint_statement->fetch();
        if ($lastcheckpointresult == null) {
            return null;
        } else {
            return $lastcheckpointresult['adress'];
        }

    }

    public function isSuperrandonneur(): bool
    {

        "SELECT * FROM `participant` p inner join track t on t.track_uid = p.track_uid inner join event e on e.event_uid = t.event_uid where finished = true and t.distance in (200,300,400,600)  and competitor_uid = '43aef45b-851b-4e5c-993b-222b0fc8af8b'";

        return true;
    }


    public function sqls($type)
    {
        $resultsqls['resultsForEvent'] = 'select revent.startnumber AS ID, revent.finished as mal, revent.bana, revent.given_name as Fornamn, revent.family_name as Efternamn,revent.club as Klubb,revent.time as Tid, revent.dnf as DNF, revent.DNS as DNS, revent.adress as Sista, revent.track_uid  from v_result_for_event_and_track revent where revent.event_uid=:event_uid and YEAR(revent.eventstart) >=:year and YEAR(revent.eventend) <=:year and revent.finished = true';
        $resultsqls['resultsForEventNew'] = 'select revent.startnumber AS ID, revent.finished as mal, revent.bana, revent.given_name as Fornamn, revent.family_name as Efternamn,revent.club as Klubb,revent.time as Tid, revent.dnf as DNF, revent.DNS as DNS, revent.adress as Sista, revent.track_uid  from v_result_for_event_and_track revent where revent.event_uid=:event_uid  and revent.finished = true';
        $resultsqls['baseResultSql'] = 'select revent.startnumber AS ID, revent.bana , revent.finished as mal, revent.given_name as Fornamn, revent.family_name as Efternamn,revent.club as Klubb,revent.time as Tid, revent.dnf as DNF, revent.DNS as DNS, revent.adress as Sista  from v_result_for_event_and_track revent ';
        $resultsqls['dnsOnEventandtrack'] = 'select revent.startnumber AS ID,revent.bana, revent.finished as mal, revent.given_name as Fornamn, revent.family_name as Efternamn,revent.club as Klubb,revent.time as Tid, revent.dnf as DNF, revent.DNS as DNS, revent.adress as Sista  from v_dns_on_event_and_track revent where revent.event_uid=:event_uid and YEAR(revent.eventstart) >=:year and YEAR(revent.eventend) <=:year;';
        $resultsqls['dnsOnEventandtrackNew'] = 'select revent.startnumber AS ID,revent.bana, revent.finished as mal, revent.given_name as Fornamn, revent.family_name as Efternamn,revent.club as Klubb,revent.time as Tid, revent.dnf as DNF, revent.DNS as DNS, revent.adress as Sista  from v_dns_on_event_and_track revent where revent.event_uid=:event_uid;';
        $resultsqls['lastCheckpointOnTrack'] = "select s.adress from track t inner join track_checkpoint tc on tc.track_uid = t.track_uid inner join checkpoint c on c.checkpoint_uid = tc.checkpoint_uid inner join site s on s.site_uid = c.site_uid where tc.track_uid=:track_uid and c.distance in (select max(distance) from checkpoint);";
        $resultsqls['dnfonEvent'] = 'select revent.startnumber AS ID, revent.finished as mal, revent.bana, revent.given_name as Fornamn, revent.family_name as Efternamn,revent.club as Klubb,revent.time as Tid, revent.dnf as DNF, revent.DNS as DNS, revent.adress as Sista, revent.track_uid  from v_result_for_event_and_track revent where revent.event_uid=:event_uid and YEAR(revent.eventstart) >=:year and YEAR(revent.eventend) <=:year and revent.finished = false and revent.dnf = true';
        $resultsqls['dnfonEventNew'] = 'select revent.startnumber AS ID, revent.finished as mal, revent.bana, revent.given_name as Fornamn, revent.family_name as Efternamn,revent.club as Klubb,revent.time as Tid, revent.dnf as DNF, revent.DNS as DNS, revent.adress as Sista, revent.track_uid  from v_result_for_event_and_track revent where revent.event_uid=:event_uid;';
        $resultsqls['resultsForYear'] = 'select revent.startnumber AS ID, revent.finished as mal, revent.bana, revent.given_name as Fornamn, revent.family_name as Efternamn,revent.club as Klubb,revent.time as Tid, revent.dnf as DNF, revent.DNS as DNS, revent.adress as Sista, revent.track_uid  from v_result_for_event_and_track revent where  YEAR(revent.eventstart) >=:year and YEAR(revent.eventend) <=:year and revent.finished = true';
        $resultsqls['dnsYear'] = 'select revent.startnumber AS ID,revent.bana, revent.finished as mal, revent.given_name as Fornamn, revent.family_name as Efternamn,revent.club as Klubb,revent.time as Tid, revent.dnf as DNF, revent.DNS as DNS, revent.adress as Sista  from v_dns_on_event_and_track revent where YEAR(revent.eventstart) >=:year and YEAR(revent.eventend) <=:year;';
        $resultsqls['dnfYear'] = 'select revent.startnumber AS ID, revent.finished as mal, revent.bana, revent.given_name as Fornamn, revent.family_name as Efternamn,revent.club as Klubb,revent.time as Tid, revent.dnf as DNF, revent.DNS as DNS, revent.adress as Sista, revent.track_uid  from v_result_for_event_and_track revent where YEAR(revent.eventstart) >=:year and YEAR(revent.eventend) <=:year and revent.finished = false and revent.dnf = true';


        return $resultsqls[$type];
        // TODO: Implement sqls() method.
    }


//SELECT
//created_at,
//previous_timestamp,
//    CASE
//    WHEN TIMESTAMPDIFF(MINUTE, previous_timestamp, created_at) DIV 60 < 10
//        THEN CONCAT(
//            CAST(TIMESTAMPDIFF(MINUTE, previous_timestamp, created_at) DIV 60 AS CHAR),
//            ':',
//            LPAD(TIMESTAMPDIFF(MINUTE, previous_timestamp, created_at) MOD 60, 2, '0')
//        )
//        ELSE CONCAT(
//            TIMESTAMPDIFF(MINUTE, previous_timestamp, created_at) DIV 60,
//            ':',
//            LPAD(TIMESTAMPDIFF(MINUTE, previous_timestamp, created_at) MOD 60, 2, '0')
//        )
//    END AS formatted_time_difference
//FROM (
//    SELECT
//        created_at,
//        LAG(created_at) OVER (ORDER BY created_at) AS previous_timestamp
//    FROM
//        registrations
//    WHERE
//        course_uid = 'd32650ff-15f8-4df1-9845-d3dc252a7a84'
//) AS t;


}