use App\Models\Unit;
$units = [
    ['name' => 'BPBD', 'description' => 'Badan Penanggulangan Bencana Daerah', 'is_active' => true],
    ['name' => 'Damkar', 'description' => 'Pemadam Kebakaran', 'is_active' => true],
    ['name' => 'PMI', 'description' => 'Palang Merah Indonesia', 'is_active' => true],
    ['name' => 'Polisi', 'description' => 'Kepolisian Republik Indonesia', 'is_active' => true],
    ['name' => 'Dinkes', 'description' => 'Dinas Kesehatan', 'is_active' => true],
    ['name' => 'Tagana', 'description' => 'Taruna Siaga Bencana', 'is_active' => true],
    ['name' => 'Basarnas', 'description' => 'Badan Nasional Pencarian dan Pertolongan', 'is_active' => true],
];
foreach ($units as $unit) {
    Unit::firstOrCreate(['name' => $unit['name']], $unit);
    echo "Unit {$unit['name']} ensured.\n";
}
exit();
