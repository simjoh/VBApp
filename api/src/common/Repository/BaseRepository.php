<?php

namespace App\common\Repository;

use App\common\Database;
use PDO;

abstract class BaseRepository extends Database
{

    /**
     * @var PDO The database connection
     */
    public PDO $connection;

     function __construct(PDO $connection) {
        $this->connection = $connection;
    }

     abstract public function sqls($type);

     public function gets() :PDO{
         return $this->connection;
    }

    /**
     * Convert wildcard pattern to SQL LIKE pattern
     * Supports * as wildcard character
     * 
     * @param string $pattern The wildcard pattern (e.g., "john*", "*doe", "*smith*")
     * @return string SQL LIKE pattern
     */
    protected function wildcardToLikePattern(string $pattern): string
    {
        // Escape SQL LIKE special characters except *
        $escaped = preg_replace('/[%_]/', '\\\\$0', $pattern);
        
        // Convert * to SQL LIKE wildcard %
        $likePattern = str_replace('*', '%', $escaped);
        
        return $likePattern;
    }

    /**
     * Execute a wildcard search query
     * 
     * @param string $sql The SQL query with :search placeholder
     * @param string $searchTerm The search term with wildcards
     * @param string $className The class name to instantiate results
     * @param array $additionalParams Additional parameters to bind
     * @return array Array of objects or empty array
     */
    protected function executeWildcardSearch(string $sql, string $searchTerm, string $className, array $additionalParams = []): array
    {
        try {
            $likePattern = $this->wildcardToLikePattern($searchTerm);
            
            $statement = $this->connection->prepare($sql);
            $statement->bindParam(':search', $likePattern);
            
            // Bind additional parameters
            foreach ($additionalParams as $key => $value) {
                $statement->bindParam($key, $value);
            }
            
            $statement->execute();
            
            $results = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $className, []);
            
            return empty($results) ? [] : $results;
            
        } catch (\PDOException $e) {
            error_log("Error in wildcard search: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Execute a wildcard search query and return single result
     * 
     * @param string $sql The SQL query with :search placeholder
     * @param string $searchTerm The search term with wildcards
     * @param string $className The class name to instantiate results
     * @param array $additionalParams Additional parameters to bind
     * @return object|null Single object or null
     */
    protected function executeWildcardSearchSingle(string $sql, string $searchTerm, string $className, array $additionalParams = []): ?object
    {
        $results = $this->executeWildcardSearch($sql, $searchTerm, $className, $additionalParams);
        
        return empty($results) ? null : $results[0];
    }

    /**
     * Check if a string contains wildcard characters
     * 
     * @param string $searchTerm The search term to check
     * @return bool True if contains wildcards
     */
    protected function hasWildcards(string $searchTerm): bool
    {
        return strpos($searchTerm, '*') !== false;
    }

    /**
     * Build a dynamic search query based on whether wildcards are present
     * 
     * @param string $column The column to search in
     * @param string $searchTerm The search term
     * @param string $tableAlias Optional table alias
     * @return array ['sql' => string, 'params' => array]
     */
    protected function buildSearchCondition(string $column, string $searchTerm, string $tableAlias = ''): array
    {
        $prefix = $tableAlias ? $tableAlias . '.' : '';
        
        if ($this->hasWildcards($searchTerm)) {
            // Use LIKE for wildcard searches
            $likePattern = $this->wildcardToLikePattern($searchTerm);
            return [
                'sql' => $prefix . $column . ' LIKE :search',
                'params' => [':search' => $likePattern]
            ];
        } else {
            // Use exact match for non-wildcard searches
            return [
                'sql' => $prefix . $column . ' = :search',
                'params' => [':search' => $searchTerm]
            ];
        }
    }



    /**
     * Execute a paginated query with offset/limit pagination
     * 
     * @param string $sql The base SQL query (without LIMIT/OFFSET)
     * @param string $countSql The count SQL query for total records
     * @param string $className The class name to instantiate results
     * @param int $page The page number (1-based)
     * @param int $perPage Number of items per page
     * @param array $params Parameters to bind to the query
     * @param string $orderBy Optional ORDER BY clause
     * @return PaginationResult
     */
    protected function executePaginatedQuery(
        string $sql,
        string $countSql,
        string $className,
        int $page = 1,
        int $perPage = 20,
        array $params = [],
        string $orderBy = ''
    ): PaginationResult {
        try {
            // Validate pagination parameters
            $page = max(1, $page);
            $perPage = max(1, min(100, $perPage)); // Limit to 100 items per page
            
            $offset = ($page - 1) * $perPage;
            
            // Get total count
            $countStatement = $this->connection->prepare($countSql);
            foreach ($params as $key => $value) {
                $countStatement->bindParam($key, $value);
            }
            $countStatement->execute();
            $total = (int) $countStatement->fetchColumn();
            
            // Calculate pagination info
            $totalPages = (int) ceil($total / $perPage);
            $hasNextPage = $page < $totalPages;
            $hasPreviousPage = $page > 1;
            
            // Build the final query with pagination
            $finalSql = $sql;
            if ($orderBy) {
                $finalSql .= ' ' . $orderBy;
            }
            $finalSql .= " LIMIT :limit OFFSET :offset";
            
            // Execute the paginated query
            $statement = $this->connection->prepare($finalSql);
            
            // Bind parameters
            foreach ($params as $key => $value) {
                $statement->bindParam($key, $value);
            }
            $statement->bindParam(':limit', $perPage, PDO::PARAM_INT);
            $statement->bindParam(':offset', $offset, PDO::PARAM_INT);
            
            $statement->execute();
            
            $data = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $className, []);
            
            return new PaginationResult(
                data: $data,
                total: $total,
                page: $page,
                perPage: $perPage,
                totalPages: $totalPages,
                hasNextPage: $hasNextPage,
                hasPreviousPage: $hasPreviousPage
            );
            
        } catch (\PDOException $e) {
            error_log("Error in paginated query: " . $e->getMessage());
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

    /**
     * Execute a cursor-based paginated query
     * 
     * @param string $sql The base SQL query
     * @param string $countSql The count SQL query for total records
     * @param string $className The class name to instantiate results
     * @param string $cursorField The field to use for cursor pagination
     * @param ?string $cursor The cursor value (null for first page)
     * @param int $perPage Number of items per page
     * @param array $params Parameters to bind to the query
     * @param string $direction 'next' or 'previous'
     * @return PaginationResult
     */
    protected function executeCursorPaginatedQuery(
        string $sql,
        string $countSql,
        string $className,
        string $cursorField,
        ?string $cursor = null,
        int $perPage = 20,
        array $params = [],
        string $direction = 'next'
    ): PaginationResult {
        try {
            // Validate pagination parameters
            $perPage = max(1, min(100, $perPage));
            
            // Get total count
            $countStatement = $this->connection->prepare($countSql);
            foreach ($params as $key => $value) {
                $countStatement->bindParam($key, $value);
            }
            $countStatement->execute();
            $total = (int) $countStatement->fetchColumn();
            
            // Build cursor condition
            $cursorCondition = '';
            if ($cursor !== null) {
                $operator = $direction === 'next' ? '>' : '<';
                $cursorCondition = " AND {$cursorField} {$operator} :cursor";
            }
            
            // Build the final query
            $finalSql = $sql . $cursorCondition . " ORDER BY {$cursorField} " . 
                       ($direction === 'next' ? 'ASC' : 'DESC') . 
                       " LIMIT :limit";
            
            // Execute the cursor paginated query
            $statement = $this->connection->prepare($finalSql);
            
            // Bind parameters
            foreach ($params as $key => $value) {
                $statement->bindParam($key, $value);
            }
            if ($cursor !== null) {
                $statement->bindParam(':cursor', $cursor);
            }
            $statement->bindParam(':limit', $perPage, PDO::PARAM_INT);
            
            $statement->execute();
            
            $data = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $className, []);
            
            // Calculate cursors
            $nextCursor = null;
            $previousCursor = null;
            
            if (!empty($data)) {
                $firstItem = $data[0];
                $lastItem = end($data);
                
                // Get cursor values using reflection or direct property access
                $nextCursor = $this->getCursorValue($lastItem, $cursorField);
                $previousCursor = $this->getCursorValue($firstItem, $cursorField);
            }
            
            $hasNextPage = count($data) === $perPage;
            $hasPreviousPage = $cursor !== null;
            
            return new PaginationResult(
                data: $data,
                total: $total,
                page: 0, // Not applicable for cursor pagination
                perPage: $perPage,
                totalPages: 0, // Not applicable for cursor pagination
                hasNextPage: $hasNextPage,
                hasPreviousPage: $hasPreviousPage,
                nextCursor: $nextCursor,
                previousCursor: $previousCursor
            );
            
        } catch (\PDOException $e) {
            error_log("Error in cursor paginated query: " . $e->getMessage());
            return new PaginationResult(
                data: [],
                total: 0,
                page: 0,
                perPage: $perPage,
                totalPages: 0,
                hasNextPage: false,
                hasPreviousPage: false
            );
        }
    }

    /**
     * Get cursor value from an object using reflection
     * 
     * @param object $object The object to extract cursor value from
     * @param string $field The field name
     * @return string|null
     */
    private function getCursorValue(object $object, string $field): ?string
    {
        try {
            $reflection = new \ReflectionClass($object);
            
            // Try to find a getter method
            $getterMethod = 'get' . ucfirst($field);
            if ($reflection->hasMethod($getterMethod)) {
                return (string) $object->$getterMethod();
            }
            
            // Try to access property directly
            if ($reflection->hasProperty($field)) {
                $property = $reflection->getProperty($field);
                $property->setAccessible(true);
                return (string) $property->getValue($object);
            }
            
            // Try to access public property
            if (property_exists($object, $field)) {
                return (string) $object->$field;
            }
            
            return null;
        } catch (\Exception $e) {
            error_log("Error getting cursor value: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Build a count SQL query from a base SQL query
     * 
     * @param string $sql The base SQL query
     * @return string The count SQL query
     */
    protected function buildCountQuery(string $sql): string
    {
        // Remove ORDER BY clause if present
        $sql = preg_replace('/\s+ORDER\s+BY\s+.*$/i', '', $sql);
        
        // Extract the FROM clause and everything after it
        if (preg_match('/FROM\s+(.+)$/i', $sql, $matches)) {
            return "SELECT COUNT(*) FROM " . $matches[1];
        }
        
        // Fallback: wrap the entire query
        return "SELECT COUNT(*) FROM ($sql) as count_table";
    }

    /**
     * Execute a wildcard search with pagination
     * 
     * @param string $sql The SQL query with :search placeholder
     * @param string $searchTerm The search term with wildcards
     * @param string $className The class name to instantiate results
     * @param int $page The page number (1-based)
     * @param int $perPage Number of items per page
     * @param array $additionalParams Additional parameters to bind
     * @param string $orderBy Optional ORDER BY clause
     * @return PaginationResult
     */
    protected function executeWildcardSearchPaginated(
        string $sql,
        string $searchTerm,
        string $className,
        int $page = 1,
        int $perPage = 20,
        array $additionalParams = [],
        string $orderBy = ''
    ): PaginationResult {
        $likePattern = $this->wildcardToLikePattern($searchTerm);
        
        // Build count query
        $countSql = $this->buildCountQuery($sql);
        
        // Prepare parameters
        $params = [':search' => $likePattern];
        $params = array_merge($params, $additionalParams);
        
        return $this->executePaginatedQuery(
            $sql,
            $countSql,
            $className,
            $page,
            $perPage,
            $params,
            $orderBy
        );
    }

}