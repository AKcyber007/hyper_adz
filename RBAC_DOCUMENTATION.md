# Hyper Adz – Role-Based Access Control (RBAC) Documentation

This document explains the security and authorization architecture implemented in Phase 2 of the Hyper Adz platform. It leverages Laravel 12 and the **Spatie Laravel Permission** package.

---

## 1. Roles & Permissions Architecture

The system uses a flexible and scalable RBAC model where **Users** are assigned **Roles**, and **Roles** are assigned **Permissions**. Users inherit permissions through their assigned roles.

```
User (e.g., admin@hyperadz.in)
  └── Assigned Role (Admin)
        └── Inherited Permissions (manage-users, manage-locations, etc.)
```

### Roles
The system is pre-seeded with three default roles:
*   **Admin**: Total system administration access.
*   **location_partner**: Business entities that own or lease physical locations and screens.
*   **Advertiser**: Clients who buy ad space and request/run marketing campaigns.

### Permissions
Permissions are granular actions that a user or role can perform. The system defines the following permissions:
*   `manage-users`: Create, view, edit, and delete user accounts.
*   `manage-location-partners`: Manage Location Partner-specific information and onboarding.
*   `manage-advertisers`: Manage Advertiser-specific registrations and approvals.
*   `manage-locations`: Manage physical locations.
*   `manage-screens`: Manage display screens at various locations.
*   `manage-campaigns`: Create, review, approve, and schedule advertisement campaigns.
*   `manage-reports`: Access analytics, logs, and billing reports.
*   `manage-cms`: Edit website content, terms, policies, and marketing copy.

### Initial Role-Permission Mapping
*   **Admin**: Assigned **all** permissions.
*   **location_partner**: No permissions assigned by default (to be expanded in Phase 3/4).
*   **Advertiser**: No permissions assigned by default (to be expanded in Phase 3/4).

---

## 2. Technical Implementation details

### Repository Pattern
To maintain high decoupling and allow future storage changes without modifying business logic, all database operations are wrapped in Repositories:
*   **`RoleRepositoryInterface`** & **`RoleRepository`**: Handles role creation, lookup, and user assignment.
*   **`PermissionRepositoryInterface`** & **`PermissionRepository`**: Handles permission creation, lookup, and role assignment/syncing.

### Service Layer
*   **`RolePermissionService`**: Encapsulates high-level business rules, keeping the Controllers lean and free of DB/ORM concerns.
    *   `createRole(string $name)`
    *   `createPermission(string $name)`
    *   `assignRoleToUser(User $user, string $roleName)`
    *   `syncPermissionsToRole(string $roleName, array $permissionNames)`
    *   `userHasPermission(User $user, string $permissionName)`
    *   `userHasRole(User $user, string $roleName)`

### Middleware Configuration
The following Spatie middlewares are registered as aliases in [bootstrap/app.php](file:///d:/UI%20design/bootstrap/app.php):
*   `role`: Restricts access to specific role(s).
*   `permission`: Restricts access to specific permission(s).
*   `role_or_permission`: Restricts access to either a role or permission.

---

## 3. Developer Guide & Usage Examples

### Protecting Routes
Apply the middleware in [routes/web.php](file:///d:/UI%20design/routes/web.php) to guard routes:

```php
// Only users with the 'Admin' role can access these routes
Route::middleware(['auth', 'role:Admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
});

// Or, protecting by specific permission
Route::middleware(['auth', 'permission:manage-locations'])->group(function () {
    Route::get('/locations/create', [LocationController::class, 'create']);
});
```

### Controller Authorization
You can perform checks inside controller methods to ensure fine-grained control:

```php
public function destroy(User $user)
{
    // Check if the current user has the permission to manage users
    if (!auth()->user()->can('manage-users')) {
        abort(403, 'Unauthorized action.');
    }

    $user->delete();
    return redirect()->back();
}
```

### Blade Templating
Use direct directive checks in `.blade.php` files to conditionally render UI components:

```html
{{-- Check by Role --}}
@role('Admin')
    <a href="/admin/settings">System Settings</a>
@endrole

{{-- Check by Permission --}}
@can('manage-users')
    <div class="sidebar-section">User Management</div>
@endcan
```

---

## 4. Future Expansion & Integration Strategy

As the platform grows, here is how each module will utilize this RBAC setup:

### A. Location Partner Portal
*   **Role**: `location_partner`
*   **Required Permissions**: `view-own-locations`, `view-own-screens`, `edit-own-locations`.
*   **Portal URL Prefix**: `/location-partner`
*   **Implementation**: Create new permissions such as `view-own-locations` under the `PermissionSeeder`. In the Location Partner Portal routes group, apply `->middleware('role:location_partner')`. Extend controllers to scope query results to the logged-in partner's user ID (e.g. `Location::where('partner_id', auth()->id())->get()`).

### B. Advertiser Portal
*   **Role**: `Advertiser`
*   **Required Permissions**: `create-campaign-requests`, `view-own-reports`, `manage-billing`.
*   **Portal URL Prefix**: `/advertiser` (or similar)
*   **Implementation**: Assign the `Advertiser` role to users registering as advertisers. Protect the advertiser dashboard using `->middleware('role:Advertiser')`.

### C. Location & Screen Management
*   **Permissions**: `manage-locations`, `manage-screens`
*   **Implementation**: Admin routes to add, edit, or delete locations and screens will be guarded by `middleware('permission:manage-locations|manage-screens')`. Location Partners will be restricted to view or edit only their own screens.

### D. Campaign Management
*   **Permissions**: `manage-campaigns`
*   **Implementation**: Admins can approve or reject campaigns via the Admin Portal (guarded by `manage-campaigns`). Advertisers can create and submit campaigns for approval.

