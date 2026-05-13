<?php

namespace Tests\Feature;

use App\Enums\ModerationStatus;
use App\Enums\NotificationStatus;
use App\Enums\ReportStatus;
use App\Enums\UserRole;
use App\Models\Claim;
use App\Models\Notification;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WebAdminPlatformTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_admin_can_login_and_open_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin->value,
            'password' => bcrypt('password123'),
        ]);

        $this->post('/admin/login', [
            'email' => $admin->email,
            'password' => 'password123',
        ])->assertRedirect(route('admin.dashboard'));

        $this->get('/admin')
            ->assertOk()
            ->assertSee('Platform Administrasi dan Moderasi', false)
            ->assertSee('Total laporan');
    }

    public function test_non_admin_user_cannot_login_to_admin_platform(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::User->value,
            'password' => bcrypt('password123'),
        ]);

        $this->post('/admin/login', [
            'email' => $user->email,
            'password' => 'password123',
        ])
            ->assertSessionHasErrors('email')
            ->assertRedirect();

        $this->assertGuest();
    }

    public function test_admin_can_approve_report_from_web_platform(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin->value]);
        $owner = User::factory()->create();
        $report = Report::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($admin)
            ->patch(route('admin.reports.approve', $report))
            ->assertRedirect();

        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'status' => ReportStatus::Approved->value,
            'moderation_status' => ModerationStatus::Approved->value,
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $owner->id,
            'report_id' => $report->id,
            'title' => 'Laporan disetujui',
        ]);
    }

    public function test_admin_can_update_report_image_from_web_platform(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['role' => UserRole::Admin->value]);
        $report = Report::factory()->approved()->create();

        $this->actingAs($admin)
            ->put(route('admin.reports.update', $report), [
                'title' => $report->title,
                'description' => $report->description,
                'report_type' => $report->report_type->value,
                'status' => $report->status->value,
                'image' => $this->fakePngUpload(),
            ])
            ->assertRedirect(route('admin.reports.show', $report));

        $report->refresh();

        $this->assertNotNull($report->image_path);
        Storage::disk('public')->assertExists($report->image_path);
    }

    public function test_admin_can_review_claim_and_mark_notification_read(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin->value]);
        $claim = Claim::factory()->create();
        $notification = Notification::factory()->create([
            'user_id' => $admin->id,
            'status' => NotificationStatus::Unread->value,
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.claims.reject', $claim))
            ->assertRedirect();

        $this->assertDatabaseHas('claims', [
            'id' => $claim->id,
            'status' => 'rejected',
            'reviewed_by' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.notifications.read', $notification))
            ->assertRedirect();

        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'status' => NotificationStatus::Read->value,
        ]);
    }

    public function test_admin_can_create_category_and_pwa_files_exist(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin->value]);

        $this->actingAs($admin)
            ->post(route('admin.categories.store'), [
                'name' => 'Vehicles',
                'slug' => '',
                'description' => 'Bicycles and vehicle accessories.',
                'status' => 'active',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('categories', [
            'name' => 'Vehicles',
            'slug' => 'vehicles',
            'status' => 'active',
        ]);

        $this->assertFileExists(public_path('manifest.json'));
        $this->assertFileExists(public_path('service-worker.js'));
        $this->assertFileExists(public_path('offline.html'));
    }

    private function fakePngUpload(): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'lost-found-web-upload-');

        file_put_contents(
            $path,
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAFgwJ/lx6L2QAAAABJRU5ErkJggg==')
        );

        return new UploadedFile($path, 'report.png', 'image/png', null, true);
    }
}
