@php
    use App\Models\CrisisReport;

    $statusLabels = [
        CrisisReport::STATUS_NEW => 'Baru',
        CrisisReport::STATUS_IN_PROGRESS => 'Proses',
        CrisisReport::STATUS_DONE => 'Selesai',
        CrisisReport::STATUS_CLOSED => 'Ditutup',
    ];

    $regionPath = collect([$report->region?->name])
        ->merge($report->region?->ancestors()->pluck('name') ?? collect())
        ->reverse()
        ->implode(' / ');
@endphp

<x-app-layout>
    <x-slot name="header">
        Detail Laporan
    </x-slot>
    <x-slot name="breadcrumb">
        Beranda / Laporan Krisis / Detail
    </x-slot>

    <div class="mb-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('reports.index') }}" class="inline-flex items-center rounded-md border border-gray-200 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                &larr; Kembali
            </a>
            @can('viewHandling', $report)
                <a href="{{ route('reports.timeline', $report) }}" class="inline-flex items-center rounded-md bg-indigo-50 px-3 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-100">
                    Timeline Penanganan
                </a>
            @endcan
        </div>
        <span class="text-sm text-gray-500">Dibuat oleh {{ $report->creator->name }} pada {{ $report->created_at->format('d M Y, H:i') }}</span>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <div class="text-sm text-gray-500">Jenis</div>
                <div class="text-xl font-semibold text-gray-900">{{ $report->crisisType->name }}</div>
                <div class="mt-1 text-sm text-gray-600">Urgensi: {{ $report->urgencyLevel->name }} (Level {{ $report->urgencyLevel->level }})</div>
            </div>
            <div>
                @php
                    $statusColor = match ($report->status) {
                        CrisisReport::STATUS_NEW => 'bg-blue-50 text-blue-700',
                        CrisisReport::STATUS_IN_PROGRESS => 'bg-amber-50 text-amber-700',
                        CrisisReport::STATUS_DONE => 'bg-emerald-50 text-emerald-700',
                        default => 'bg-gray-100 text-gray-700',
                    };
                @endphp
                <span class="inline-flex rounded-full px-4 py-2 text-sm font-semibold {{ $statusColor }}">{{ $statusLabels[$report->status] ?? $report->status }}</span>
            </div>
        </div>

        <dl class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="rounded-md border border-gray-100 bg-gray-50 p-4">
                <dt class="text-sm font-medium text-gray-600">Waktu Kejadian</dt>
                <dd class="mt-1 text-gray-900">{{ $report->occurred_at->format('d M Y, H:i') }}</dd>
            </div>
            <div class="rounded-md border border-gray-100 bg-gray-50 p-4">
                <dt class="text-sm font-medium text-gray-600">Wilayah</dt>
                <dd class="mt-1 text-gray-900">{{ $regionPath }}</dd>
            </div>
            <div class="rounded-md border border-gray-100 bg-gray-50 p-4">
                <dt class="text-sm font-medium text-gray-600">Alamat</dt>
                <dd class="mt-1 text-gray-900">{{ $report->address }}</dd>
            </div>
            <div class="rounded-md border border-gray-100 bg-gray-50 p-4">
                <dt class="text-sm font-medium text-gray-600">Koordinat</dt>
                <dd class="mt-1 text-gray-900">{{ $report->latitude }}, {{ $report->longitude }}</dd>
            </div>
        </dl>

        <div class="mt-6">
            <h4 class="text-sm font-medium text-gray-700">Deskripsi</h4>
            <p class="mt-2 whitespace-pre-line text-gray-800">{{ $report->description }}</p>
        </div>
    </div>

    @can('viewHandling', $report)
        <div class="mt-6 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <h4 class="text-lg font-semibold text-gray-900">Timeline Penanganan (5 terbaru)</h4>
                <a href="{{ route('reports.timeline', $report) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">Lihat semua</a>
            </div>

            <div class="mt-4 space-y-4">
                @forelse ($report->handlingUpdates as $update)
                    <div class="flex items-start gap-3">
                        <span class="mt-1 inline-flex h-2 w-2 rounded-full bg-indigo-500"></span>
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div class="text-sm font-semibold text-gray-900">{{ $statusLabels[$update->status] ?? $update->status }}</div>
                                <div class="text-xs text-gray-500">{{ optional($update->occurred_at)->format('d M Y, H:i') }}</div>
                            </div>
                            <div class="mt-1 text-sm text-gray-700">Progres {{ $update->progress_percent }}%</div>
                            @if ($update->note)
                                <div class="mt-1 text-sm text-gray-600">{{ $update->note }}</div>
                            @endif
                            <div class="mt-1 text-xs text-gray-500">Oleh {{ $update->updatedBy->name ?? 'Tidak diketahui' }}</div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Belum ada update progres.</p>
                @endforelse
            </div>
        </div>
    @endcan
</x-app-layout>
