<?php

namespace App\Context;

class UserContext
{
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
        return $this->hasRole('ADMIN');
    }

    public function isSuperUser(): bool
    {
        return $this->hasRole('SUPERUSER');
    }

    public function isVolonteer(): bool
    {
        return $this->hasRole('VOLONTEER');
    }

    public function isCompetitor(): bool
    {
        return $this->hasRole('COMPETITOR');
    }

    public function isDeveloper(): bool
    {
        return $this->hasRole('DEVELOPER');
    }
}
