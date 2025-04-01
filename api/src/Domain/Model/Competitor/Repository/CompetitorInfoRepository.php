<?php

namespace App\Domain\Model\Competitor\Repository;

use App\common\Repository\BaseRepository;
use App\Domain\Model\Competitor\CompetitorInfo;
use Exception;
use PDO;
use PDOException;
use Ramsey\Uuid\Uuid;

class CompetitorInfoRepository extends BaseRepository
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

    public function getCompetitorInfo(string $uid): ?CompetitorInfo{

        return null;
    }

    public function getCompetitorInfoByCompetitorUid(string $competitor_uid){
        try {
            $statement = $this->connection->prepare($this->sqls('infobycompetitoruid'));
            $statement->bindParam(':competitor_uid', $competitor_uid);
            $statement->execute();
            $event = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Competitor\CompetitorInfo::class, null);

            if($statement->rowCount() > 1){
                // Fixa bätter felhantering
                return $event[0];
             //   throw new Exception();
            }
            if(!empty($event)){
                return $event[0];
            }
        }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }
        return null;
    }

    public function creatCompetitorInfoForCompetitor(CompetitorInfo $competitorInfo): ?CompetitorInfo {
        $uid = Uuid::uuid4();
        $competitor_uid = $competitorInfo->getCompetitorUid();

        $email = $competitorInfo->getEmail();
        $phone = $competitorInfo->getPhone();
        $adress = $competitorInfo->getAdress();
        $postal_code = $competitorInfo->getPostalCode();
        $place = $competitorInfo->getPlace();
        $country = $competitorInfo->getCountry();
        try {
            $statement = $this->connection->prepare($this->sqls('createCompetitorInfo2'));

            $statement->bindParam(':uid', $uid);
            $statement->bindParam(':competitor_uid', $competitor_uid);
            $statement->bindParam(':email', $email);
            $statement->bindParam(':phone',$phone);
            $statement->bindParam(':adress', $adress);
            $statement->bindParam(':postal_code', $postal_code);
            $statement->bindParam(':place', $place);
            $statement->bindParam(':country', $country);
            $status = $statement->execute();

            if($status){
                return $competitorInfo;
            }
        } catch (PDOException $e) {
            echo 'Kunde inte uppdatera competitor_info: ' . $e->getMessage();
        }
        return $competitorInfo;
    }


    public function creatCompetitorInfoForCompetitorParams(string $email, string $phone, string $adress, string $postal_code, string $place, string $country, string $competitor_uid)  {
        $uid = Uuid::uuid4();
        try {
            $statement = $this->connection->prepare($this->sqls('createCompetitorInfo2'));

            $statement->bindParam(':uid', $uid);
            $statement->bindParam(':competitor_uid', $competitor_uid);
            $statement->bindParam(':email', $email);
            $statement->bindParam(':phone',$phone);
            $statement->bindParam(':adress', $adress);
            $statement->bindParam(':postal_code', $postal_code);
            $statement->bindParam(':place', $place);
            $statement->bindParam(':country', $country);
            $status = $statement->execute();

            $competitorInfo = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,  \App\Domain\Model\Competitor\CompetitorInfo::class, null);

            if($status){
                if($statement->rowCount() > 0){
                    return $competitorInfo;
                }

            }
        } catch (PDOException $e) {
            echo 'Kunde inte uppdatera competitor_info: ' . $e->getMessage();
        }
        return null;
    }

    public function creatCompetitorInfoForCompetitorParamsFromLoppservice(string $email, string $phone, string $adress, string $postal_code, string $place, string $country, string $competitor_uid, string $country_id)  {
        $uid = Uuid::uuid4();

        try {
            $statement = $this->connection->prepare($this->sqls('createCompetitorInfo2'));
            $statement->bindParam(':uid', $uid);
            $statement->bindParam(':competitor_uid', $competitor_uid);
            $statement->bindParam(':email', $email);
            $statement->bindParam(':phone',$phone);
            $statement->bindParam(':adress', $adress);
            $statement->bindParam(':postal_code', $postal_code);
            $statement->bindParam(':place', $place);
            $statement->bindParam(':country', $country);
            $statement->bindParam(':country_id', $country_id);

            $status = $statement->execute();

            $competitorInfo = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,  \App\Domain\Model\Competitor\CompetitorInfo::class, null);

            if($status){
                if($statement->rowCount() > 0){
                    return $competitorInfo;
                }

            }
        } catch (PDOException $e) {
            echo 'Kunde inte uppdatera competitor_info: ' . $e->getMessage();
        }


        return null;
    }




    public function sqls($type)
    {
        $competitorsqls['infobycompetitoruid'] = 'select * from competitor_info where competitor_uid = :competitor_uid;';
        $competitorsqls['createCompetitorInfo']  = "INSERT INTO competitor_info(uid, competitor_uid, email, phone, adress, postal_code, place,country, country_id) VALUES (:uid, :competitor_uid,:email,:phone,:adress, :postal_code, :place, :country, :country_id)";
        $competitorsqls['createCompetitorInfo2']  = "INSERT INTO competitor_info(uid, competitor_uid, email, phone, adress, postal_code, place,country, country_id) VALUES (:uid, :competitor_uid,:email,:phone,:adress, :postal_code, :place, :country, :country_id)";
        return $competitorsqls[$type];
    }
}