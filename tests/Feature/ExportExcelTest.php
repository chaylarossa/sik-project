<?php

namespace Tests\Feature;

use App\Enums\PermissionName;
use App\Enums\RoleName;
use App\Exports\CrisisArchiveExport;
use App\Models\CrisisReport;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class ExportExcelTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_user_can_download_excel(): void
    {
        Excel::fake();
        $this->seed(RbacSeeder::class);

        $user = User::factory()->create();
        $user->assignRole(RoleName::Administrator->value);

        $response = $this->actingAs($user)->get('/archive/export/excel');

        $response->assertOk();
        Excel::assertDownloaded('arsip-laporan.xlsx', fn (CrisisArchiveExport $export) => true);
    }

    public function test_user_without_permission_gets_forbidden(): void
    {
        $this->seed(RbacSeeder::class);

        $user = User::factory()->create();
        $user->assignRole(RoleName::Verifikator->value);

        $response = $this->actingAs($user)->get('/archive/export/excel');

        $response->assertForbidden();
    }

    public function test_filter_applied_on_export(): void
    {
        Excel::fake();
        $this->seed(RbacSeeder::class);

        $user = User::factory()->create();
        $user->assignRole(RoleName::Administrator->value);

        $matching = CrisisReport::factory()->count(2)->create([
            'verification_status' => 'verified',
        ]);
        CrisisReport::factory()->create([
            'verification_status' => 'pending',
        ]);

        $response = $this->actingAs($user)->get('/archive/export/excel?verification_status=verified');

        $response->assertOk();
        Excel::assertDownloaded('arsip-laporan.xlsx', function (CrisisArchiveExport $export) use ($matching) {
            $rows = $export->collection();
            return $rows->count() === $matching->count()
                && $rows->every(fn ($row) => $row->verification_status === 'verified');
        });
    }
}
