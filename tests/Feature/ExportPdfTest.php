<?php

namespace Tests\Feature;

use App\Enums\PermissionName;
use App\Models\CrisisReport;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ExportPdfTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Ensure perm exists
        Permission::firstOrCreate(['name' => PermissionName::ExportData->value, 'guard_name' => 'web']);
    }

    public function test_user_can_download_pdf_with_permission()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(PermissionName::ExportData->value);

        // Create some reports to populate PDF
        CrisisReport::factory()->count(3)->create();

        $response = $this->actingAs($user)
            ->get(route('archive.export.pdf'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_user_cannot_download_pdf_without_permission()
    {
        $user = User::factory()->create();
        // No permission

        $response = $this->actingAs($user)
            ->get(route('archive.export.pdf'));

        $response->assertStatus(403);
    }

    public function test_filter_affects_pdf_output_data()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(PermissionName::ExportData->value);

        // We use string literals to be safe if constants aren't autoloaded or public
        $reportNew = CrisisReport::factory()->create(['status' => 'new']);
        $reportDone = CrisisReport::factory()->create(['status' => 'done']);

        // Mock PDF to capture View data
        Pdf::shouldReceive('loadView')
            ->once()
            ->withArgs(function ($view, $data) use ($reportNew, $reportDone) {
                if ($view !== 'exports.archive-pdf') return false;
                
                $reports = $data['reports'];
                
                // Expect only 'new' report in the collection
                if ($reports->count() !== 1) return false;
                if (!$reports->contains($reportNew)) return false;
                if ($reports->contains($reportDone)) return false;
                
                return true;
            })
            ->andReturnSelf();

        Pdf::shouldReceive('setPaper')->andReturnSelf();
        Pdf::shouldReceive('download')->andReturn(response('mock-pdf'));

        $response = $this->actingAs($user)
            ->get(route('archive.export.pdf', ['status' => 'new']));
            
        $response->assertStatus(200);
    }
}
