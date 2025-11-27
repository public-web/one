<?php

namespace Tests\Unit\Models;

use App\Models\Articulo;
use App\Models\User;
use App\Models\UserImportExport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserRelationsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    // ========== Relationship Tests ==========

    public function test_user_has_roles_relationship(): void
    {
        $role = Role::create(['name' => 'admin']);
        $this->user->assignRole($role);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $this->user->roles);
        $this->assertTrue($this->user->roles->contains($role));
    }

    public function test_user_has_import_exports_relationship(): void
    {
        $importExport = UserImportExport::create([
            'user_id' => $this->user->id,
            'type' => \App\Enums\ImportExportType::Import,
            'status' => \App\Enums\ImportExportStatus::Pending,
            'file_name' => 'test.csv',
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $this->user->importExports());
        $this->assertTrue($this->user->importExports->contains($importExport));
    }

    public function test_user_has_articulos_relationship(): void
    {
        $articulo = Articulo::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $this->user->articulos());
        $this->assertTrue($this->user->articulos->contains($articulo));
    }

    // ========== Role Method Tests ==========

    public function test_is_super_admin_returns_true_for_superadmin(): void
    {
        Role::create(['name' => 'superadmin']);
        $this->user->assignRole('superadmin');

        $this->assertTrue($this->user->isSuperAdmin());
    }

    public function test_is_super_admin_returns_false_for_non_superadmin(): void
    {
        Role::create(['name' => 'user']);
        $this->user->assignRole('user');

        $this->assertFalse($this->user->isSuperAdmin());
    }

    public function test_is_admin_returns_true_for_admin(): void
    {
        Role::create(['name' => 'admin']);
        $this->user->assignRole('admin');

        $this->assertTrue($this->user->isAdmin());
    }

    public function test_is_admin_returns_true_for_superadmin(): void
    {
        Role::create(['name' => 'superadmin']);
        $this->user->assignRole('superadmin');

        $this->assertTrue($this->user->isAdmin());
    }

    public function test_is_admin_returns_false_for_regular_user(): void
    {
        Role::create(['name' => 'user']);
        $this->user->assignRole('user');

        $this->assertFalse($this->user->isAdmin());
    }

    public function test_get_primary_role_returns_first_role(): void
    {
        $role = Role::create(['name' => 'editor']);
        $this->user->assignRole($role);

        $this->assertEquals('editor', $this->user->getPrimaryRole());
    }

    public function test_get_primary_role_returns_null_when_no_roles(): void
    {
        $this->assertNull($this->user->getPrimaryRole());
    }

    // ========== Status Method Tests ==========

    public function test_needs_password_change_returns_true_when_never_changed(): void
    {
        $user = User::factory()->create(['password_changed_at' => null]);

        $this->assertTrue($user->needsPasswordChange());
    }

    public function test_needs_password_change_returns_false_when_changed(): void
    {
        $user = User::factory()->create(['password_changed_at' => now()]);

        $this->assertFalse($user->needsPasswordChange());
    }

    public function test_has_expired_returns_true_for_expired_user(): void
    {
        $user = User::factory()->create(['expires_at' => now()->subDay()]);

        $this->assertTrue($user->hasExpired());
    }

    public function test_has_expired_returns_false_for_non_expired_user(): void
    {
        $user = User::factory()->create(['expires_at' => now()->addDay()]);

        $this->assertFalse($user->hasExpired());
    }

    public function test_has_expired_returns_false_when_no_expiration(): void
    {
        $user = User::factory()->create(['expires_at' => null]);

        $this->assertFalse($user->hasExpired());
    }

    public function test_is_active_and_not_expired_returns_true(): void
    {
        $user = User::factory()->create([
            'active' => true,
            'expires_at' => now()->addDay(),
        ]);

        $this->assertTrue($user->isActiveAndNotExpired());
    }

    public function test_is_active_and_not_expired_returns_false_for_inactive(): void
    {
        $user = User::factory()->create([
            'active' => false,
            'expires_at' => now()->addDay(),
        ]);

        $this->assertFalse($user->isActiveAndNotExpired());
    }

    public function test_is_active_and_not_expired_returns_false_for_expired(): void
    {
        $user = User::factory()->create([
            'active' => true,
            'expires_at' => now()->subDay(),
        ]);

        $this->assertFalse($user->isActiveAndNotExpired());
    }

    // ========== Scope Tests ==========

    public function test_active_scope_filters_active_users(): void
    {
        User::factory()->create(['active' => true, 'name' => 'Active User']);
        User::factory()->create(['active' => false, 'name' => 'Inactive User']);

        $activeUsers = User::active()->get();

        $this->assertGreaterThan(0, $activeUsers->count());
        $this->assertTrue($activeUsers->every(fn($user) => $user->active === true));
    }

    public function test_not_expired_scope_filters_non_expired_users(): void
    {
        User::factory()->create(['expires_at' => now()->addDay()]);
        User::factory()->create(['expires_at' => now()->subDay()]);
        User::factory()->create(['expires_at' => null]);

        $nonExpiredUsers = User::notExpired()->get();

        $this->assertGreaterThan(0, $nonExpiredUsers->count());
        foreach ($nonExpiredUsers as $user) {
            $this->assertFalse($user->hasExpired());
        }
    }

    public function test_active_and_not_expired_scope(): void
    {
        User::factory()->create(['active' => true, 'expires_at' => now()->addDay()]);
        User::factory()->create(['active' => false, 'expires_at' => now()->addDay()]);
        User::factory()->create(['active' => true, 'expires_at' => now()->subDay()]);

        $users = User::activeAndNotExpired()->get();

        $this->assertGreaterThan(0, $users->count());
        foreach ($users as $user) {
            $this->assertTrue($user->isActiveAndNotExpired());
        }
    }

    public function test_search_scope_filters_by_name(): void
    {
        User::factory()->create(['name' => 'John Doe']);
        User::factory()->create(['name' => 'Jane Smith']);

        $results = User::search('John')->get();

        $this->assertEquals(1, $results->count());
        $this->assertEquals('John Doe', $results->first()->name);
    }

    public function test_search_scope_filters_by_email(): void
    {
        User::factory()->create(['email' => 'john@example.com']);
        User::factory()->create(['email' => 'jane@example.com']);

        $results = User::search('john@')->get();

        $this->assertEquals(1, $results->count());
        $this->assertEquals('john@example.com', $results->first()->email);
    }

    public function test_search_scope_returns_all_when_empty(): void
    {
        User::factory()->count(3)->create();

        $results = User::search('')->get();

        $this->assertGreaterThanOrEqual(3, $results->count());
    }

    public function test_with_role_scope(): void
    {
        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'user']);

        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        $user = User::factory()->create();
        $user->assignRole($userRole);

        $admins = User::withRole('admin')->get();

        $this->assertEquals(1, $admins->count());
        $this->assertEquals($admin->id, $admins->first()->id);
    }

    public function test_by_status_scope_active(): void
    {
        User::factory()->create(['active' => true]);
        User::factory()->create(['active' => false]);

        $activeUsers = User::byStatus('active')->get();

        $this->assertGreaterThan(0, $activeUsers->count());
        $this->assertTrue($activeUsers->every(fn($user) => $user->active === true));
    }

    public function test_by_status_scope_inactive(): void
    {
        User::factory()->create(['active' => true]);
        User::factory()->create(['active' => false]);

        $inactiveUsers = User::byStatus('inactive')->get();

        $this->assertGreaterThan(0, $inactiveUsers->count());
        $this->assertTrue($inactiveUsers->every(fn($user) => $user->active === false));
    }

    public function test_by_status_scope_deleted(): void
    {
        $deletedUser = User::factory()->create();
        $deletedUser->delete();

        User::factory()->create(['active' => true]);

        $deletedUsers = User::byStatus('deleted')->get();

        $this->assertEquals(1, $deletedUsers->count());
        $this->assertTrue($deletedUsers->first()->trashed());
    }

    public function test_expiring_scope_soon(): void
    {
        User::factory()->create(['expires_at' => now()->addDays(15)]);
        User::factory()->create(['expires_at' => now()->addDays(60)]);

        $expiringSoon = User::expiring('soon')->get();

        $this->assertGreaterThan(0, $expiringSoon->count());
    }

    public function test_expiring_scope_expired(): void
    {
        User::factory()->create(['expires_at' => now()->subDay()]);
        User::factory()->create(['expires_at' => now()->addDay()]);

        $expired = User::expiring('expired')->get();

        $this->assertGreaterThan(0, $expired->count());
        $this->assertTrue($expired->every(fn($user) => $user->hasExpired()));
    }
}
