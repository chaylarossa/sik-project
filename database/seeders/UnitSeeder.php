<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['name' => 'BPBD', 'code' => 'BPBD', 'description' => 'Badan Penanggulangan Bencana Daerah', 'contact_phone' => '021-000000'],
            ['name' => 'Damkar', 'code' => 'DAMKAR', 'description' => 'Pemadam Kebakaran', 'contact_phone' => '021-000001'],
            ['name' => 'PMI', 'code' => 'PMI', 'description' => 'Palang Merah Indonesia', 'contact_phone' => '021-000002'],
            ['name' => 'Polisi', 'code' => 'POLRI', 'description' => 'Kepolisian Republik Indonesia', 'contact_phone' => '021-000003'],
            ['name' => 'Dinkes', 'code' => 'DINKES', 'description' => 'Dinas Kesehatan', 'contact_phone' => '021-000004'],
            ['name' => 'Tagana', 'code' => 'TAGANA', 'description' => 'Taruna Siaga Bencana', 'contact_phone' => '021-000005'],
            ['name' => 'Basarnas', 'code' => 'BASARNAS', 'description' => 'Badan Nasional Pencarian dan Pertolongan', 'contact_phone' => '021-000006'],
        ];

        foreach ($units as $unit) {
            Unit::firstOrCreate(
                ['code' => $unit['code']],
                [
                    'name' => $unit['name'],
                    'description' => $unit['description'],
                    'contact_phone' => $unit['contact_phone'],
                    'is_active' => true,
                ]
            );
        }
    }
}
