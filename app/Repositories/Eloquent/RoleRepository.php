<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\RoleRepositoryInterface;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Collection;

class RoleRepository implements RoleRepositoryInterface
{
    /**
     * Get all roles.
     *
     * @return Collection
     */
    public function all(): Collection
    {
        return Role::all();
    }

    /**
     * Find a role by name.
     *
     * @param string $name
     * @return Role|null
     */
    public function findByName(string $name): ?Role
    {
        return Role::where('name', $name)->first();
    }

    /**
     * Create a new role.
     *
     * @param string $name
     * @return Role
     */
    public function create(string $name): Role
    {
        return Role::create(['name' => $name]);
    }

    /**
     * Assign a role to a user.
     *
     * @param User $user
     * @param string $role
     * @return void
     */
    public function assignRole(User $user, string $role): void
    {
        $user->assignRole($role);
    }

    /**
     * Remove a role from a user.
     *
     * @param User $user
     * @param string $role
     * @return void
     */
    public function removeRole(User $user, string $role): void
    {
        $user->removeRole($role);
    }

    /**
     * Check if a user has a role.
     *
     * @param User $user
     * @param string $role
     * @return bool
     */
    public function hasRole(User $user, string $role): bool
    {
        return $user->hasRole($role);
    }
}
