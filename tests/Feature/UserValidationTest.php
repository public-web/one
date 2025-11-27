<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserValidationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Role $adminRole;

    protected function setUp(): void
    {
        parent::setUp();

        // Create permissions
        Permission::create(['name' => 'users.list']);
        Permission::create(['name' => 'users.view']);
        Permission::create(['name' => 'users.create']);
        Permission::create(['name' => 'users.edit']);
        Permission::create(['name' => 'users.delete']);

        // Create roles
        $this->adminRole = Role::create(['name' => 'admin']);
        $this->adminRole->givePermissionTo([
            'users.list',
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
        ]);

        Role::create(['name' => 'user']);
        Role::create(['name' => 'editor']);

        // Create authenticated admin user
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_name_is_required_when_creating_user(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/users', [
            'email' => 'test@example.com',
            'role' => 'user',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_email_is_required_when_creating_user(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/users', [
            'name' => 'Test User',
            'role' => 'user',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_email_must_be_valid_format(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/users', [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'role' => 'user',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_email_must_be_unique_when_creating_user(): void
    {
        $existingUser = User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->actingAs($this->admin)->postJson('/users', [
            'name' => 'Test User',
            'email' => 'existing@example.com',
            'role' => 'user',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_role_is_required_when_creating_user(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['role']);
    }

    public function test_role_must_be_valid(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'invalid-role',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['role']);
    }

    public function test_name_cannot_exceed_255_characters(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/users', [
            'name' => str_repeat('a', 256),
            'email' => 'test@example.com',
            'role' => 'user',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_expires_at_must_be_future_or_today(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'user',
            'expires_at' => now()->subDay()->format('Y-m-d'),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['expires_at']);
    }

    public function test_can_create_user_with_valid_data(): void
    {
        $response = $this->actingAs($this->admin)->post('/users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'user',
            'active' => true,
        ]);

        $response->assertRedirect('/users');
        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }

    public function test_name_is_required_when_updating_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)->putJson("/users/{$user->id}", [
            'email' => 'updated@example.com',
            'role' => 'user',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_email_is_required_when_updating_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)->putJson("/users/{$user->id}", [
            'name' => 'Updated Name',
            'role' => 'user',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_email_must_be_unique_except_own_email_when_updating(): void
    {
        $user1 = User::factory()->create(['email' => 'user1@example.com']);
        $user2 = User::factory()->create(['email' => 'user2@example.com']);

        // Try to update user1 with user2's email
        $response = $this->actingAs($this->admin)->putJson("/users/{$user1->id}", [
            'name' => 'Updated Name',
            'email' => 'user2@example.com',
            'role' => 'user',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_can_keep_same_email_when_updating_user(): void
    {
        $user = User::factory()->create(['email' => 'user@example.com']);

        $response = $this->actingAs($this->admin)->put("/users/{$user->id}", [
            'name' => 'Updated Name',
            'email' => 'user@example.com',
            'role' => 'user',
            'active' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'user@example.com',
        ]);
    }

    public function test_active_field_must_be_boolean(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'user',
            'active' => 'not-a-boolean',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['active']);
    }

    public function test_require_2fa_must_be_boolean(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'user',
            'require_2fa' => 'not-a-boolean',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['require_2fa']);
    }
}
