<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $province = Region::firstOrCreate(
            ['code' => 'JBR'],
            [
                'name' => 'Jawa Barat',
                'level' => Region::LEVEL_PROVINCE,
                'parent_id' => null,
            ]
        );

        $city = Region::firstOrCreate(
            ['code' => 'BDG'],
            [
                'name' => 'Kota Bandung',
                'level' => Region::LEVEL_CITY,
                'parent_id' => $province->id,
            ]
        );

        $district = Region::firstOrCreate(
            ['code' => 'CBDG'],
            [
                'name' => 'Kecamatan Coblong',
                'level' => Region::LEVEL_DISTRICT,
                'parent_id' => $city->id,
            ]
        );

        Region::firstOrCreate(
            ['code' => 'DAGO'],
            [
                'name' => 'Kelurahan Dago',
                'level' => Region::LEVEL_VILLAGE,
                'parent_id' => $district->id,
            ]
        );
    }
}
