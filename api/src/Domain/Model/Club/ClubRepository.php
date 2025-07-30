<?php

namespace App\Domain\Model\Club;

use App\common\Repository\BaseRepository;
use PDO;
use PDOException;
use Ramsey\Uuid\Uuid;
use App\common\Exceptions\BrevetException;
use App\common\Repository\PaginationResult;

class ClubRepository extends BaseRepository
{





    /**
     * Constructor.
     *
     * @param PDO $connection The database connection
     */
    public function __construct(PDO $connection)
    {
         parent::__construct($connection);
        $this->connection = $connection;
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getAllClubs(): ?array
    {
        try {
        $statement = $this->connection->prepare($this->sqls('allClubs'));
        $statement->execute();
        $clubs = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Club::class, null);
        if (empty($clubs)) {
            return null;
        }
        return $clubs;

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return array();
    }


    public function getClubByUId(string $club_uid): ?Club
    {
        try {
            $statement = $this->connection->prepare($this->sqls('clubByUID'));
            $statement->bindParam(':club_uid', $club_uid);
            $statement->execute();
            $events = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Club\Club::class, null);

            if (empty($events)) {
                return null;
            }

            return $events[0];
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return null;

    }

    public function getClubByTitle(?string $title): ?Club
    {
        try {
            if ($title === null) {
                return null;
            }
            
            $statement = $this->connection->prepare($this->sqls('clubByTitle'));
            $statement->bindParam(':title', $title);
            $statement->execute();

            $clubs = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Club\Club::class, null);

            if (empty($clubs)) {
                return null;
            }

            return $clubs[0];
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return null;

    }


    public function getClubByTitleLower(?string $title): ?Club
    {
        try {
            if ($title === null) {
                return null;
            }

            $trimmmedandlover = strtolower(trim($title));
            $statement = $this->connection->prepare($this->sqls('clubByTitleLower'));
            $statement->bindParam(':title', $trimmmedandlover);
            $statement->execute();

            $clubs = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Club\Club::class, null);

            if (empty($clubs)) {
                return null;
            }

            return $clubs[0];
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return null;

    }


    public function getClubByAcpCode(string $acpcode)
    {
        try {
            $statement = $this->connection->prepare($this->sqls('clubByAcpCode'));
            $statement->bindParam(':acpcode', $acpcode);
            $statement->execute();
            $events = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Club\Club::class, null);

            if (empty($events)) {
                return array();
            }

            return $events;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return array();
    }

    /**
     * Search clubs by title using wildcard patterns
     * Supports patterns like: "john*", "*doe", "*smith*", "exact"
     * 
     * @param string $title The search term with optional wildcards
     * @return array Array of Club objects
     */
    public function searchClubsByTitle(string $title): array
    {
        return $this->executeWildcardSearch(
            $this->sqls('searchClubsByTitle'),
            $title,
            \App\Domain\Model\Club\Club::class
        );
    }

    /**
     * Search clubs by title using wildcard patterns and return single result
     * 
     * @param string $title The search term with optional wildcards
     * @return Club|null Single Club object or null
     */
    public function searchClubByTitleSingle(string $title): ?Club
    {
        return $this->executeWildcardSearchSingle(
            $this->sqls('searchClubsByTitle'),
            $title,
            \App\Domain\Model\Club\Club::class
        );
    }

    /**
     * Search clubs by ACP code using wildcard patterns
     * 
     * @param string $acpCode The ACP code with optional wildcards
     * @return array Array of Club objects
     */
    public function searchClubsByAcpCode(string $acpCode): array
    {
        return $this->executeWildcardSearch(
            $this->sqls('searchClubsByAcpCode'),
            $acpCode,
            \App\Domain\Model\Club\Club::class
        );
    }

    /**
     * Advanced search with multiple criteria and wildcards
     * 
     * @param array $criteria Search criteria ['title' => 'john*', 'acp_code' => '123*']
     * @return array Array of Club objects
     */
    public function advancedSearch(array $criteria): array
    {
        try {
            $conditions = [];
            $params = [];
            
            foreach ($criteria as $field => $value) {
                if (!empty($value)) {
                    $condition = $this->buildSearchCondition($field, $value, 'c');
                    $conditions[] = $condition['sql'];
                    $params = array_merge($params, $condition['params']);
                }
            }
            
            if (empty($conditions)) {
                $allClubs = $this->getAllClubs();
                return $allClubs ?? [];
            }
            
            $whereClause = implode(' AND ', $conditions);
            $sql = "SELECT * FROM club c WHERE " . $whereClause;
            
            $statement = $this->connection->prepare($sql);
            
            // Bind parameters
            foreach ($params as $key => $value) {
                $statement->bindParam($key, $value);
            }
            
            $statement->execute();
            
            $results = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Club\Club::class, null);
            
            return empty($results) ? [] : $results;
            
        } catch (PDOException $e) {
            error_log("Error in advanced search: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all clubs with pagination
     * 
     * @param int $page Page number (1-based)
     * @param int $perPage Items per page (max 100)
     * @param string $orderBy Optional ORDER BY clause
     * @return PaginationResult
     */
    public function getAllClubsPaginated(int $page = 1, int $perPage = 20, string $orderBy = 'ORDER BY title ASC'): PaginationResult
    {
        return $this->executePaginatedQuery(
            $this->sqls('allClubsPaginated'),
            $this->sqls('countAllClubs'),
            \App\Domain\Model\Club\Club::class,
            $page,
            $perPage,
            [],
            $orderBy
        );
    }

    /**
     * Search clubs by title with pagination
     * 
     * @param string $title The search term with optional wildcards
     * @param int $page Page number (1-based)
     * @param int $perPage Items per page (max 100)
     * @param string $orderBy Optional ORDER BY clause
     * @return PaginationResult
     */
    public function searchClubsByTitlePaginated(string $title, int $page = 1, int $perPage = 20, string $orderBy = 'ORDER BY title ASC'): PaginationResult
    {
        return $this->executeWildcardSearchPaginated(
            $this->sqls('searchClubsByTitle'),
            $title,
            \App\Domain\Model\Club\Club::class,
            $page,
            $perPage,
            [],
            $orderBy
        );
    }

    /**
     * Get clubs with cursor-based pagination (useful for large datasets)
     * 
     * @param ?string $cursor The cursor value (null for first page)
     * @param int $perPage Items per page (max 100)
     * @param string $direction 'next' or 'previous'
     * @return PaginationResult
     */
    public function getClubsCursorPaginated(?string $cursor = null, int $perPage = 20, string $direction = 'next'): PaginationResult
    {
        return $this->executeCursorPaginatedQuery(
            $this->sqls('allClubsCursorPaginated'),
            $this->sqls('countAllClubs'),
            \App\Domain\Model\Club\Club::class,
            'club_uid', // Use club_uid as cursor field
            $cursor,
            $perPage,
            [],
            $direction
        );
    }

    /**
     * Advanced search with pagination
     * 
     * @param array $criteria Search criteria
     * @param int $page Page number (1-based)
     * @param int $perPage Items per page (max 100)
     * @param string $orderBy Optional ORDER BY clause
     * @return PaginationResult
     */
    public function advancedSearchPaginated(array $criteria, int $page = 1, int $perPage = 20, string $orderBy = 'ORDER BY title ASC'): PaginationResult
    {
        try {
            $conditions = [];
            $params = [];
            
            foreach ($criteria as $field => $value) {
                if (!empty($value)) {
                    $condition = $this->buildSearchCondition($field, $value, 'c');
                    $conditions[] = $condition['sql'];
                    $params = array_merge($params, $condition['params']);
                }
            }
            
            if (empty($conditions)) {
                return $this->getAllClubsPaginated($page, $perPage, $orderBy);
            }
            
            $whereClause = implode(' AND ', $conditions);
            $sql = "SELECT * FROM club c WHERE " . $whereClause;
            $countSql = "SELECT COUNT(*) FROM club c WHERE " . $whereClause;
            
            return $this->executePaginatedQuery(
                $sql,
                $countSql,
                \App\Domain\Model\Club\Club::class,
                $page,
                $perPage,
                $params,
                $orderBy
            );
            
        } catch (PDOException $e) {
            error_log("Error in advanced search paginated: " . $e->getMessage());
            return new PaginationResult(
                data: [],
                total: 0,
                page: $page,
                perPage: $perPage,
                totalPages: 0,
                hasNextPage: false,
                hasPreviousPage: false
            );
        }
    }

    public function createClub(Club $club): void
    {
        try {
            error_log("Creating club in database with ACP kod: " . $club->getAcpKod(), 0);
            $statement = $this->connection->prepare($this->sqls('createClub'));
            $club_uid = $club->getClubUid();
            $title = $club->getTitle();
            $acp_kod = $club->getAcpKod();
            
            error_log("Bound parameters - club_uid: $club_uid, title: $title, acp_kod: $acp_kod", 0);
            
            $statement->bindParam(':club_uid', $club_uid);
            $statement->bindParam(':title', $title);
            $statement->bindParam(':acp_kod', $acp_kod);
            
            $statement->execute();
            error_log("Club created successfully in database", 0);
        } catch (PDOException $e) {
            error_log("Error creating club in database: " . $e->getMessage(), 0);
            throw new BrevetException("Det gick inte att skapa klubben: " . $e->getMessage(), 5);
        }
    }


    public function updateClub(Club $club): void
    {
        try {
            error_log("Updating club in database with ACP kod: " . $club->getAcpKod(), 0);
            $statement = $this->connection->prepare($this->sqls('updateClub'));
            $club_uid = $club->getClubUid();
            $title = $club->getTitle();
            $acp_kod = $club->getAcpKod();
            
            error_log("Bound parameters - club_uid: $club_uid, title: $title, acp_kod: $acp_kod", 0);
            
            $statement->bindParam(':club_uid', $club_uid);
            $statement->bindParam(':title', $title);
            $statement->bindParam(':acp_kod', $acp_kod);
            
            $statement->execute();
            error_log("Club updated successfully in database", 0);
        } catch (PDOException $e) {
            error_log("Error updating club in database: " . $e->getMessage(), 0);
            throw new BrevetException("Det gick inte att uppdatera klubben: " . $e->getMessage(), 5);
        }
    }

    public function deleteClub(string $club_uid): bool
    {
        try {
            $stmt = $this->connection->prepare($this->sqls('deleteClub'));
            $stmt->bindParam(':club_uid', $club_uid);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Get the database connection for transaction handling
     * 
     * @return PDO
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }

    public function sqls($type): string
    {
        $clubsql['clubByUID'] = 'select * from club e where club_uid=:club_uid;';
        $clubsql['clubByTitle'] = 'select * from club e where title=:title;';
        $clubsql['clubByTitleLower'] = 'select * from club e where REPLACE(TRIM(lower(title))," ","")=:title;';
        $clubsql['allClubs'] = 'select * from club;';
        $clubsql['clubByAcpCode'] = 'select * from club e where acp_kod=:acpcode;';
        $clubsql['searchClubsByTitle'] = 'select * from club e where title LIKE :search;';
        $clubsql['searchClubsByAcpCode'] = 'select * from club e where acp_kod LIKE :search;';
        $clubsql['createClub'] = 'INSERT INTO club(club_uid, acp_kod, title) VALUES (:club_uid, :acp_kod, :title)';
        $clubsql['updateClub'] = 'UPDATE club set acp_kod=:acp_kod, title=:title where club_uid=:club_uid';
        $clubsql['deleteClub'] = 'DELETE FROM club WHERE club_uid = :club_uid';
        $clubsql['allClubsPaginated'] = 'SELECT * FROM club';
        $clubsql['countAllClubs'] = 'SELECT COUNT(*) FROM club';
        $clubsql['allClubsCursorPaginated'] = 'SELECT * FROM club';
        return $clubsql[$type];
    }
}