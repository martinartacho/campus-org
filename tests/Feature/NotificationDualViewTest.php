<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationDualViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_user_sees_table_view()
    {
        // Create admin user
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        // Create notification
        $notification = Notification::factory()->create([
            'title' => 'Test Admin Notification',
            'is_published' => true,
        ]);

        // Act as admin and visit notifications index
        $response = $this->actingAs($admin)->get(route('notifications.index'));

        // Assert response is successful
        $response->assertStatus(200);

        // Assert view receives isAdminView as true
        $response->assertViewHas('isAdminView', true);

        // Assert table partial is included (admin view)
        $response->assertSee('overflow-x-auto');
    }

    public function test_regular_user_sees_cards_view()
    {
        // Create regular user
        $user = User::factory()->create();
        $user->assignRole('teacher');

        // Create notification
        $notification = Notification::factory()->create([
            'title' => 'Test User Notification',
            'is_published' => true,
        ]);

        // Assign notification to user
        $notification->recipients()->attach($user->id);

        // Act as user and visit notifications index
        $response = $this->actingAs($user)->get(route('notifications.index'));

        // Assert response is successful
        $response->assertStatus(200);

        // Assert view receives isAdminView as false
        $response->assertViewHas('isAdminView', false);

        // Assert cards partial is included (user view)
        $response->assertSee('grid grid-cols-1');
    }

    public function test_isBackoffice_method_works_correctly()
    {
        // Test admin user
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->assertTrue($admin->isBackoffice());

        // Test teacher user
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        $this->assertFalse($teacher->isBackoffice());

        // Test director user
        $director = User::factory()->create();
        $director->assignRole('director');
        $this->assertTrue($director->isBackoffice());
    }

    public function test_regular_user_only_sees_published_notifications()
    {
        // Create regular user
        $user = User::factory()->create();
        $user->assignRole('teacher');

        // Create published and unpublished notifications
        $publishedNotification = Notification::factory()->create([
            'title' => 'Published Notification',
            'is_published' => true,
        ]);
        $unpublishedNotification = Notification::factory()->create([
            'title' => 'Unpublished Notification',
            'is_published' => false,
        ]);

        // Assign both notifications to user
        $publishedNotification->recipients()->attach($user->id);
        $unpublishedNotification->recipients()->attach($user->id);

        // Act as user and visit notifications index
        $response = $this->actingAs($user)->get(route('notifications.index'));

        // Assert only published notification is visible
        $response->assertSee('Published Notification');
        $response->assertDontSee('Unpublished Notification');
    }
}
