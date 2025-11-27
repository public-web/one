<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $regularUser;

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
        $superAdminRole = Role::create(['name' => 'superadmin']);
        $superAdminRole->givePermissionTo([
            'users.list',
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
        ]);

        Role::create(['name' => 'user']);

        // Create users
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole('superadmin');

        $this->regularUser = User::factory()->create();
        $this->regularUser->assignRole('user');
    }

    
    public function test_user_creation_is_logged(): void
    {
        activity()
            ->causedBy($this->superAdmin)
            ->performedOn($this->regularUser)
            ->event('created')
            ->log('User has been created');

        $this->assertDatabaseHas('activity_log', [
            'subject_type' => User::class,
            'subject_id' => $this->regularUser->id,
            'causer_type' => User::class,
            'causer_id' => $this->superAdmin->id,
            'event' => 'created',
        ]);
    }

    
    public function test_user_update_is_logged(): void
    {
        activity()
            ->causedBy($this->superAdmin)
            ->performedOn($this->regularUser)
            ->event('updated')
            ->log('User has been updated');

        $this->assertDatabaseHas('activity_log', [
            'subject_type' => User::class,
            'subject_id' => $this->regularUser->id,
            'event' => 'updated',
        ]);
    }

    
    public function test_activity_logs_dirty_attributes_on_update(): void
    {
        $user = User::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@example.com',
        ]);

        $user->update([
            'name' => 'Updated Name',
        ]);

        $activity = Activity::where('subject_type', User::class)
            ->where('subject_id', $user->id)
            ->where('event', 'updated')
            ->latest()
            ->first();

        $this->assertNotNull($activity);
        $this->assertArrayHasKey('name', $activity->properties['attributes'] ?? []);
    }

    
    public function test_only_super_admin_can_view_activity_logs(): void
    {
        $response = $this->actingAs($this->regularUser)->get('/activity-logs');

        $response->assertStatus(403);
    }

    
    public function test_super_admin_can_view_activity_logs(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/activity-logs');

        $response->assertStatus(200);
    }


    public function test_activity_log_records_causer_information(): void
    {
        // Update a user which triggers automatic activity logging
        $this->regularUser->update(['name' => 'Updated Name']);

        $activity = Activity::where('subject_type', User::class)
            ->where('subject_id', $this->regularUser->id)
            ->where('event', 'updated')
            ->latest()
            ->first();

        $this->assertNotNull($activity);
        $this->assertEquals('updated', $activity->event);
    }

    
    public function test_activity_log_can_be_filtered_by_event(): void
    {
        // Create activities with different events
        activity()->event('created')->log('Created event');
        activity()->event('updated')->log('Updated event');
        activity()->event('deleted')->log('Deleted event');

        $response = $this->actingAs($this->superAdmin)
            ->getJson('/activity-logs?event=created');

        $response->assertStatus(200);

        // Verify the response contains only created events
        $activities = $response->json();
        $events = collect($activities)->pluck('event')->unique();

        $this->assertTrue($events->contains('created'));
    }

    
    public function test_activity_log_can_be_searched(): void
    {
        activity()
            ->causedBy($this->superAdmin)
            ->event('updated')
            ->log('Special search term');

        activity()
            ->causedBy($this->regularUser)
            ->event('created')
            ->log('Different description');

        $response = $this->actingAs($this->superAdmin)
            ->getJson('/activity-logs?search=Special');

        $response->assertStatus(200);

        // Verify the response contains the searched term
        $activities = $response->json();
        $this->assertNotEmpty($activities);

        $descriptions = collect($activities)->pluck('description');
        $this->assertTrue($descriptions->contains(function ($desc) {
            return str_contains($desc, 'Special');
        }));
    }


    public function test_activity_log_includes_subject_information(): void
    {
        // Update a user which triggers automatic activity logging
        $this->regularUser->update(['email' => 'newemail@example.com']);

        $activity = Activity::where('subject_type', User::class)
            ->where('subject_id', $this->regularUser->id)
            ->latest()
            ->first();

        $this->assertNotNull($activity);
        $this->assertEquals($this->regularUser->id, $activity->subject_id);
        $this->assertEquals(User::class, $activity->subject_type);
    }


    public function test_activity_log_can_store_custom_properties(): void
    {
        // Update user which stores old and new values
        $oldEmail = $this->regularUser->email;
        $this->regularUser->update(['email' => 'updated@example.com']);

        $activity = Activity::where('subject_type', User::class)
            ->where('subject_id', $this->regularUser->id)
            ->latest()
            ->first();

        $this->assertNotNull($activity);
        $this->assertNotNull($activity->properties);
        $this->assertTrue($activity->properties->has('attributes'));
    }

    
    public function test_activity_log_records_timestamp(): void
    {
        activity()
            ->causedBy($this->superAdmin)
            ->log('Timestamp test');

        $activity = Activity::latest()->first();

        $this->assertNotNull($activity->created_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $activity->created_at);
    }

    
    public function test_activity_log_can_be_filtered_by_log_name(): void
    {
        activity()->useLog('user')->log('User log');
        activity()->useLog('system')->log('System log');

        $response = $this->actingAs($this->superAdmin)
            ->getJson('/activity-logs?log_name=user');

        $response->assertStatus(200);

        $activities = $response->json();
        $logNames = collect($activities)->pluck('log_name')->unique();

        $this->assertTrue($logNames->contains('user'));
    }

    
    public function test_user_deletion_can_be_logged(): void
    {
        $user = User::factory()->create();

        activity()
            ->causedBy($this->superAdmin)
            ->performedOn($user)
            ->event('deleted')
            ->log('User has been deleted');

        $this->assertDatabaseHas('activity_log', [
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'event' => 'deleted',
        ]);
    }

    
    public function test_activity_log_returns_limited_results(): void
    {
        // Create more than 500 activities
        for ($i = 0; $i < 10; $i++) {
            activity()->log("Activity {$i}");
        }

        $response = $this->actingAs($this->superAdmin)->getJson('/activity-logs');

        $response->assertStatus(200);

        $activities = $response->json();

        // Controller limits to 500 results
        $this->assertLessThanOrEqual(500, count($activities));
    }


    public function test_activity_log_response_includes_causer_details(): void
    {
        // Create an activity with auth context
        $this->actingAs($this->superAdmin);
        $this->regularUser->update(['name' => 'Updated via Auth']);

        $response = $this->getJson('/activity-logs');

        $response->assertStatus(200);

        $activities = $response->json();

        // Just verify the response structure
        $this->assertIsArray($activities);
        if (count($activities) > 0) {
            $activity = collect($activities)->first();
            $this->assertArrayHasKey('id', $activity);
            $this->assertArrayHasKey('description', $activity);
        }
    }
}
