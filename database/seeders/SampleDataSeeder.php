<?php

namespace Database\Seeders;

use App\Models\CrisisReport;
use App\Models\CrisisType;
use App\Models\Region;
use App\Models\UrgencyLevel;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        if (CrisisReport::count() > 0) {
            return; // already seeded
        }

        $crisisTypes = [
            ['code' => 'BAN', 'name' => 'Banjir'],
            ['code' => 'LGS', 'name' => 'Tanah Longsor'],
            ['code' => 'KEB', 'name' => 'Kebakaran'],
        ];

        foreach ($crisisTypes as $type) {
            CrisisType::firstOrCreate(
                ['code' => $type['code']],
                ['name' => $type['name'], 'is_active' => true]
            );
        }

        $urgencyLevels = [
            ['name' => 'Rendah', 'level' => 1, 'color' => '#0ea5e9', 'is_high_priority' => false],
            ['name' => 'Sedang', 'level' => 2, 'color' => '#f59e0b', 'is_high_priority' => true],
            ['name' => 'Tinggi', 'level' => 3, 'color' => '#ef4444', 'is_high_priority' => true],
        ];

        foreach ($urgencyLevels as $urgency) {
            UrgencyLevel::firstOrCreate(
                ['level' => $urgency['level']],
                Arr::except($urgency, ['level'])
            );
        }

        $creator = User::where('email', 'admin@example.com')->first() ?? User::first();
        if (!$creator) {
            return;
        }

        $districts = Region::query()
            ->where('level', Region::LEVEL_DISTRICT)
            ->take(5)
            ->get();

        if ($districts->isEmpty()) {
            $districts = Region::query()->take(5)->get();
        }

        $statuses = [
            CrisisReport::STATUS_NEW,
            CrisisReport::STATUS_NEW,
            CrisisReport::STATUS_IN_PROGRESS,
            CrisisReport::STATUS_DONE,
            CrisisReport::STATUS_CLOSED,
        ];

        foreach ($districts as $index => $district) {
            $crisisType = CrisisType::inRandomOrder()->first();
            $urgencyLevel = UrgencyLevel::orderBy('level')->skip($index % 3)->first();

            CrisisReport::create([
                'crisis_type_id' => $crisisType->id,
                'urgency_level_id' => $urgencyLevel->id,
                'region_id' => $district->id,
                'created_by' => $creator->id,
                'status' => $statuses[$index % count($statuses)],
                'occurred_at' => now()->subDays($index + 1),
                'address' => 'Alamat kejadian '.$district->name,
                'latitude' => -6.2 - $index * 0.01,
                'longitude' => 106.8 + $index * 0.01,
                'description' => 'Laporan krisis contoh untuk verifikasi dan pengujian.',
            ]);
        }
    }
}