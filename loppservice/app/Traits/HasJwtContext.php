<?php

namespace App\Traits;

use App\Context\UserContext;

trait HasJwtContext
{
    /**
     * Get the UserContext instance
     *
     * @return UserContext
     */
    protected function getUserContext(): UserContext
    {
        return UserContext::getInstance();
    }

    /**
     * Get the current user ID from JWT context
     *
     * @return string|null
     */
    protected function getCurrentUserId(): ?string
    {
        return $this->getUserContext()->getUserId();
    }

    /**
     * Get the current organizer ID from JWT context
     *
     * @return int|null
     */
    protected function getCurrentOrganizerId(): ?int
    {
        return $this->getUserContext()->getOrganizerId();
    }

    /**
     * Get the current user roles from JWT context
     *
     * @return array
     */
    protected function getCurrentUserRoles(): array
    {
        return $this->getUserContext()->getRoles();
    }

    /**
     * Check if the current user has a specific role
     *
     * @param string $role
     * @return bool
     */
    protected function hasRole(string $role): bool
    {
        return $this->getUserContext()->hasRole($role);
    }

    /**
     * Check if the current user has any of the specified roles
     *
     * @param array $roles
     * @return bool
     */
    protected function hasAnyRole(array $roles): bool
    {
        return !empty(array_intersect($roles, $this->getCurrentUserRoles()));
    }

    /**
     * Check if the current user is authenticated via JWT
     *
     * @return bool
     */
    protected function isJwtAuthenticated(): bool
    {
        return !is_null($this->getCurrentUserId());
    }

    /**
     * Check if the current user has an organization
     *
     * @return bool
     */
    protected function hasOrganization(): bool
    {
        return $this->getUserContext()->hasOrganization();
    }

    /**
     * Check if the current user is an admin
     *
     * @return bool
     */
    protected function isAdmin(): bool
    {
        return $this->getUserContext()->isAdmin();
    }

    /**
     * Check if the current user is a super user
     *
     * @return bool
     */
    protected function isSuperUser(): bool
    {
        return $this->getUserContext()->isSuperUser();
    }

    /**
     * Check if the current user is a volunteer
     *
     * @return bool
     */
    protected function isVolonteer(): bool
    {
        return $this->getUserContext()->isVolonteer();
    }

    /**
     * Check if the current user is a competitor
     *
     * @return bool
     */
    protected function isCompetitor(): bool
    {
        return $this->getUserContext()->isCompetitor();
    }

    /**
     * Check if the current user is a developer
     *
     * @return bool
     */
    protected function isDeveloper(): bool
    {
        return $this->getUserContext()->isDeveloper();
    }
}

