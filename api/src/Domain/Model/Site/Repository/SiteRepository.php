<?php

namespace App\Domain\Model\Site\Repository;

use App\common\Repository\BaseRepository;
use App\Domain\Model\Site\Site;
use PDO;
use PDOException;


class SiteRepository extends BaseRepository
{

    /**
     * Constructor.
     *
     * @param PDO $connection The database connection
     */
    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function allSites(): ?array {
        try {
        $statement = $this->connection->prepare($this->sqls('allSites'));
        $statement->execute();
        $data = $statement->fetchAll();

        if (empty($data)) {
            return array();
        }
        $sites = [];
        foreach ($data as $x =>  $row) {
            $site = new Site($row["site_uid"], $row["place"], $row["adress"],$row["location"]);
            array_push($sites,  $site);
        }
        return $sites;

        }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }

        return null;
    }

    public function siteFor($siteUid): ?Site {
        try {
        $statement = $this->connection->prepare($this->sqls('getSiteByUid'));
        $statement->bindParam(':site_uid', $siteUid);
        $statement->execute();
        $data = $statement->fetch();
             return new Site($data["site_uid"],  $data["place"], $data["adress"],$data["location"]);
         }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }

        return null;
    }

    public function updateSite($siteUid): void{

    }

    public function createSite(): void{

    }

    public function deleteSite(): void{

    }




    public function sqls($type): string
    {
        $sitesqls['allSites'] = 'select * from site s;';
        $sitesqls['getSiteByUid'] = 'select * from site s where s.site_uid = :site_uid;';
        return $sitesqls[$type];

    }
}