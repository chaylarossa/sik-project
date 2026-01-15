<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JavaRegionSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $provinces = [
            [
                'code' => 'BNT',
                'name' => 'Banten',
                'cities' => [
                    [
                        'code' => 'SRG',
                        'name' => 'Kota Serang',
                        'districts' => [
                            [
                                'code' => 'CPC',
                                'name' => 'Kecamatan Cipocok Jaya',
                                'villages' => [
                                    ['code' => 'PNC', 'name' => 'Panancangan'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'code' => 'DKI',
                'name' => 'DKI Jakarta',
                'cities' => [
                    [
                        'code' => 'JKS',
                        'name' => 'Kota Administrasi Jakarta Selatan',
                        'districts' => [
                            [
                                'code' => 'KBY',
                                'name' => 'Kecamatan Kebayoran Baru',
                                'villages' => [
                                    ['code' => 'SLG', 'name' => 'Kelurahan Selong'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'code' => 'JBR',
                'name' => 'Jawa Barat',
                'cities' => [
                    [
                        'code' => 'BDG',
                        'name' => 'Kota Bandung',
                        'districts' => [
                            [
                                'code' => 'CBDG',
                                'name' => 'Kecamatan Coblong',
                                'villages' => [
                                    ['code' => 'DAGO', 'name' => 'Kelurahan Dago'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'code' => 'JTG',
                'name' => 'Jawa Tengah',
                'cities' => [
                    [
                        'code' => 'SMG',
                        'name' => 'Kota Semarang',
                        'districts' => [
                            [
                                'code' => 'BYK',
                                'name' => 'Kecamatan Banyumanik',
                                'villages' => [
                                    ['code' => 'PDK', 'name' => 'Kelurahan Pudakpayung'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'code' => 'DIY',
                'name' => 'DI Yogyakarta',
                'cities' => [
                    [
                        'code' => 'SLM',
                        'name' => 'Kabupaten Sleman',
                        'districts' => [
                            [
                                'code' => 'DPK',
                                'name' => 'Kecamatan Depok',
                                'villages' => [
                                    ['code' => 'CTT', 'name' => 'Kelurahan Caturtunggal'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'code' => 'JTM',
                'name' => 'Jawa Timur',
                'cities' => [
                    [
                        'code' => 'SBY',
                        'name' => 'Kota Surabaya',
                        'districts' => [
                            [
                                'code' => 'SKL',
                                'name' => 'Kecamatan Sukolilo',
                                'villages' => [
                                    ['code' => 'KPT', 'name' => 'Kelurahan Keputih'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($provinces as $provinceData) {
            $province = Region::firstOrCreate(
                ['code' => $provinceData['code']],
                [
                    'name' => $provinceData['name'],
                    'level' => Region::LEVEL_PROVINCE,
                    'parent_id' => null,
                ]
            );

            foreach ($provinceData['cities'] as $cityData) {
                $city = Region::firstOrCreate(
                    ['code' => $cityData['code']],
                    [
                        'name' => $cityData['name'],
                        'level' => Region::LEVEL_CITY,
                        'parent_id' => $province->id,
                    ]
                );

                foreach ($cityData['districts'] as $districtData) {
                    $district = Region::firstOrCreate(
                        ['code' => $districtData['code']],
                        [
                            'name' => $districtData['name'],
                            'level' => Region::LEVEL_DISTRICT,
                            'parent_id' => $city->id,
                        ]
                    );

                    foreach ($districtData['villages'] as $villageData) {
                        Region::firstOrCreate(
                            ['code' => $villageData['code']],
                            [
                                'name' => $villageData['name'],
                                'level' => Region::LEVEL_VILLAGE,
                                'parent_id' => $district->id,
                            ]
                        );
                    }
                }
            }
        }
    }
}
