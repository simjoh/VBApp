<?php

namespace App\Domain\Model\Country\Repository;

use App\common\Repository\BaseRepository;
use App\Domain\Model\Country\Country;
use App\Domain\Model\Event\Event;
use Exception;
use PDO;
use PDOException;

class CountryRepository extends BaseRepository
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

    public function allCountries(): array
    {
        try {
            $statement = $this->connection->prepare($this->sqls('allCountries'));
            $statement->execute();
            $countries = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Country\Country::class, null);

            if (empty($countries)) {
                return array();
            }

            return $countries;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return array();
    }

    public function countryFor(int $countryId): ?Country
    {
        try {
            $statement = $this->connection->prepare('select *  from countries e where e.country_id=:country_id;');
            $statement->bindParam(':country_id', $countryId);
            $statement->execute();
            $country = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Country\Country::class, null);

            if($statement->rowCount() > 1){
                // Fixa bätter felhantering
                throw new Exception();
            }
            if(!empty($country)){
                return $country[0];
            }
        }
        catch(PDOException $e)
        {
            echo "Error: " . $e->getMessage();
        }

        return null;
    }

    public function sqls($type)
    {
        $eventqls['allCountries'] = 'select * from countries e;';
        $eventqls['getCountryByID'] = 'select *  from countries e where e.country_id=:country_id;';
        return $eventqls[$type] ?? '';
    }
}