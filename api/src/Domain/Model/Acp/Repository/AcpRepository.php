<?php

namespace App\Domain\Model\Acp\Repository;

use App\common\Repository\BaseRepository;
use App\Domain\Model\Acp\AcpReport;
use App\Domain\Model\Acp\AcpReportParicipants;
use PDO;

class AcpRepository extends BaseRepository
{
    public function __construct(PDO $connection)
    {
        parent::__construct($connection);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getAcpReportFor(string $track_uid): ?AcpReport
    {
        $result = $this->executeQuery($this->sqls('acpreportfortrack'), ['track_uid' => $track_uid]);
        return $result ? AcpReport::fromArray($result[0]) : null;
    }

    public function getParticipants(string $track_uid): ?array
    {
        $result = $this->executeQuery($this->sqls('acpreportfortrack'), ['track_uid' => $track_uid]);
        return $result;
    }

    public function getParticipantsToReport(string $track_uid, string $report_uid): array
    {
        $results = $this->executeQuery($this->sqls('getparticipantstoreport'), ['track_uid' => $track_uid, 'report_uid' => $report_uid]);
        if ($results) {
            $reports = [];
            foreach ($results as $result) {
                $reports[] = AcpReportParicipants::fromArray($result);
            }
            return $reports;  // Return the array of reports
        }
        return array();  // Return null if no results found
    }


    public function getAcpReportBy(string $reportUid): ?AcpReport
    {

        // Base query to get the report by report_uid
        $baseQuery = $this->sqls('reportbyuid');

        // Prepare the predicates array with the required conditions
        $predicates = [
            'report_uid' => $reportUid,  // Report UID is mandatory
        ];

        // Add the organizer_id predicate (always included)
        $predicates['organizer_id'] = $this->getOrganizer();

        // Merge any additional predicates provided (e.g., filter by status, type, etc.)
//        $predicates = array_merge($predicates, $additionalPredicates);

        // Add predicates to the query
        list($queryWithPredicates, $paramsWithPredicates) = $this->addPredicates($baseQuery, $predicates);

        // Execute the query and fetch the result
        $result = $this->executeQuery($queryWithPredicates, $paramsWithPredicates);

        // Return the result as an AcpReport object or null if no data is found
        return $result ? AcpReport::fromArray($result[0]) : null;


//        $result = $this->executeQuery($this->sqls('reportbyuid'), ['report_uid' => $reportUid]);
//        return $result ? AcpReport::fromArray($result[0]) : null;
    }

    public function createAcpreport(AcpReport $acpReport): ?AcpReport
    {
        $params = AcpReportParamBuilder::buildCreateParams($acpReport);
        $this->executeQuery($this->sqls('createacpreport'), $params);
        return $acpReport;
    }

    public function updateAcpreport(AcpReport $acpReport): void
    {
        $params = AcpReportParamBuilder::buildUpdateParams($acpReport);
        $this->executeQuery($this->sqls('updateacpreport'), $params);
    }

    public function approveAcpreport(string $report_uid): void
    {
        $params = AcpReportParamBuilder::buildApproveParams($report_uid);
        $this->executeQuery($this->sqls('approvepreport'), $params);
    }

    public function markAsReadyForApproval(string $report_uid): bool
    {
        $statement = $this->connection->prepare($this->sqls('markasreadyforapproval'));
        $statement->bindParam(':report_uid', $report_uid);
        $statement->execute();
        return $statement->rowCount() > 0;
    }

    public function markAsDeliveredToAcp(string $report_uid): void
    {
        $params = AcpReportParamBuilder::buildDeliveredToAcpParams($report_uid);
        $this->executeQuery($this->sqls('markAsDeliveredToAcp'), $params);
    }


    public function deleteReport(string $report_uid): bool
    {
        $statement = $this->connection->prepare("DELETE FROM acpreports WHERE report_uid = :report_uid");
        $statement->bindParam(':report_uid', $report_uid);
        $statement->execute();
        return $statement->rowCount() > 0;
    }


    public function markparticipantsAsdeliverd(string $report_uid)
    {
        $statement = $this->connection->prepare("update acpreportparicipants set delivered = 1, updated_at=:updated_at WHERE report_uid = :report_uid");
        $statement->bindParam(':report_uid', $report_uid);
        $statement->bindParam(':updated_at', $updated_at);
        $statement->execute();

    }

    public function createAcpParticipants(AcpReportParicipants $acpReportParicipants)
    {
        $params = AcpReportParticipantParamBuilder::buildCreateParams($acpReportParicipants);
        $this->executeQuery($this->sqls('createacpreport'), $params);
        return $acpReportParicipants;
    }

    public function getTracks($track_uid)
    {
        $result = $this->executeQuery('select * from track where t.track_uid=:track_uid and t.active = false and not exists(select track_uid from acpreports where approved= false and  delivered_to_acp = false)', ['track_uid' => $track_uid]);
        return $result;

    }


    public function sqls($type)
    {
        $acpsql['acpreportfortrack'] = "select t.title, com.family_name as NOM, com.given_name as PRENOM, cl.title as CLUB_DU_PARICIPANT, cl.acp_kod as ACPKOD,  p.time as DURTION,  com.gender as SEXE, t.track_uid, p.participant_uid, p.brevenr  from competitors c inner join participant p on p.competitor_uid = c.competitor_uid inner join competitor_info ci on ci.competitor_uid = c.competitor_uid inner join competitors com on com.competitor_uid = p.competitor_uid inner join club cl on cl.club_uid = p.club_uid left join countries co on co.country_id = ci.country_id inner join track t on t.track_uid = p.track_uid where t.track_uid=:track_uid and t.active = false and p.dns = false and p.dnf = false order by com.family_name, com.given_name;";
        $acpsql['getparticipantstoreport'] = "";
        $acpsql['trackstoreport'] = "SELECT * FROM track t , participant p where p.track_uid = t.track_uid and t.active = 1 and p.finished = 1";
        $acpsql['countfinishedontrack'] = "SELECT EXISTS (SELECT 1 FROM track t JOIN participant p ON p.track_uid = t.track_uid WHERE t.active = 0 AND p.finished = 1 and track_uid=:track_uid) AS has_participant;";
        $acpsql['createacpreport'] = "insert into acpreports value(report_uid=:report_uid, track_uid=:track_uid,organizer_id=:organizer_id,ready_for_approval=:ready_for_approval, marked_as_ready_for_approval_by=:marked_as_ready_for_approval_by,approved=:approved,approved_by=:approved_by,created_at=:created_at,updated_at=:updated_at, delivered_to_acp=:delivered_to_acp )";
        $acpsql['approvepreport'] = "update acpreports set approved=:approved,approved_by=: approved_by, updated_at=:updated_at where report_uid=:report_uid";
        $acpsql['updateacpreport'] = "update acpreports set marked_as_ready_for_approval_by=:marked_as_ready_for_approval_by, updated_at=:updated_at, ready_for_approval=:ready_for_approval where report_uid=:report_uid and approved is FALSE";
        $acpsql['markasreadyforapproval'] = "update acpreports set marked_as_ready_for_approval_by=:marked_as_ready_for_approval_by updated_at=:updated_at, ready_for_approval=:ready_for_approval where report_uid=:report_uid and approved  is FALSE";
        $acpsql['markAsDeliveredToAcp'] = "update acpreports set delivered_to_acp= 1, updated_at=:updated_at where report_uid=:report_uid";
        $acpsql['reportbyuid'] = "SELECT * FROM acpreports where report_uid=:report_uid";
        return $acpsql[$type];
    }




}
