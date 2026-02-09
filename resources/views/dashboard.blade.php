@php
    $statusStyles = [
        'new' => ['label' => 'Baru', 'color' => 'bg-indigo-100 text-indigo-700'],
        'in_progress' => ['label' => 'Proses', 'color' => 'bg-amber-100 text-amber-700'],
        'done' => ['label' => 'Selesai', 'color' => 'bg-emerald-100 text-emerald-700'],
        'closed' => ['label' => 'Ditutup', 'color' => 'bg-gray-100 text-gray-700'],
    ];
@endphp

<x-app-layout>
    <x-slot name="header">Dashboard</x-slot>
    <x-slot name="breadcrumb">Beranda / Dashboard</x-slot>

    <div class="space-y-6">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border border-indigo-100 bg-white p-5 shadow-sm">
                <div class="text-sm font-medium text-gray-500">Aktif</div>
                <div class="mt-2 flex items-end justify-between">
                    <div class="text-3xl font-semibold text-gray-900">{{ $cards['active'] ?? 0 }}</div>
                    <span class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700">New + Proses</span>
                </div>
            </div>
            <div class="rounded-xl border border-indigo-100 bg-white p-5 shadow-sm">
                <div class="text-sm font-medium text-gray-500">Menunggu Verifikasi</div>
                <div class="mt-2 flex items-end justify-between">
                    <div class="text-3xl font-semibold text-gray-900">{{ $cards['pending_verification'] ?? 0 }}</div>
                    <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-700">Pending</span>
                </div>
            </div>
            <div class="rounded-xl border border-indigo-100 bg-white p-5 shadow-sm">
                <div class="text-sm font-medium text-gray-500">Sedang Ditangani</div>
                <div class="mt-2 flex items-end justify-between">
                    <div class="text-3xl font-semibold text-gray-900">{{ $cards['in_progress'] ?? 0 }}</div>
                    <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">Proses</span>
                </div>
            </div>
            <div class="rounded-xl border border-indigo-100 bg-white p-5 shadow-sm">
                <div class="text-sm font-medium text-gray-500">Selesai / Ditutup</div>
                <div class="mt-2 flex items-end justify-between">
                    <div class="text-3xl font-semibold text-gray-900">{{ $cards['done_closed'] ?? 0 }}</div>
                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Clear</span>
                </div>
            </div>
        </div>

        <div class="grid gap-3 lg:grid-cols-2">
            <div class="rounded-xl border border-indigo-100 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-900">Krisis per Jenis</h3>
                </div>
                <div class="mt-3 rounded-lg bg-indigo-50/50 p-2">
                    <canvas id="typeChart" class="h-44"></canvas>
                </div>
            </div>
            <div class="rounded-xl border border-indigo-100 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-900">Tren Harian</h3>
                    <div class="flex items-center gap-1 text-xs">
                        <button type="button" data-range="7" class="trend-toggle rounded-lg bg-indigo-600 px-2 py-1 font-semibold text-white shadow-sm hover:bg-indigo-700">7H</button>
                        <button type="button" data-range="30" class="trend-toggle rounded-lg bg-white px-2 py-1 font-semibold text-indigo-700 ring-1 ring-indigo-200 hover:bg-indigo-50">30H</button>
                    </div>
                </div>
                <div class="mt-3 rounded-lg bg-indigo-50/50 p-2">
                    <canvas id="trendChart" class="h-44"></canvas>
                </div>
            </div>
        </div>

        @if($roleView === 'admin')
            <div class="rounded-xl border border-indigo-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Laporan Terbaru</h3>
                        <p class="text-sm text-gray-500">Seluruh status untuk pemantauan cepat.</p>
                    </div>
                </div>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-left font-semibold text-gray-600">
                            <tr>
                                <th class="px-4 py-3">Jenis</th>
                                <th class="px-4 py-3">Urgensi</th>
                                <th class="px-4 py-3">Wilayah</th>
                                <th class="px-4 py-3">Waktu</th>
                                <th class="px-4 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($recentReports as $report)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $report->crisisType->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3">{{ $report->urgencyLevel->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $report->region->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ optional($report->occurred_at)->format('d M Y H:i') }}</td>
                                    <td class="px-4 py-3">
                                        @php($style = $statusStyles[$report->status] ?? $statusStyles['closed'])
                                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $style['color'] }}">{{ $style['label'] }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-4 text-center text-gray-500">Belum ada data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @elseif($roleView === 'operator')
            <div class="rounded-xl border border-indigo-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Penugasan & Progres</h3>
                        <p class="text-sm text-gray-500">Fokus pada laporan yang sudah diverifikasi.</p>
                    </div>
                </div>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-left font-semibold text-gray-600">
                            <tr>
                                <th class="px-4 py-3">Jenis</th>
                                <th class="px-4 py-3">Urgensi</th>
                                <th class="px-4 py-3">Wilayah</th>
                                <th class="px-4 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($operatorQueue as $report)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $report->crisisType->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3">{{ $report->urgencyLevel->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $report->region->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3">
                                        @php($style = $statusStyles[$report->status] ?? $statusStyles['closed'])
                                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $style['color'] }}">{{ $style['label'] }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-4 text-center text-gray-500">Belum ada penugasan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @elseif($roleView === 'verifikator')
            <div class="rounded-xl border border-indigo-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Verifikasi Masuk</h3>
                        <p class="text-sm text-gray-500">Hanya laporan status "Baru" yang dapat diverifikasi.</p>
                    </div>
                </div>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-left font-semibold text-gray-600">
                            <tr>
                                <th class="px-4 py-3">Jenis</th>
                                <th class="px-4 py-3">Urgensi</th>
                                <th class="px-4 py-3">Wilayah</th>
                                <th class="px-4 py-3">Pembuat</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($pending as $report)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $report->crisisType->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3">{{ $report->urgencyLevel->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $report->region->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $report->creator->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <form method="POST" action="{{ route('verifications.approve', $report) }}">
                                                @csrf
                                                <button type="submit" class="rounded-md bg-emerald-600 px-3 py-2 text-xs font-semibold text-white shadow hover:bg-emerald-700">Setujui</button>
                                            </form>
                                            <form method="POST" action="{{ route('verifications.reject', $report) }}">
                                                @csrf
                                                <button type="submit" class="rounded-md bg-red-600 px-3 py-2 text-xs font-semibold text-white shadow hover:bg-red-700">Tolak</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-4 text-center text-gray-500">Tidak ada laporan baru.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @elseif($roleView === 'pimpinan')
            <div class="rounded-xl border border-indigo-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Ringkasan Terbaru</h3>
                        <p class="text-sm text-gray-500">Hanya tampilan baca untuk monitoring.</p>
                    </div>
                </div>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-left font-semibold text-gray-600">
                            <tr>
                                <th class="px-4 py-3">Jenis</th>
                                <th class="px-4 py-3">Urgensi</th>
                                <th class="px-4 py-3">Wilayah</th>
                                <th class="px-4 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($recentReports as $report)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $report->crisisType->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3">{{ $report->urgencyLevel->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $report->region->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3">
                                        @php($style = $statusStyles[$report->status] ?? $statusStyles['closed'])
                                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $style['color'] }}">{{ $style['label'] }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-4 text-center text-gray-500">Belum ada data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const typeCtx = document.getElementById('typeChart');
        const typeData = @json($typeChart);
        if (typeCtx && typeData.labels.length) {
            new Chart(typeCtx, {
                type: 'doughnut',
                data: {
                    labels: typeData.labels,
                    datasets: [{
                        data: typeData.data,
                        backgroundColor: typeData.colors,
                        borderWidth: 0,
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                    },
                    layout: { padding: { top: 4, right: 4, bottom: 4, left: 4 } },
                },
            });
        }

        const trendCtx = document.getElementById('trendChart');
        const trend7 = @json($trend7);
        const trend30 = @json($trend30);
        let trendChartInstance = null;

        const renderTrend = (source) => {
            if (!trendCtx || !source.labels.length) return;
            const config = {
                type: 'line',
                data: {
                    labels: source.labels,
                    datasets: [{
                        label: 'Laporan',
                        data: source.data,
                        borderColor: '#6366F1',
                        backgroundColor: 'rgba(99, 102, 241, 0.12)',
                        tension: 0.35,
                        fill: true,
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    layout: { padding: { top: 4, right: 4, bottom: 4, left: 4 } },
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1 } },
                    },
                },
            };

            if (trendChartInstance) trendChartInstance.destroy();
            trendChartInstance = new Chart(trendCtx, config);
        };

        renderTrend(trend7);

        document.querySelectorAll('.trend-toggle').forEach((btn) => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.trend-toggle').forEach(b => b.classList.remove('bg-indigo-600','text-white'));
                document.querySelectorAll('.trend-toggle').forEach(b => b.classList.add('bg-white','text-indigo-700','ring-1','ring-indigo-200'));
                btn.classList.remove('bg-white','text-indigo-700','ring-1','ring-indigo-200');
                btn.classList.add('bg-indigo-600','text-white');
                const range = btn.dataset.range;
                renderTrend(range === '30' ? trend30 : trend7);
            });
        });
    </script>
</x-app-layout>
