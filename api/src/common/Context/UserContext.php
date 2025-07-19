<?php

namespace App\common\Context;

class UserContext
{
    // Role constants to avoid hardcoding
    public const ROLE_SUPERUSER = 'SUPERUSER';
    public const ROLE_ADMIN = 'ADMIN';
    public const ROLE_VOLONTEER = 'VOLONTEER';
    public const ROLE_COMPETITOR = 'COMPETITOR';
    public const ROLE_DEVELOPER = 'DEVELOPER';
    public const ROLE_USER = 'USER';
    
    // Role property names for new format
    public const ROLE_PROP_IS_SUPERUSER = 'isSuperuser';
    public const ROLE_PROP_IS_ADMIN = 'isAdmin';
    public const ROLE_PROP_IS_VOLONTEER = 'isVolonteer';
    public const ROLE_PROP_IS_COMPETITOR = 'isCompetitor';
    public const ROLE_PROP_IS_DEVELOPER = 'isDeveloper';
    public const ROLE_PROP_IS_USER = 'isUser';

    private static ?UserContext $instance = null;
    private ?string $userId = null;
    private ?int $organizerId = null;
    private array $roles = [];
    private array $permissions = [];

    private function __construct()
    {
        // Private constructor to enforce singleton pattern
    }

    public static function getInstance(): UserContext
    {
        if (self::$instance === null) {
            self::$instance = new UserContext();
        }
        return self::$instance;
    }

    public function initialize(string $userId, ?int $organizerId, array $roles, array $permissions = []): void
    {
        $this->userId = $userId;
        $this->organizerId = $organizerId;
        $this->roles = $roles;
        $this->permissions = $permissions;
    }

    public function clear(): void
    {
        $this->userId = null;
        $this->organizerId = null;
        $this->roles = [];
        $this->permissions = [];
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function getOrganizerId(): ?int
    {
        return $this->organizerId;
    }

    public function hasOrganization(): bool
    {
        return $this->organizerId !== null;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getPermissions(): array
    {
        return $this->permissions;
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles);
    }

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions);
    }

    public function isAdmin(): bool
    {
        // Check if roles is an array of strings (old format)
        if (is_array($this->roles) && !empty($this->roles) && isset($this->roles[0]) && is_string($this->roles[0])) {
            return $this->hasRole(self::ROLE_ADMIN);
        }
        
        // Check if roles is an object with boolean values (new format)
        if (is_array($this->roles) && isset($this->roles[self::ROLE_PROP_IS_ADMIN])) {
            return (bool) $this->roles[self::ROLE_PROP_IS_ADMIN];
        }
        
        return false;
    }

    public function isSuperUser(): bool
    {
        // Check if roles is an array of strings (old format)
        if (is_array($this->roles) && !empty($this->roles) && isset($this->roles[0]) && is_string($this->roles[0])) {
            return $this->hasRole(self::ROLE_SUPERUSER);
        }
        
        // Check if roles is an object with boolean values (new format)
        if (is_array($this->roles) && isset($this->roles[self::ROLE_PROP_IS_SUPERUSER])) {
            return (bool) $this->roles[self::ROLE_PROP_IS_SUPERUSER];
        }
        
        return false;
    }

    public function isVolonteer(): bool
    {
        // Check if roles is an array of strings (old format)
        if (is_array($this->roles) && !empty($this->roles) && isset($this->roles[0]) && is_string($this->roles[0])) {
            return $this->hasRole(self::ROLE_VOLONTEER);
        }
        
        // Check if roles is an object with boolean values (new format)
        if (is_array($this->roles) && isset($this->roles[self::ROLE_PROP_IS_VOLONTEER])) {
            return (bool) $this->roles[self::ROLE_PROP_IS_VOLONTEER];
        }
        
        return false;
    }

    public function isCompetitor(): bool
    {
        // Check if roles is an array of strings (old format)
        if (is_array($this->roles) && !empty($this->roles) && isset($this->roles[0]) && is_string($this->roles[0])) {
            return $this->hasRole(self::ROLE_COMPETITOR);
        }
        
        // Check if roles is an object with boolean values (new format)
        if (is_array($this->roles) && isset($this->roles[self::ROLE_PROP_IS_COMPETITOR])) {
            return (bool) $this->roles[self::ROLE_PROP_IS_COMPETITOR];
        }
        
        return false;
    }

    public function isDeveloper(): bool
    {
        // Check if roles is an array of strings (old format)
        if (is_array($this->roles) && !empty($this->roles) && isset($this->roles[0]) && is_string($this->roles[0])) {
            return $this->hasRole(self::ROLE_DEVELOPER);
        }
        
        // Check if roles is an object with boolean values (new format)
        if (is_array($this->roles) && isset($this->roles[self::ROLE_PROP_IS_DEVELOPER])) {
            return (bool) $this->roles[self::ROLE_PROP_IS_DEVELOPER];
        }
        
        return false;
    }
} 