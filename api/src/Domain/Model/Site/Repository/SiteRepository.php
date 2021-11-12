<?php

namespace App\Domain\Model\Site\Repository;

use App\common\Repository\BaseRepository;
use App\Domain\Model\Site\Site;
use Nette\Utils\Validators;
use PDO;
use PDOException;
use PrestaShop\Decimal\DecimalNumber;
use Ramsey\Uuid\Uuid;


class SiteRepository extends BaseRepository
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
            // fixa lat long
            $site = new Site($row["site_uid"], $row["place"],
                $row["adress"],$row['description'],
                is_null($row["location"]) ? "" : $row["location"],
                is_null($row["lat"])? new DecimalNumber("0") : new DecimalNumber($row["lat"]), is_null($row["lng"])   ? new DecimalNumber("0")  : new DecimalNumber($row["lng"]));
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

    public function siteFor(string $siteUid): ?Site {
        try {
        $statement = $this->connection->prepare($this->sqls('getSiteByUid'));
        $statement->bindParam(':site_uid', $siteUid);
        $statement->execute();
        $data = $statement->fetch();
        print_r($data);
        if(!empty($data)){
            return new Site($data["site_uid"],  $data["place"], $data["adress"],$data['description'],$data["location"],
                empty($data["lat"]) ? new DecimalNumber("0") : new DecimalNumber($data["lat"]),
                empty($data["lng"]) ? new DecimalNumber("0")  : new DecimalNumber($data["lng"]));
        }

         }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }

        return null;
    }

    public function updateSite(Site $site): ?Site{

        $site_uid = $site->getSiteUid();
        $adress = $site->getAdress();
        $place = $site->getPlace();
        $description = $site->getDescription();
        try {
            $statement = $this->connection->prepare($this->sqls('updateSite'));
            $statement->bindParam(':site_uid', $site_uid);
            $statement->bindParam(':adress',$adress );
            $statement->bindParam(':description',$description );
            $statement->bindParam(':place', $place);
            $statement->execute();
        } catch (PDOException $e) {

            echo 'Kunde inte uppdatera site: ' . $e->getMessage();
        }

        return $site;
    }

    public function createSite(Site $siteToCreate): void{
        try {
            $site_uid = Uuid::uuid4();
            $adress = $siteToCreate->getAdress();
            $place = $siteToCreate->getPlace();
            $location = $siteToCreate->getLocation();
            $description = $siteToCreate->getDescription();
            $stmt = $this->connection->prepare($this->sqls('createSite'));
            $stmt->bindParam(':site_uid', $site_uid);
            $stmt->bindParam(':adress',$adress );
            $stmt->bindParam(':place', $place);
            $stmt->bindParam(':description', $description);
            $null = null;
            $stmt->bindParam(':location', $null);
            $stmt->bindParam(':lat', $null);
            $stmt->bindParam(':lng', $null);
            $stmt->execute();
        }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }
    }

    public function deleteSite(string $siterUid): void{
        try {
            $stmt = $this->connection->prepare($this->sqls('deleteSite'));
            $stmt->bindParam(':site_uid', $siterUid);
            $stmt->execute();
        }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }
    }

    public function sqls($type): string
    {
        $sitesqls['allSites'] = 'select * from site s;';
        $sitesqls['getSiteByUid'] = 'select * from site s where s.site_uid = :site_uid;';
        $sitesqls['updateSite']  = "UPDATE site SET  place=:place, adress=:adress , description=:description  WHERE site_uid=:site_uid";
        $sitesqls['deleteSite'] = 'delete from site  where site_uid = :site_uid';
        $sitesqls['createSite']  = "INSERT INTO site(site_uid, place, adress, description, location, lat, lng) VALUES (:site_uid, :place, :adress, :description ,:location, :lat, :lng)";
        return $sitesqls[$type];

    }
}