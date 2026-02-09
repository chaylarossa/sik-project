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
            ['name' => 'BPBD', 'description' => 'Badan Penanggulangan Bencana Daerah'],
            ['name' => 'Damkar', 'description' => 'Pemadam Kebakaran'],
            ['name' => 'PMI', 'description' => 'Palang Merah Indonesia'],
            ['name' => 'Polisi', 'description' => 'Kepolisian Republik Indonesia'],
            ['name' => 'Dinkes', 'description' => 'Dinas Kesehatan'],
            ['name' => 'Tagana', 'description' => 'Taruna Siaga Bencana'],
            ['name' => 'Basarnas', 'description' => 'Badan Nasional Pencarian dan Pertolongan'],
        ];

        foreach ($units as $unit) {
            Unit::firstOrCreate(
                ['name' => $unit['name']],
                ['description' => $unit['description'], 'is_active' => true]
            );
        }
    }
}
