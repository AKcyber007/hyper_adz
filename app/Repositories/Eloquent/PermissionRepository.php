<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\PermissionRepositoryInterface;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Collection;

class PermissionRepository implements PermissionRepositoryInterface
{
    /**
     * Get all permissions.
     *
     * @return Collection
     */
    public function all(): Collection
    {
        return Permission::all();
    }

    /**
     * Find a permission by name.
     *
     * @param string $name
     * @return Permission|null
     */
    public function findByName(string $name): ?Permission
    {
        return Permission::where('name', $name)->first();
    }

    /**
     * Create a new permission.
     *
     * @param string $name
     * @return Permission
     */
    public function create(string $name): Permission
    {
        return Permission::create(['name' => $name]);
    }

    /**
     * Give a permission to a role.
     *
     * @param Role $role
     * @param string $permission
     * @return void
     */
    public function givePermissionTo(Role $role, string $permission): void
    {
        $role->givePermissionTo($permission);
    }

    /**
     * Sync permissions to a role.
     *
     * @param Role $role
     * @param array $permissions
     * @return void
     */
    public function syncPermissions(Role $role, array $permissions): void
    {
        $role->syncPermissions($permissions);
    }
}
