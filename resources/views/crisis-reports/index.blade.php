@php
    use App\Models\CrisisReport;
    use Illuminate\Support\Str;

    $statusLabels = [
        CrisisReport::STATUS_NEW => 'Baru',
        CrisisReport::STATUS_IN_PROGRESS => 'Proses',
        CrisisReport::STATUS_DONE => 'Selesai',
        CrisisReport::STATUS_CLOSED => 'Ditutup',
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        Laporan Krisis
    </x-slot>
    <x-slot name="breadcrumb">
        Beranda / Laporan Krisis
    </x-slot>

    <div class="mb-4 flex flex-col gap-3 rounded-lg border border-gray-200 bg-white p-4 shadow-sm md:flex-row md:items-end md:justify-between">
        <form method="GET" action="{{ route('reports.index') }}" class="grid w-full grid-cols-1 gap-3 md:grid-cols-5">
            <div>
                <label for="crisis_type_id" class="text-sm font-medium text-gray-700">Jenis</label>
                <select
                    id="crisis_type_id"
                    name="crisis_type_id"
                    class="mt-1 w-full rounded-md border border-gray-200 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                >
                    <option value="">Semua</option>
                    @foreach ($crisisTypes as $type)
                        <option value="{{ $type->id }}" @selected($filters['crisis_type_id'] === $type->id)>{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status" class="text-sm font-medium text-gray-700">Status</label>
                <select
                    id="status"
                    name="status"
                    class="mt-1 w-full rounded-md border border-gray-200 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                >
                    <option value="">Semua</option>
                    @foreach ($statusOptions as $statusOption)
                        <option value="{{ $statusOption }}" @selected($filters['status'] === $statusOption)>{{ $statusLabels[$statusOption] ?? $statusOption }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="region_id" class="text-sm font-medium text-gray-700">Wilayah</label>
                <select
                    id="region_id"
                    name="region_id"
                    class="mt-1 w-full rounded-md border border-gray-200 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                >
                    <option value="">Semua</option>
                    @foreach ($regions as $region)
                        <option value="{{ $region->id }}" @selected($filters['region_id'] === $region->id)>{{ $region->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="period_start" class="text-sm font-medium text-gray-700">Periode Mulai</label>
                <input
                    id="period_start"
                    name="period_start"
                    type="date"
                    value="{{ $filters['period_start'] }}"
                    class="mt-1 w-full rounded-md border border-gray-200 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                >
            </div>

            <div>
                <label for="period_end" class="text-sm font-medium text-gray-700">Periode Selesai</label>
                <input
                    id="period_end"
                    name="period_end"
                    type="date"
                    value="{{ $filters['period_end'] }}"
                    class="mt-1 w-full rounded-md border border-gray-200 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                >
            </div>

            <div class="md:col-span-5 flex flex-wrap items-center gap-2 pt-2">
                <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Terapkan</button>
                <a href="{{ route('reports.index') }}" class="inline-flex items-center rounded-md border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">Reset</a>
            </div>
        </form>

        @can('create', App\Models\CrisisReport::class)
            <a
                href="{{ route('reports.create') }}"
                class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
            >
                + Buat Laporan
            </a>
        @endcan
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('status') }}
        </div>
    @endif

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Jenis</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Urgensi</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Wilayah</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Waktu</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Ringkasan</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse ($reports as $report)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $report->crisisType->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex h-2 w-2 rounded-full" style="background-color: {{ $report->urgencyLevel->color ?? '#94a3b8' }}"></span>
                                <span>{{ $report->urgencyLevel->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $report->region->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $report->occurred_at->format('d M Y, H:i') }}</td>
                        <td class="px-6 py-4 text-sm">
                            @php
                                $statusColor = match ($report->status) {
                                    CrisisReport::STATUS_NEW => 'bg-blue-50 text-blue-700',
                                    CrisisReport::STATUS_IN_PROGRESS => 'bg-amber-50 text-amber-700',
                                    CrisisReport::STATUS_DONE => 'bg-emerald-50 text-emerald-700',
                                    default => 'bg-gray-100 text-gray-700',
                                };
                            @endphp
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusColor }}">{{ $statusLabels[$report->status] ?? $report->status }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ Str::limit($report->description, 60) }}</td>
                        <td class="px-6 py-4 text-right text-sm font-medium">
                            <a
                                href="{{ route('reports.show', $report) }}"
                                class="inline-flex items-center rounded-md border border-gray-200 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50"
                            >
                                Lihat
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-6 text-center text-sm text-gray-500">Belum ada laporan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $reports->links() }}
    </div>
</x-app-layout>
