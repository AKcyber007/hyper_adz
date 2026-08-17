<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Repositories\Contracts\PermissionRepositoryInterface;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Collection;

class RolePermissionService
{
    protected RoleRepositoryInterface $roleRepository;
    protected PermissionRepositoryInterface $permissionRepository;

    public function __construct(
        RoleRepositoryInterface $roleRepository,
        PermissionRepositoryInterface $permissionRepository
    ) {
        $this->roleRepository = $roleRepository;
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * Create a new role.
     *
     * @param string $name
     * @return Role
     */
    public function createRole(string $name): Role
    {
        return $this->roleRepository->create($name);
    }

    /**
     * Create a new permission.
     *
     * @param string $name
     * @return Permission
     */
    public function createPermission(string $name): Permission
    {
        return $this->permissionRepository->create($name);
    }

    /**
     * Assign a role to a user.
     *
     * @param User $user
     * @param string $roleName
     * @return void
     */
    public function assignRoleToUser(User $user, string $roleName): void
    {
        $this->roleRepository->assignRole($user, $roleName);
    }

    /**
     * Sync permissions to a role.
     *
     * @param string $roleName
     * @param array $permissionNames
     * @return void
     */
    public function syncPermissionsToRole(string $roleName, array $permissionNames): void
    {
        $role = $this->roleRepository->findByName($roleName);
        if ($role) {
            $this->permissionRepository->syncPermissions($role, $permissionNames);
        }
    }

    /**
     * Check if a user has a specific permission.
     *
     * @param User $user
     * @param string $permissionName
     * @return bool
     */
    public function userHasPermission(User $user, string $permissionName): bool
    {
        return $user->hasPermissionTo($permissionName);
    }

    /**
     * Check if a user has a specific role.
     *
     * @param User $user
     * @param string $roleName
     * @return bool
     */
    public function userHasRole(User $user, string $roleName): bool
    {
        return $this->roleRepository->hasRole($user, $roleName);
    }
}
