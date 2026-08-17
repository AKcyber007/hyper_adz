<?php

namespace App\Repositories\Contracts;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Collection;

interface PermissionRepositoryInterface
{
    /**
     * Get all permissions.
     *
     * @return Collection
     */
    public function all(): Collection;

    /**
     * Find a permission by name.
     *
     * @param string $name
     * @return Permission|null
     */
    public function findByName(string $name): ?Permission;

    /**
     * Create a new permission.
     *
     * @param string $name
     * @return Permission
     */
    public function create(string $name): Permission;

    /**
     * Give a permission to a role.
     *
     * @param Role $role
     * @param string $permission
     * @return void
     */
    public function givePermissionTo(Role $role, string $permission): void;

    /**
     * Sync permissions to a role.
     *
     * @param Role $role
     * @param array $permissions
     * @return void
     */
    public function syncPermissions(Role $role, array $permissions): void;
}
