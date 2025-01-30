<?php

namespace App\Domain\Model\Acp\Repository;

use App\common\CurrentUser;
use App\common\Repository\BaseRepository;
use PDO;
use Ramsey\Uuid\Uuid;

class AcpRepository extends BaseRepository
{
    public function __construct(PDO $connection)
    {
        parent::__construct($connection);
        $this->connection = $connection;
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getAcpReportFor(string $track_uid): array
    {
        $statement = $this->connection->prepare($this->sqls('acpreportfortrack'));
        $statement->bindParam(':track_uid', $track_uid);
        $statement->execute();
        $resultset = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $resultset;
    }

    public function getTrackstoReport(string $track_uid): array
    {
        $statement = $this->connection->prepare($this->sqls('trackstoreport'));
        $statement->execute();
        $resultset = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $resultset;
    }

    public function getCountFinishedParticipantsOnTrack(string $track_uid): array
    {
        $statement = $this->connection->prepare($this->sqls('countfinishedontrack'));
        $statement->bindParam(':track_uid', $track_uid);
        $statement->execute();
        $resultset = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $resultset;
    }

    public function createAcpreport(string $track_uid): array
    {
        $acpreport_uid = Uuid::uuid4();
        $ready_for_approval = false;
        $approved = false;
        $approved_by = "";
        $createdat = $this->getCreatedAt();
        $updatedat = $this->getUpdatedAt();
        $statement = $this->connection->prepare($this->sqls('createacpreport'));
        $statement->bindParam(':acpreport_uid', $acpreport_uid);
        $statement->bindParam(':track_uid', $track_uid);
        $statement->bindParam(':organizer_id', $organizer_id);
        $statement->bindParam(':ready_for_approval', $ready_for_approval);
        $statement->bindParam(':marked_as_ready_for_approval_by', $marked_as_ready_for_approval_by);
        $statement->bindParam(':approved', $approved);
        $statement->bindParam(':approved_by', $approved_by);
        $statement->bindParam(':created_at', $createdat);
        $statement->bindParam(':updated_at', $updatedat);

        $statement->execute();
        $resultset = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $resultset;
    }

    public function updateAcpreport(string $track_uid): array
    {
        $ready_for_approval = false;
        $approved = false;

        if ($approved === true) {
            $approved_by = CurrentUser::getUser()->getId();
        } else {
            $approved_by = null;
        }
        $acpreport_uid = "";

        $updatedat = $this->getUpdatedAt();
        $statement = $this->connection->prepare($this->sqls('updateacpreport'));
        $statement->bindParam(':report_uid', $acpreport_uid);
        $statement->bindParam(':track_uid', $track_uid);
        $statement->bindParam(':organizer_id', $organizer_id);
        $statement->bindParam(':ready_for_approval', $ready_for_approval);
        $statement->bindParam(':approved', $approved);
        $statement->bindParam(':approved_by', $approved_by);
        $statement->bindParam(':updated_at', $updatedat);

        $statement->execute();
        $resultset = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $resultset;
    }


    public function approveAcpreport(string $report_uid): array
    {
        $approved = true;
        $approved_by = CurrentUser::getUser()->getId();

        $updatedat = $this->getUpdatedAt();
        $statement = $this->connection->prepare($this->sqls('approvepreport'));
        $statement->bindParam(':report_uid', $report_uid);
        $statement->bindParam(':approved', $approved);
        $statement->bindParam(':approved_by', $approved_by);
        $statement->bindParam(':updated_at', $updatedat);
        $statement->execute();
        $resultset = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $resultset;
    }

    public function markAsReadyForapproval(string $report_uid): array
    {
        $approved = true;
        $ready_for_approval = true;
        $marked_as_ready_for_approval_by = "";
        $updatedat = $this->getUpdatedAt();
        $statement = $this->connection->prepare($this->sqls('markasreadyforapproval'));
        $statement->bindParam(':report_uid', $report_uid);
        $statement->bindParam(':ready_for_approval', $ready_for_approval);
        $statement->bindParam(':marked_as_ready_for_approval_by', $marked_as_ready_for_approval_by);
        $statement->bindParam(':updated_at', $updatedat);
        $statement->execute();
        $resultset = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $resultset;
    }


    public function deleteReport(string $report_uid)
    {
        $statement = $this->connection->prepare($this->sqls('delete from acpreports where report_uid=:report_uid'));
        $statement->bindParam(':report_uid', $report_uid);
        $statement->execute();
    }


    public function sqls($type)
    {
        $acpsql['acpreportfortrack'] = "select t.title, com.family_name as NOM, com.given_name as PRENOM, cl.title as CLUB_DU_PARICIPANT, cl.acp_kod as ACPKOD,  p.time as DURTION,  com.gender as SEXE, t.track_uid, p.participant_uid, p.brevenr  from competitors c inner join participant p on p.competitor_uid = c.competitor_uid inner join competitor_info ci on ci.competitor_uid = c.competitor_uid inner join competitors com on com.competitor_uid = p.competitor_uid inner join club cl on cl.club_uid = p.club_uid left join countries co on co.country_id = ci.country_id inner join track t on t.track_uid = p.track_uid where t.track_uid=:track_uid and t.active = false and p.dns = false and p.dnf = false order by com.family_name, com.given_name;";
        $acpsql['trackstoreport'] = "SELECT * FROM track t , participant p where p.track_uid = t.track_uid and t.active = 1 and p.finished = 1";
        $acpsql['countfinishedontrack'] = "SELECT EXISTS (SELECT 1 FROM track t JOIN participant p ON p.track_uid = t.track_uid WHERE t.active = 0 AND p.finished = 1 and track_uid=:track_uid) AS has_participant;";
        $acpsql['createacpreport'] = "insert into acpreports value(report_uid=:report_uid, track_uid=:track_uid,organizer_id=:organizer_id,ready_for_approval=:ready_for_approval, marked_as_ready_for_approval_by=:marked_as_ready_for_approval_by,approved=:approved,approved_by=:approved_by,created_at=:created_at,updated_at=:updated_at )";
        $acpsql['approvepreport'] = "update acpreports set approved=:approved,approved_by=: approved_by, updated_at=:updated_at where report_uid=:report_uid";
        $acpsql['updateacpreport'] = "update acpreports set approved=:approved,approved_by=: approved_by, updated_at=: updated_at, organizer_id=:organizer_id where report_uid=:report_uid";
        $acpsql['markasreadyforapproval'] = "update acpreports set marked_as_ready_for_approval_by=:marked_as_ready_for_approval_by updated_at=:updated_at, ready_for_approval=:ready_for_approval where report_uid=:report_uid";
        return $acpsql[$type];
    }
}
