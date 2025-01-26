<?php

namespace App\common\Service\Cache;

use App\common\Repository\BaseRepository;
use App\Domain\Model\Cache\SvgCache;
use Exception;
use PDO;
use PDOException;

class CacheRepository extends BaseRepository
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


    public function getAllSvgs()

    {
        try {
            $statement = $this->connection->prepare($this->sqls('allSvgs'));
            $statement->execute();
            $svgs = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, SvgCache::class, null);
            if (empty($svgs)) {
                return null;
            }
            return $svgs;

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }

        return array();
    }


    public function saveSvg(SvgCache $cache): ?SvgCache
    {
        // Prepare the SQL query to insert the SVG
        $sql = "INSERT INTO svg_files (organizer_id, name, svg_blob, created_at, updated_at) 
                VALUES (:organizer_id, :svg_blob, :created_at, :updated_at)";

        $organizer = $cache->getOrganizerId();
        $svg = $cache->getSvgBlob();
        $created_at = $this->getCreatedAt();
        $updated_at = $this->getUpdatedAt();

        try {
            $statement = $this->connection->prepare($this->sqls('insert'));

            $statement->bindParam(':organizer_id', $organizer);
            $statement->bindParam(':svg_blob', $svg);
            $statement->bindParam(':created_at', $created_at);
            $statement->bindParam(':updated_at', $updated_at);
            $status = $statement->execute();

            $row = $statement->fetch(PDO::FETCH_ASSOC);

            if ($status) {
                $cache->setId($row['id']);
                return $cache;
            }

        } catch (PDOException $e) {
            echo 'Kunde inte uppdatera site: ' . $e->getMessage();
        }

        return null;
    }


    public function sqls($type)
    {
        $cachesql['allSvgs'] = 'select * from svg_files;';
        $cachesql['insert'] = 'INSERT INTO svg_files (organizer_id, name, svg_blob, created_at, updated_at)   VALUES (:organizer_id, :name, :svg_blob, :created_at, :updated_at)';
        return $cachesql[$type];
    }


}