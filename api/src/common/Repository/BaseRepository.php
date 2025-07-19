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
     * Get the organizer ID from UserContext
     * 
     * @return int|null The organizer ID from the current user context, or null if not available
     */
    public function getOrganizerIdFromContext(): ?int
    {
        $userContext = \App\common\Context\UserContext::getInstance();
        return $userContext->getOrganizerId();
    }

    /**
     * Check if the current user has an organization
     * 
     * @return bool True if the user has an organization, false otherwise
     */
    public function hasOrganization(): bool
    {
        $userContext = \App\common\Context\UserContext::getInstance();
        return $userContext->hasOrganization();
    }

    /**
     * Get the current timestamp in MySQL format
     * 
     * @return string Current timestamp in 'Y-m-d H:i:s' format
     */
    protected function getCurrentTimestamp(): string
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * Get the current date in MySQL format
     * 
     * @return string Current date in 'Y-m-d' format
     */
    protected function getCurrentDate(): string
    {
        return date('Y-m-d');
    }

    /**
     * Get the created_at timestamp for new records
     * 
     * @return string Current timestamp for created_at field
     */
    protected function getCreatedAtTimestamp(): string
    {
        return $this->getCurrentTimestamp();
    }

    /**
     * Get the updated_at timestamp for modified records
     * 
     * @return string Current timestamp for updated_at field
     */
    protected function getUpdatedAtTimestamp(): string
    {
        return $this->getCurrentTimestamp();
    }

    /**
     * Bind created_at and updated_at parameters to a prepared statement
     * 
     * @param \PDOStatement $statement The prepared statement
     * @param bool $isUpdate Whether this is an update operation (affects which timestamps to bind)
     */
    protected function bindTimestampParameters(\PDOStatement $statement, bool $isUpdate = false): void
    {
        if ($isUpdate) {
            // For updates, only set updated_at
            $statement->bindValue(':updated_at', $this->getUpdatedAtTimestamp());
        } else {
            // For inserts, set both created_at and updated_at
            $statement->bindValue(':created_at', $this->getCreatedAtTimestamp());
            $statement->bindValue(':updated_at', $this->getUpdatedAtTimestamp());
        }
    }

    /**
     * Get SQL fragment for timestamp columns in INSERT statements
     * 
     * @return string SQL fragment like "created_at, updated_at"
     */
    protected function getTimestampColumns(): string
    {
        return 'created_at, updated_at';
    }

    /**
     * Get SQL fragment for timestamp values in INSERT statements
     * 
     * @return string SQL fragment like ":created_at, :updated_at"
     */
    protected function getTimestampValues(): string
    {
        return ':created_at, :updated_at';
    }

    /**
     * Get SQL fragment for updating timestamp in UPDATE statements
     * 
     * @return string SQL fragment like "updated_at = :updated_at"
     */
    protected function getUpdateTimestampFragment(): string
    {
        return 'updated_at = :updated_at';
    }

    /**
     * Check if organizer filtering should be applied based on user roles
     * 
     * @return bool True if filtering should be applied, false if user has elevated privileges
     */
    protected function shouldApplyOrganizerFilter(): bool
    {
        $userContext = \App\common\Context\UserContext::getInstance();
        
        // Skip filtering for SUPERUSER only (aligns with service layer logic)
        if ($userContext->isSuperUser()) {
            return false;
        }
        
        // Apply filtering for all other roles
        return true;
    }

    /**
     * Get organizer filter SQL fragment
     * 
     * @param string $tableAlias The table alias to use in the WHERE clause
     * @return string SQL fragment for organizer filtering or empty string if no filtering needed
     */
    protected function getOrganizerFilterSql(string $tableAlias = ''): string
    {
        if (!$this->shouldApplyOrganizerFilter()) {
            return '';
        }
        
        $organizerId = $this->getOrganizerIdFromContext();
        if ($organizerId === null) {
            return '';
        }
        
        $prefix = $tableAlias ? $tableAlias . '.' : '';
        return $prefix . 'organizer_id = ' . $organizerId;
    }

    /**
     * Get organizer filter SQL fragment with parameter binding
     * 
     * @param string $tableAlias The table alias to use in the WHERE clause
     * @param string $paramName The parameter name to use (default: 'organizer_id')
     * @return string SQL fragment for organizer filtering with parameter or empty string if no filtering needed
     */
    protected function getOrganizerFilterSqlWithParam(string $tableAlias = '', string $paramName = 'organizer_id'): string
    {
        if (!$this->shouldApplyOrganizerFilter()) {
            return '';
        }
        
        $organizerId = $this->getOrganizerIdFromContext();
        if ($organizerId === null) {
            return '';
        }
        
        $prefix = $tableAlias ? $tableAlias . '.' : '';
        return $prefix . 'organizer_id = :' . $paramName;
    }

    /**
     * Bind organizer filter parameter to a prepared statement
     * 
     * @param \PDOStatement $statement The prepared statement
     * @param string $paramName The parameter name to use (default: 'organizer_id')
     * @return bool True if parameter was bound, false if no filtering needed
     */
    protected function bindOrganizerFilterParameter(\PDOStatement $statement, string $paramName = 'organizer_id'): bool
    {
        if (!$this->shouldApplyOrganizerFilter()) {
            return false;
        }
        
        $organizerId = $this->getOrganizerIdFromContext();
        if ($organizerId === null) {
            return false;
        }
        
        $statement->bindValue(':' . $paramName, $organizerId);
        return true;
    }

}