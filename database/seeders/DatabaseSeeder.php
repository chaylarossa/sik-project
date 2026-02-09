<?php

namespace Database\Seeders;

use App\Enums\RoleName;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RbacSeeder::class,
            JavaRegionSeeder::class,
            UnitSeeder::class,
            SampleDataSeeder::class,
        ]);

        $users = [
            [
                'name' => 'Administrator',
                'email' => 'admin@example.com',
                'role' => RoleName::Administrator,
            ],
            [
                'name' => 'Operator Lapangan',
                'email' => 'operator@example.com',
                'role' => RoleName::OperatorLapangan,
            ],
            [
                'name' => 'Verifikator',
                'email' => 'verifikator@example.com',
                'role' => RoleName::Verifikator,
            ],
            [
                'name' => 'Pimpinan',
                'email' => 'pimpinan@example.com',
                'role' => RoleName::Pimpinan,
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );

            $user->assignRole($userData['role']->value);
        }
    }
}
