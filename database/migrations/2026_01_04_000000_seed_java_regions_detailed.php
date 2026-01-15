<?php

use App\Models\Region;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;

return new class extends Migration
{
    public function up(): void
    {
        $now = Date::now();

        $data = [
            'DKI' => [
                'name' => 'DKI Jakarta',
                'cities' => [
                    'JKP' => [
                        'name' => 'Jakarta Pusat',
                        'districts' => [
                            'GMB' => ['name' => 'Gambir', 'villages' => [
                                'GMB1' => 'Gambir',
                                'GMB2' => 'Cideng',
                                'GMB3' => 'Petojo Utara',
                                'GMB4' => 'Petojo Selatan',
                                'GMB5' => 'Kebon Kelapa',
                            ]],
                            'TNA' => ['name' => 'Tanah Abang', 'villages' => [
                                'TNA1' => 'Bendungan Hilir',
                                'TNA2' => 'Karet Tengsin',
                                'TNA3' => 'Kebon Melati',
                                'TNA4' => 'Kebon Kacang',
                                'TNA5' => 'Petamburan',
                            ]],
                            'MNT' => ['name' => 'Menteng', 'villages' => [
                                'MNT1' => 'Menteng',
                                'MNT2' => 'Pegangsaan',
                                'MNT3' => 'Cikini',
                                'MNT4' => 'Gondangdia',
                                'MNT5' => 'Kebon Sirih',
                            ]],
                            'SNN' => ['name' => 'Senen', 'villages' => [
                                'SNN1' => 'Senen',
                                'SNN2' => 'Kwitang',
                                'SNN3' => 'Kenari',
                                'SNN4' => 'Bungur',
                                'SNN5' => 'Paseban',
                            ]],
                            'CPH' => ['name' => 'Cempaka Putih', 'villages' => [
                                'CPH1' => 'Cempaka Putih Barat',
                                'CPH2' => 'Cempaka Putih Timur',
                                'CPH3' => 'Rawasari',
                                'CPH4' => 'Johar Baru',
                                'CPH5' => 'Galur',
                            ]],
                        ],
                    ],
                ],
            ],
            'JBR' => [
                'name' => 'Jawa Barat',
                'cities' => [
                    'BDG' => [
                        'name' => 'Kota Bandung',
                        'districts' => [
                            'AND' => ['name' => 'Andir', 'villages' => [
                                'AND1' => 'Dunguscariang',
                                'AND2' => 'Garuda',
                                'AND3' => 'Maleber',
                                'AND4' => 'Campaka',
                                'AND5' => 'Ciroyom',
                            ]],
                            'CCD' => ['name' => 'Cicendo', 'villages' => [
                                'CCD1' => 'Pasirkaliki',
                                'CCD2' => 'Pajajaran',
                                'CCD3' => 'Sukaraja',
                                'CCD4' => 'Arjuna',
                                'CCD5' => 'Husen Sastranegara',
                            ]],
                            'CBL' => ['name' => 'Coblong', 'villages' => [
                                'CBL1' => 'Dago',
                                'CBL2' => 'Lebak Gede',
                                'CBL3' => 'Sadang Serang',
                                'CBL4' => 'Sekeloa',
                                'CBL5' => 'Cipaganti',
                            ]],
                            'LGK' => ['name' => 'Lengkong', 'villages' => [
                                'LGK1' => 'Burangrang',
                                'LGK2' => 'Cijagra',
                                'LGK3' => 'Lingkar Selatan',
                                'LGK4' => 'Malabar',
                                'LGK5' => 'Turangga',
                            ]],
                            'SKJ' => ['name' => 'Sukajadi', 'villages' => [
                                'SKJ1' => 'Pasteur',
                                'SKJ2' => 'Cipedes',
                                'SKJ3' => 'Sukabungah',
                                'SKJ4' => 'Sukagalih',
                                'SKJ5' => 'Sukarasa',
                            ]],
                        ],
                    ],
                ],
            ],
            'JTG' => [
                'name' => 'Jawa Tengah',
                'cities' => [
                    'SMG' => [
                        'name' => 'Kota Semarang',
                        'districts' => [
                            'SMT' => ['name' => 'Semarang Tengah', 'villages' => [
                                'SMT1' => 'Jagalan',
                                'SMT2' => 'Kauman',
                                'SMT3' => 'Kranggan',
                                'SMT4' => 'Pandansari',
                                'SMT5' => 'Purwodinatan',
                            ]],
                            'SMB' => ['name' => 'Semarang Barat', 'villages' => [
                                'SMB1' => 'Kembangarum',
                                'SMB2' => 'Manyaran',
                                'SMB3' => 'Ngemplak Simongan',
                                'SMB4' => 'Bojongsalaman',
                                'SMB5' => 'Salamanmloyo',
                            ]],
                            'SME' => ['name' => 'Semarang Timur', 'villages' => [
                                'SME1' => 'Karangturi',
                                'SME2' => 'Karangtempel',
                                'SME3' => 'Mlatiharjo',
                                'SME4' => 'Rejomulyo',
                                'SME5' => 'Sarirejo',
                            ]],
                            'BYM' => ['name' => 'Banyumanik', 'villages' => [
                                'BYM1' => 'Padangsari',
                                'BYM2' => 'Pedalangan',
                                'BYM3' => 'Srondol Kulon',
                                'BYM4' => 'Srondol Wetan',
                                'BYM5' => 'Ngesrep',
                            ]],
                            'TBL' => ['name' => 'Tembalang', 'villages' => [
                                'TBL1' => 'Bulusan',
                                'TBL2' => 'Meteseh',
                                'TBL3' => 'Rowosari',
                                'TBL4' => 'Sendangmulyo',
                                'TBL5' => 'Tandang',
                            ]],
                        ],
                    ],
                ],
            ],
            'DIY' => [
                'name' => 'DI Yogyakarta',
                'cities' => [
                    'SLM' => [
                        'name' => 'Kabupaten Sleman',
                        'districts' => [
                            'DPK' => ['name' => 'Depok', 'villages' => [
                                'DPK1' => 'Condongcatur',
                                'DPK2' => 'Maguwoharjo',
                                'DPK3' => 'Caturtunggal',
                                'DPK4' => 'Demangan',
                                'DPK5' => 'Babarsari',
                            ]],
                            'MLT' => ['name' => 'Mlati', 'villages' => [
                                'MLT1' => 'Sinduadi',
                                'MLT2' => 'Sendangadi',
                                'MLT3' => 'Tlogoadi',
                                'MLT4' => 'Tirtoadi',
                                'MLT5' => 'Sumberadi',
                            ]],
                            'GMP' => ['name' => 'Gamping', 'villages' => [
                                'GMP1' => 'Ambarketawang',
                                'GMP2' => 'Balecatur',
                                'GMP3' => 'Banyuraden',
                                'GMP4' => 'Nogotirto',
                                'GMP5' => 'Trihanggo',
                            ]],
                            'KLS' => ['name' => 'Kalasan', 'villages' => [
                                'KLS1' => 'Purwomartani',
                                'KLS2' => 'Selomartani',
                                'KLS3' => 'Tamanmartani',
                                'KLS4' => 'Tirtomartani',
                                'KLS5' => 'Bokoharjo',
                            ]],
                            'NGL' => ['name' => 'Ngaglik', 'villages' => [
                                'NGL1' => 'Sardonoharjo',
                                'NGL2' => 'Sinduharjo',
                                'NGL3' => 'Sukoharjo',
                                'NGL4' => 'Minomartani',
                                'NGL5' => 'Donoharjo',
                            ]],
                        ],
                    ],
                ],
            ],
            'JTM' => [
                'name' => 'Jawa Timur',
                'cities' => [
                    'SBY' => [
                        'name' => 'Kota Surabaya',
                        'districts' => [
                            'GNT' => ['name' => 'Genteng', 'villages' => [
                                'GNT1' => 'Genteng',
                                'GNT2' => 'Kapasari',
                                'GNT3' => 'Ketabang',
                                'GNT4' => 'Peneleh',
                                'GNT5' => 'Embong Kaliasin',
                            ]],
                            'TGS' => ['name' => 'Tegalsari', 'villages' => [
                                'TGS1' => 'Wonorejo',
                                'TGS2' => 'Dr. Sutomo',
                                'TGS3' => 'Kedungdoro',
                                'TGS4' => 'Keputran',
                                'TGS5' => 'Tegalsari',
                            ]],
                            'WNK' => ['name' => 'Wonokromo', 'villages' => [
                                'WNK1' => 'Sawunggaling',
                                'WNK2' => 'Darmo',
                                'WNK3' => 'Ngagel',
                                'WNK4' => 'Jagir',
                                'WNK5' => 'Wonokromo',
                            ]],
                            'RGK' => ['name' => 'Rungkut', 'villages' => [
                                'RGK1' => 'Rungkut Kidul',
                                'RGK2' => 'Rungkut Menanggal',
                                'RGK3' => 'Medokan Ayu',
                                'RGK4' => 'Penjaringan Sari',
                                'RGK5' => 'Kedung Baruk',
                            ]],
                            'SKL' => ['name' => 'Sukolilo', 'villages' => [
                                'SKL1' => 'Keputih',
                                'SKL2' => 'Gebang Putih',
                                'SKL3' => 'Menur Pumpungan',
                                'SKL4' => 'Semolowaru',
                                'SKL5' => 'Klampis Ngasem',
                            ]],
                        ],
                    ],
                ],
            ],
            'BNT' => [
                'name' => 'Banten',
                'cities' => [
                    'TNG' => [
                        'name' => 'Kota Tangerang',
                        'districts' => [
                            'TNGD' => ['name' => 'Tangerang', 'villages' => [
                                'TNGD1' => 'Sukasari',
                                'TNGD2' => 'Tanah Tinggi',
                                'TNGD3' => 'Babakan',
                                'TNGD4' => 'Bugel',
                                'TNGD5' => 'Gerendeng',
                            ]],
                            'CPD' => ['name' => 'Cipondoh', 'villages' => [
                                'CPD1' => 'Ketapang',
                                'CPD2' => 'Kenanga',
                                'CPD3' => 'Gondrong',
                                'CPD4' => 'Petir',
                                'CPD5' => 'Poris Plawad',
                            ]],
                            'KRW' => ['name' => 'Karawaci', 'villages' => [
                                'KRW1' => 'Karawaci Baru',
                                'KRW2' => 'Bugel',
                                'KRW3' => 'Nambo Jaya',
                                'KRW4' => 'Pabuaran',
                                'KRW5' => 'Cimone',
                            ]],
                            'CLD' => ['name' => 'Ciledug', 'villages' => [
                                'CLD1' => 'Paninggilan',
                                'CLD2' => 'Paninggilan Utara',
                                'CLD3' => 'Sudimara Barat',
                                'CLD4' => 'Sudimara Selatan',
                                'CLD5' => 'Sudimara Timur',
                            ]],
                            'JTW' => ['name' => 'Jatiuwung', 'villages' => [
                                'JTW1' => 'Alam Jaya',
                                'JTW2' => 'Gandasari',
                                'JTW3' => 'Jatake',
                                'JTW4' => 'Keroncong',
                                'JTW5' => 'Manis Jaya',
                            ]],
                        ],
                    ],
                ],
            ],
        ];

        DB::transaction(function () use ($data, $now) {
            foreach ($data as $provinceCode => $provinceData) {
                $province = Region::query()->updateOrCreate(
                    ['code' => $provinceCode],
                    [
                        'name' => $provinceData['name'],
                        'level' => Region::LEVEL_PROVINCE,
                        'parent_id' => null,
                        'updated_at' => $now,
                        'created_at' => $now,
                    ]
                );

                foreach ($provinceData['cities'] as $cityCode => $cityData) {
                    $city = Region::query()->updateOrCreate(
                        ['code' => $cityCode],
                        [
                            'name' => $cityData['name'],
                            'level' => Region::LEVEL_CITY,
                            'parent_id' => $province->id,
                            'updated_at' => $now,
                            'created_at' => $now,
                        ]
                    );

                    foreach ($cityData['districts'] as $districtCode => $districtData) {
                        $district = Region::query()->updateOrCreate(
                            ['code' => $districtCode],
                            [
                                'name' => $districtData['name'],
                                'level' => Region::LEVEL_DISTRICT,
                                'parent_id' => $city->id,
                                'updated_at' => $now,
                                'created_at' => $now,
                            ]
                        );

                        foreach ($districtData['villages'] as $villageCode => $villageName) {
                            Region::query()->updateOrCreate(
                                ['code' => $villageCode],
                                [
                                    'name' => $villageName,
                                    'level' => Region::LEVEL_VILLAGE,
                                    'parent_id' => $district->id,
                                    'updated_at' => $now,
                                    'created_at' => $now,
                                ]
                            );
                        }
                    }
                }
            }
        });
    }

    public function down(): void
    {
        $codes = [
            // provinces
            'BNT','DKI','JBR','JTG','DIY','JTM',
            // cities
            'JKP','BDG','SMG','SLM','SBY','TNG',
            // districts (partial list from data)
            'GMB','TNA','MNT','SNN','CPH','AND','CCD','CBL','LGK','SKJ','SMT','SMB','SME','BYM','TBL','DPK','MLT','GMP','KLS','NGL','GNT','TGS','WNK','RGK','SKL','TNGD','CPD','KRW','CLD','JTW',
            // villages codes all
            'GMB1','GMB2','GMB3','GMB4','GMB5','TNA1','TNA2','TNA3','TNA4','TNA5','MNT1','MNT2','MNT3','MNT4','MNT5','SNN1','SNN2','SNN3','SNN4','SNN5','CPH1','CPH2','CPH3','CPH4','CPH5',
            'AND1','AND2','AND3','AND4','AND5','CCD1','CCD2','CCD3','CCD4','CCD5','CBL1','CBL2','CBL3','CBL4','CBL5','LGK1','LGK2','LGK3','LGK4','LGK5','SKJ1','SKJ2','SKJ3','SKJ4','SKJ5',
            'SMT1','SMT2','SMT3','SMT4','SMT5','SMB1','SMB2','SMB3','SMB4','SMB5','SME1','SME2','SME3','SME4','SME5','BYM1','BYM2','BYM3','BYM4','BYM5','TBL1','TBL2','TBL3','TBL4','TBL5',
            'DPK1','DPK2','DPK3','DPK4','DPK5','MLT1','MLT2','MLT3','MLT4','MLT5','GMP1','GMP2','GMP3','GMP4','GMP5','KLS1','KLS2','KLS3','KLS4','KLS5','NGL1','NGL2','NGL3','NGL4','NGL5',
            'GNT1','GNT2','GNT3','GNT4','GNT5','TGS1','TGS2','TGS3','TGS4','TGS5','WNK1','WNK2','WNK3','WNK4','WNK5','RGK1','RGK2','RGK3','RGK4','RGK5','SKL1','SKL2','SKL3','SKL4','SKL5',
            'TNGD1','TNGD2','TNGD3','TNGD4','TNGD5','CPD1','CPD2','CPD3','CPD4','CPD5','KRW1','KRW2','KRW3','KRW4','KRW5','CLD1','CLD2','CLD3','CLD4','CLD5','JTW1','JTW2','JTW3','JTW4','JTW5',
        ];

        DB::table('regions')->whereIn('code', $codes)->delete();
    }
};
