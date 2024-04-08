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
                is_null($row["lat"])? new DecimalNumber("0") : new DecimalNumber(strval($row["lat"])), is_null($row["lng"])   ? new DecimalNumber("0")  : new DecimalNumber($row["lng"]), is_null($row["picture"]) ? "": $row["picture"] );
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
        if(!empty($data)){
            return new Site($data["site_uid"],  $data["place"], $data["adress"],$data['description'],$data["location"],
                empty($data["lat"]) ? new DecimalNumber("0") : new DecimalNumber(strval($data["lat"])),
                empty($data["lng"]) ? new DecimalNumber("0")  : new DecimalNumber(strval($data["lng"])), is_null($data["picture"]) ? "": $data["picture"]);
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
        $logo = $site->getPicture();
       // print_r(round($site->getLat(),2));
        $lat = $site->getLat();
        $lng = $site->getLng();
        $description = $site->getDescription();
        try {
            $statement = $this->connection->prepare($this->sqls('updateSite'));
            $statement->bindParam(':site_uid', $site_uid);
            $statement->bindParam(':adress',$adress );
            $statement->bindParam(':description',$description );
            $statement->bindParam(':place', $place);
            $statement->bindParam(':picture', $logo);
            $statement->bindParam('lat', $lat);
            $statement->bindParam('lng', $lng);
            $statement->execute();
        } catch (PDOException $e) {

            echo 'Kunde inte uppdatera site: ' . $e->getMessage();
        }

        return $site;
    }

    public function existsByPlaceAndAdress(string $place, string $adress): ?Site
    {
        try {

        $statement = $this->connection->prepare($this->sqls('existsByPlaceAndAdress'));
        $statement->bindParam(':place', $place);
        $statement->bindParam(':adress', $adress);
            $statement->execute();
        $data = $statement->fetch();

        if(!empty($data)){

            return new Site($data["site_uid"],  $data["place"], $data["adress"],$data['description'],$data["location"],
                empty($data["lat"]) ? new DecimalNumber("0") : new DecimalNumber(strval($data["lat"])),
                empty($data["lng"]) ? new DecimalNumber("0")  : new DecimalNumber(strval($data["lng"])), is_null($data["picture"]) ? "": $data["picture"]);
        }
        } catch (PDOException $e) {
            echo 'Kunde inte läsa upp site: ' . $e->getMessage();
        }
        return null;
    }


    public function existsByPlaceAndAdress2(string $place, string $adress): ?Site
    {
        try {

            $statement = $this->connection->prepare($this->sqls('existsByPlaceAndAdress2'));
            $statement->bindParam(':place', $place);
            $statement->bindParam(':adress', $adress);
            $statement->execute();
            $data = $statement->fetch();
            if($statement->rowCount() > 0){
                return new Site($data["site_uid"],  $data["place"], $data["adress"],$data['description'],$data["location"],
                    empty($data["lat"]) ? new DecimalNumber("0") : new DecimalNumber(strval($data["lat"])),
                    empty($data["lng"]) ? new DecimalNumber("0")  : new DecimalNumber(strval($data["lng"])), is_null($data["picture"]) ? "": $data["picture"]);
            }
        } catch (PDOException $e) {
            echo 'Kunde inte läsa upp site: ' . $e->getMessage();
        }
        return null;
    }

    public function createSite(Site $siteToCreate): ?Site{
        try {


              $site_uid = Uuid::uuid4();


            $adress = $siteToCreate->getAdress();
            $place = $siteToCreate->getPlace();
           // $location = $siteToCreate->getLocation();
            $description = $siteToCreate->getDescription();
            $image = $siteToCreate->getPicture();
            $lat = sprintf('%.7f', $siteToCreate->getLat());
            $lng = sprintf('%.7f', $siteToCreate->getLng());
            $stmt = $this->connection->prepare($this->sqls('createSite'));
            $stmt->bindParam(':site_uid', $site_uid);
            $stmt->bindParam(':adress',$adress );
            $stmt->bindParam(':place', $place);
            $stmt->bindParam(':description', $description);
            $null = null;
            $stmt->bindParam(':location', $null);
            $stmt->bindParam(':lat',$lat );
            $stmt->bindParam(':lng', $lng);
            $stmt->bindParam(':picture', $image);
            $stmt->execute();
        }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }

        $siteToCreate->setSiteUid($site_uid);

     return $siteToCreate;
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

    public function siteInUse($siteUid)
    {
        try {
            $stmt = $this->connection->prepare($this->sqls('siteInUse'));
            $stmt->bindParam(':site_uid', $siteUid);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                return true;
            } else {
               return false;
            }
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
        $sitesqls['updateSite']  = "UPDATE site SET  place=:place, adress=:adress , description=:description , picture=:picture ,lat=:lat , lng=:lng WHERE site_uid=:site_uid";
        $sitesqls['deleteSite'] = 'delete from site  where site_uid = :site_uid';
        $sitesqls['createSite']  = "INSERT INTO site(site_uid, place, adress, description, location, lat, lng, picture) VALUES (:site_uid, :place, :adress, :description ,:location, :lat, :lng, :picture)";
        $sitesqls['existsByPlaceAndAdress'] = 'select *  from site e where e.place=:place and e.adress=:adress;';
        $sitesqls['existsByPlaceAndAdress2'] = 'select *  from site e where REPLACE(TRIM(lower(e.place))," ","")=:place and REPLACE(TRIM(lower(e.adress))," ","")=:adress;';
        $sitesqls['siteInUse'] = 'select 1 from checkpoint WHERE site_uid=:site_uid limit 1;';
        return $sitesqls[$type];

    }




}