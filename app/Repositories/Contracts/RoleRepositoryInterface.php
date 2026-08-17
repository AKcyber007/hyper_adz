<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Collection;

interface RoleRepositoryInterface
{
    /**
     * Get all roles.
     *
     * @return Collection
     */
    public function all(): Collection;

    /**
     * Find a role by name.
     *
     * @param string $name
     * @return Role|null
     */
    public function findByName(string $name): ?Role;

    /**
     * Create a new role.
     *
     * @param string $name
     * @return Role
     */
    public function create(string $name): Role;

    /**
     * Assign a role to a user.
     *
     * @param User $user
     * @param string $role
     * @return void
     */
    public function assignRole(User $user, string $role): void;

    /**
     * Remove a role from a user.
     *
     * @param User $user
     * @param string $role
     * @return void
     */
    public function removeRole(User $user, string $role): void;

    /**
     * Check if a user has a role.
     *
     * @param User $user
     * @param string $role
     * @return bool
     */
    public function hasRole(User $user, string $role): bool;
}
