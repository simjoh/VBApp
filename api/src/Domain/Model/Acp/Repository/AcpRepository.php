<?php

namespace App\Domain\Model\Acp\Repository;

use App\common\Repository\BaseRepository;
use PDO;

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


    public function sqls($type)
    {

        $acpsql['acpreportfortrack'] = "select t.title, com.family_name as NOM, com.given_name as PRENOM, cl.title as CLUB_DU_PARICIPANT, cl.acp_kod as ACPKOD,  p.time as DURTION,  com.gender as SEXE, t.track_uid, p.participant_uid, p.brevenr  from competitors c inner join participant p on p.competitor_uid = c.competitor_uid inner join competitor_info ci on ci.competitor_uid = c.competitor_uid inner join competitors com on com.competitor_uid = p.competitor_uid inner join club cl on cl.club_uid = p.club_uid left join countries co on co.country_id = ci.country_id inner join track t on t.track_uid = p.track_uid where t.track_uid=:track_uid and t.active = false and p.dns = false and p.dnf = false order by com.family_name, com.given_name;";


        return $acpsql[$type];
    }
}