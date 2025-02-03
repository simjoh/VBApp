<?php

namespace App\common\Repository;

use App\common\CurrentOrganizer;
use App\common\CurrentUser;
use App\common\Database;
use DateTimeImmutable;
use DateTimeZone;
use PDO;
use PDOException;
use RuntimeException;

abstract class BaseRepository extends Database
{

    /**
     * @var PDO The database connection
     */
    public PDO $connection;

    function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    abstract public function sqls($type);

    public function gets(): PDO
    {
        return $this::getConnection();
    }

    public function getOrganizer(): int
    {
        return CurrentOrganizer::getUser()->getOrganizerId();
    }

    public function getCreatedAt(): string
    {
        return (new DateTimeImmutable('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s');
//        return date('Y-m-d H:i:s');
    }

    public function getUpdatedAt(): string
    {
//        return date('Y-m-d H:i:s');
        return (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s');

    }

    public function changedBy(): string
    {
        return CurrentUser::getUser()->getId();
    }


    protected function executeQuery(string $query, array $params = []): array
    {
        try {
            $statement = $this->connection->prepare($query);
            foreach ($params as $key => $value) {
                $statement->bindValue(":$key", $value);
            }
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            throw new RuntimeException("Database operation failed.");
        }
    }

    /**
     * Retrieves paginated results based on the provided SQL query.
     *
     * @param string $baseQuery The base SQL query to be used for fetching data.
     * @param int $page The current page number.
     * @param int $recordsPerPage The number of records to retrieve per page.
     * @param array $params Any additional query parameters.
     *
     * @return array The paginated results.
     */
    public function getPaginatedResults(string $baseQuery, int $page, int $recordsPerPage, array $params = []): array
    {
        // Calculate the OFFSET for pagination
        $offset = ($page - 1) * $recordsPerPage;

        // Modify the base query to include LIMIT and OFFSET for pagination
        $paginatedQuery = $baseQuery . " LIMIT :limit OFFSET :offset";

        // Merge pagination parameters into the existing parameters
        $params['limit'] = $recordsPerPage;
        $params['offset'] = $offset;

        // Execute the query and return the results
        return $this->executeQuery($paginatedQuery, $params);
    }


//    public function getPaginatedUsers(int $page, int $recordsPerPage): array
//    {
//        // Get the base SQL query for fetching all users
//        $baseQuery = $this->sqls('getAllUsers');
//
//        // Add the organizer ID to the parameters
//        $params = ['organizer_id' => $this->getOrganizer()];
//
//        // Call the getPaginatedResults method to fetch paginated results
//        return $this->getPaginatedResults($baseQuery, $page, $recordsPerPage, $params);
//    }


    /**
     * Adds predicates to a SQL query dynamically.
     *
     * @param string $query The SQL query to modify.
     * @param array $predicates Associative array of predicates where the key is the column and the value is the value to filter by.
     * @param array $params The query parameters to bind.
     *
     * @return array The modified query and parameters.
     */
    public function addPredicates(string $query, array $predicates = [], array $params = []): array
    {
        // Check if the query already has a WHERE clause
        $firstPredicate = true;

        foreach ($predicates as $column => $value) {
            // Check if we should start the WHERE clause or add an AND
            if ($firstPredicate) {
                $query .= " WHERE $column = :$column";
                $firstPredicate = false;
            } else {
                $query .= " AND $column = :$column";
            }

            // Add the value to the parameters
            $params[$column] = $value;
        }

        return [$query, $params];
    }

//    // Create an instance of UserRepository
//$userRepository = new UserRepository($pdo);
//
//// Define dynamic predicates, e.g., search by name or status
//$predicates = [
//'name' => 'John Doe',
//'status' => 'active'
//];
//
//// Optionally add more parameters for filtering
//$additionalParams = ['email' => 'john.doe@example.com'];
//
//// Fetch users with the predicates (e.g., name, status, and organizer_id)
//$users = $userRepository->getUsersWithPredicates($predicates, $additionalParams);
//
//// Display the users
//foreach ($users as $user) {
//echo $user['name'] . "<br>";
//}

    /**
     * Ensures that the result data belongs to the current user's organizer.
     *
     * @param array $results The results from the database query (can be multiple rows).
     * @param bool $checkOrganizer Whether to enforce the organizer check. Default is true.
     * @throws \RuntimeException if the organizer_id does not match for any row.
     */
    private function checkOrganizerInResult(array $results, bool $checkOrganizer = true): void
    {
        if ($checkOrganizer) {
            // Loop through each row and ensure the organizer_id matches
            foreach ($results as $result) {
                if (isset($result['organizer_id']) && $result['organizer_id'] !== CurrentUser::getUser()->getOrganizerId()) {
                    throw new RuntimeException('You do not have permission to access one or more of the data rows.');
                }
            }
        }
    }

}