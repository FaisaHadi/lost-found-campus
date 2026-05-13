<?php

namespace Tests\Feature;

use App\Enums\ClaimStatus;
use App\Enums\ModerationStatus;
use App\Enums\NotificationStatus;
use App\Enums\ReportStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Notification;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class Phase2ApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_report_with_image_and_admin_can_approve_it(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $admin = User::factory()->create(['role' => UserRole::Admin->value]);
        $category = Category::factory()->create(['name' => 'Electronics', 'slug' => 'electronics']);

        Sanctum::actingAs($user);

        $response = $this->post('/api/v1/reports', [
            'category_id' => $category->id,
            'title' => 'Lost phone near library',
            'description' => 'Black phone with a cracked case near the main library entrance.',
            'report_type' => 'lost',
            'image' => $this->fakePngUpload(),
            'latitude' => -6.2000000,
            'longitude' => 106.8166660,
            'location_text' => 'Main library entrance',
        ], ['Accept' => 'application/json']);

        $response
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', ReportStatus::Pending->value)
            ->assertJsonPath('data.moderation_status', ModerationStatus::Pending->value);

        $report = Report::firstOrFail();
        Storage::disk('public')->assertExists($report->image_path);

        Sanctum::actingAs($admin);

        $this->patchJson("/api/v1/admin/reports/{$report->id}/approve")
            ->assertOk()
            ->assertJsonPath('data.status', ReportStatus::Approved->value)
            ->assertJsonPath('data.moderation_status', ModerationStatus::Approved->value);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $user->id,
            'report_id' => $report->id,
            'title' => 'Laporan disetujui',
            'status' => NotificationStatus::Unread->value,
        ]);
    }

    public function test_report_listing_supports_keyword_category_type_and_status_filters(): void
    {
        $user = User::factory()->create();
        $electronics = Category::factory()->create(['slug' => 'electronics']);
        $documents = Category::factory()->create(['slug' => 'documents']);

        Report::factory()->approved()->create([
            'user_id' => $user->id,
            'category_id' => $electronics->id,
            'title' => 'Blue laptop charger',
            'report_type' => 'found',
        ]);

        Report::factory()->approved()->create([
            'user_id' => $user->id,
            'category_id' => $documents->id,
            'title' => 'Student card',
            'report_type' => 'found',
        ]);

        Sanctum::actingAs(User::factory()->create());

        $this->getJson('/api/v1/reports?keyword=laptop&category_slug=electronics&report_type=found&status=approved')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Blue laptop charger');
    }

    public function test_user_cannot_view_or_update_another_users_pending_report(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $report = Report::factory()->create(['user_id' => $owner->id]);

        Sanctum::actingAs($otherUser);

        $this->getJson("/api/v1/reports/{$report->id}")
            ->assertForbidden()
            ->assertJsonPath('error.code', 'FORBIDDEN');

        $this->putJson("/api/v1/reports/{$report->id}", [
            'title' => 'Changed title',
        ])->assertForbidden();
    }

    public function test_category_crud_is_admin_only(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/v1/categories', [
            'name' => 'Vehicles',
        ])->assertForbidden();

        Sanctum::actingAs(User::factory()->create(['role' => UserRole::Admin->value]));

        $categoryId = $this->postJson('/api/v1/categories', [
            'name' => 'Vehicles',
            'description' => 'Bicycles, helmets, and vehicle accessories.',
        ])
            ->assertCreated()
            ->assertJsonPath('data.slug', 'vehicles')
            ->json('data.id');

        $this->putJson("/api/v1/categories/{$categoryId}", [
            'status' => 'inactive',
        ])->assertOk()->assertJsonPath('data.status', 'inactive');
    }

    public function test_claim_lifecycle_rejects_own_report_and_updates_report_when_approved(): void
    {
        $owner = User::factory()->create();
        $claimant = User::factory()->create();
        $admin = User::factory()->create(['role' => UserRole::Admin->value]);
        $report = Report::factory()->approved()->create(['user_id' => $owner->id]);

        Sanctum::actingAs($owner);

        $this->postJson('/api/v1/claims', [
            'report_id' => $report->id,
            'proof_text' => 'I can identify the item and describe unique details.',
        ])->assertUnprocessable();

        Sanctum::actingAs($claimant);

        $claimId = $this->postJson('/api/v1/claims', [
            'report_id' => $report->id,
            'proof_text' => 'I can identify the item and describe unique details.',
        ])
            ->assertCreated()
            ->assertJsonPath('data.status', ClaimStatus::Pending->value)
            ->json('data.id');

        Sanctum::actingAs($admin);

        $this->patchJson("/api/v1/admin/claims/{$claimId}/approve")
            ->assertOk()
            ->assertJsonPath('data.status', ClaimStatus::Approved->value);

        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'status' => ReportStatus::Claimed->value,
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $claimant->id,
            'claim_id' => $claimId,
            'title' => 'Klaim disetujui',
            'status' => NotificationStatus::Unread->value,
        ]);
    }

    public function test_user_can_mark_own_notification_as_read(): void
    {
        $user = User::factory()->create();
        $notification = Notification::factory()->create(['user_id' => $user->id]);
        $otherNotification = Notification::factory()->create();

        Sanctum::actingAs($user);

        $this->patchJson("/api/v1/notifications/{$notification->id}/read")
            ->assertOk()
            ->assertJsonPath('data.status', NotificationStatus::Read->value);

        $this->patchJson("/api/v1/notifications/{$otherNotification->id}/read")
            ->assertForbidden();
    }

    private function fakePngUpload(): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'lost-found-upload-');

        file_put_contents(
            $path,
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAFgwJ/lx6L2QAAAABJRU5ErkJggg==')
        );

        return new UploadedFile($path, 'phone.png', 'image/png', null, true);
    }
}
