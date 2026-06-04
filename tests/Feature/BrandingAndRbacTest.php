<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BrandingAndRbacTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_uses_hot_l_branding()
    {
        $response = $this->get('/login');

        $response
            ->assertOk()
            ->assertSee('Hot-L')
            ->assertSee('admin@hot-l.test')
            ->assertDontSee('Hotelio')
            ->assertDontSee('Hotello');
    }

    public function test_staff_sidebar_only_shows_allowed_navigation()
    {
        $user = $this->userWithRole('Staff');

        $response = $this->actingAs($user)->get('/home');

        $response
            ->assertOk()
            ->assertSee('Dashboard')
            ->assertSee('Bookings')
            ->assertSee('Guests')
            ->assertSee('Room Transfer')
            ->assertDontSee('Administration')
            ->assertDontSee('Finance')
            ->assertDontSee('Roles & Permissions')
            ->assertDontSee('Payments');
    }

    public function test_cashier_sidebar_shows_finance_but_not_administration()
    {
        $user = $this->userWithRole('Cashier');

        $response = $this->actingAs($user)->get('/home');

        $response
            ->assertOk()
            ->assertSee('Finance')
            ->assertSee('Payments')
            ->assertSee('Bookings')
            ->assertDontSee('Administration')
            ->assertDontSee('Employees')
            ->assertDontSee('Account Ledger');
    }

    public function test_web_routes_require_matching_permissions()
    {
        $staff = $this->userWithRole('Staff');
        $admin = $this->userWithRole('Admin');

        $this->actingAs($staff)->get('/booking')->assertOk();
        $this->actingAs($staff)->get('/bank')->assertForbidden();
        $this->actingAs($staff)->get('/role')->assertForbidden();

        $this->actingAs($admin)->get('/role')->assertOk();
    }

    public function test_api_routes_require_authentication_and_permissions()
    {
        $staff = $this->userWithRole('Staff');
        $superAdmin = $this->userWithRole('SuperAdmin');

        $this->getJson('/api/v1/bank')->assertUnauthorized();

        Sanctum::actingAs($staff);
        $this->getJson('/api/v1/bank')->assertForbidden();

        Sanctum::actingAs($superAdmin);
        $this->getJson('/api/v1/bank')->assertOk();
    }

    public function test_super_admin_can_create_and_update_roles()
    {
        $superAdmin = $this->userWithRole('SuperAdmin');

        $this->actingAs($superAdmin)->post('/role', [
            'name' => 'Night Auditor',
            'permissions' => [
                'dashboard.view',
                'booking.view',
                'booking.edit',
            ],
        ])->assertRedirect();

        $role = Role::query()->firstWhere('name', 'Night Auditor');

        $this->assertNotNull($role);
        $this->assertTrue($role->permissions()->where('name', 'booking.edit')->exists());

        $this->actingAs($superAdmin)->put("/role/{$role->id}", [
            'name' => 'Lead Night Auditor',
            'permissions' => [
                'dashboard.view',
                'booking.view',
            ],
        ])->assertRedirect();

        $this->assertDatabaseHas('roles', ['id' => $role->id, 'name' => 'Lead Night Auditor']);
        $this->assertFalse($role->fresh()->permissions()->where('name', 'booking.edit')->exists());
    }

    public function test_admin_cannot_edit_super_admin_role()
    {
        $admin = $this->userWithRole('Admin');
        $superAdminRole = Role::query()->firstWhere('name', 'SuperAdmin');

        $this->actingAs($admin)->put("/role/{$superAdminRole->id}", [
            'permissions' => Permission::query()->pluck('name')->all(),
        ])->assertForbidden();
    }

    public function test_admin_cannot_grant_permissions_they_do_not_have()
    {
        $adminRole = Role::query()->firstWhere('name', 'Admin');
        $allowedPermissionIds = Permission::query()
            ->whereIn('name', ['role.view', 'role.create', 'role.edit', 'dashboard.view'])
            ->pluck('id');

        $adminRole->permissions()->sync($allowedPermissionIds);

        $admin = $this->userWithRole('Admin');

        $this->actingAs($admin)->post('/role', [
            'name' => 'Front Office',
            'permissions' => ['dashboard.view', 'bank.view'],
        ])->assertForbidden();
    }

    public function test_role_column_stays_synced_with_role_id()
    {
        $managerRole = Role::query()->firstWhere('name', 'Manager');
        $cashierRole = Role::query()->firstWhere('name', 'Cashier');

        $user = User::factory()->create([
            'role_id' => $managerRole->id,
        ]);

        $this->assertSame('Manager', $user->fresh()->Role);

        $user->update(['role_id' => $cashierRole->id]);

        $this->assertSame('Cashier', $user->fresh()->Role);
    }

    protected function userWithRole(string $roleName): User
    {
        $role = Role::query()->firstWhere('name', $roleName);

        return User::factory()->create([
            'role_id' => $role?->id,
            'Role' => $roleName,
        ]);
    }
}
