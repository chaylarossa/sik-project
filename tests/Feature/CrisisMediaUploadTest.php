<?php

namespace Tests\Feature;

use App\Models\CrisisReport;
use App\Models\CrisisType;
use App\Models\Region;
use App\Models\UrgencyLevel;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CrisisMediaUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_upload_media_success(): void
    {
        $this->seed(RbacSeeder::class);

        Storage::fake('public');

        $creator = User::factory()->create();
        $report = CrisisReport::factory()->create([
            'created_by' => $creator->id,
            'crisis_type_id' => CrisisType::factory(),
            'urgency_level_id' => UrgencyLevel::factory(),
            'region_id' => Region::factory()->village()->create()->id,
        ]);

        $file = UploadedFile::fake()->image('photo.jpg');

        $response = $this->actingAs($creator)->post(route('reports.media.store', $report), [
            'files' => [$file],
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('crisis_media', [
            'crisis_report_id' => $report->id,
            'uploaded_by' => $creator->id,
            'original_name' => 'photo.jpg',
        ]);

        $files = Storage::disk('public')->files('crisis/'.$report->id);
        $this->assertNotEmpty($files);
    }

    public function test_upload_media_invalid_mime_fails(): void
    {
        $this->seed(RbacSeeder::class);

        $creator = User::factory()->create();
        $report = CrisisReport::factory()->create([
            'created_by' => $creator->id,
            'crisis_type_id' => CrisisType::factory(),
            'urgency_level_id' => UrgencyLevel::factory(),
            'region_id' => Region::factory()->village()->create()->id,
        ]);

        $file = UploadedFile::fake()->create('malware.exe', 5, 'application/x-msdownload');

        $response = $this->actingAs($creator)->post(route('reports.media.store', $report), [
            'files' => [$file],
        ]);

        $response->assertSessionHasErrors(['files.0']);
    }

    public function test_user_without_permission_cannot_upload_media(): void
    {
        $this->seed(RbacSeeder::class);

        $creator = User::factory()->create();
        $report = CrisisReport::factory()->create([
            'created_by' => $creator->id,
            'crisis_type_id' => CrisisType::factory(),
            'urgency_level_id' => UrgencyLevel::factory(),
            'region_id' => Region::factory()->village()->create()->id,
        ]);

        $otherUser = User::factory()->create();

        $file = UploadedFile::fake()->image('photo.jpg');

        $response = $this->actingAs($otherUser)->post(route('reports.media.store', $report), [
            'files' => [$file],
        ]);

        $response->assertForbidden();
    }
}
