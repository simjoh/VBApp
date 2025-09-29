<?php

namespace App\Traits;

trait HasJwtContext
{
    /**
     * Get the current user ID from JWT context
     *
     * @return string|null
     */
    protected function getCurrentUserId(): ?string
    {
        return request()->attributes->get('current_user_id');
    }

    /**
     * Get the current organizer ID from JWT context
     *
     * @return string|null
     */
    protected function getCurrentOrganizerId(): ?string
    {
        return request()->attributes->get('current_organizer_id');
    }

    /**
     * Get the current user roles from JWT context
     *
     * @return array
     */
    protected function getCurrentUserRoles(): array
    {
        return request()->attributes->get('current_user_roles', []);
    }

    /**
     * Check if the current user has a specific role
     *
     * @param string $role
     * @return bool
     */
    protected function hasRole(string $role): bool
    {
        return in_array($role, $this->getCurrentUserRoles());
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
}

