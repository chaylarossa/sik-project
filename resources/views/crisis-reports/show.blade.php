@php
    use App\Models\CrisisReport;

    $statusLabels = [
        CrisisReport::STATUS_NEW => 'Baru',
        CrisisReport::STATUS_IN_PROGRESS => 'Proses',
        CrisisReport::STATUS_DONE => 'Selesai',
        CrisisReport::STATUS_CLOSED => 'Ditutup',
    ];

    $verificationLabels = [
        CrisisReport::VERIFICATION_PENDING => 'Menunggu Verifikasi',
        CrisisReport::VERIFICATION_APPROVED => 'Disetujui',
        CrisisReport::VERIFICATION_REJECTED => 'Ditolak',
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

    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <a href="{{ route('reports.index') }}" class="inline-flex items-center rounded-md border border-gray-200 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                &larr; Kembali
            </a>
            @can('verify', $report)
                @if ($report->verification_status === CrisisReport::VERIFICATION_PENDING)
                    <a href="{{ route('reports.verify', $report) }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-500">
                        Verifikasi
                    </a>
                @endif
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

                    $verificationColor = match ($report->verification_status) {
                        CrisisReport::VERIFICATION_APPROVED => 'bg-emerald-50 text-emerald-700',
                        CrisisReport::VERIFICATION_REJECTED => 'bg-red-50 text-red-700',
                        default => 'bg-amber-50 text-amber-700',
                    };
                @endphp
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex rounded-full px-4 py-2 text-sm font-semibold {{ $statusColor }}">{{ $statusLabels[$report->status] ?? $report->status }}</span>
                    <span class="inline-flex rounded-full px-4 py-2 text-sm font-semibold {{ $verificationColor }}">
                        {{ $verificationLabels[$report->verification_status] ?? $report->verification_status }}
                    </span>
                </div>
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

    <div class="mt-6 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <h4 class="text-sm font-semibold text-gray-900">Riwayat Verifikasi</h4>
        @if ($report->verifications->isEmpty())
            <p class="mt-3 text-sm text-gray-600">Belum ada verifikasi.</p>
        @else
            <div class="mt-4 space-y-4">
                @foreach ($report->verifications->sortByDesc('created_at') as $verification)
                    <div class="rounded-md border border-gray-100 bg-gray-50 p-4">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div class="text-sm font-medium text-gray-800">
                                {{ $verification->verifier->name }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $verification->created_at->format('d M Y, H:i') }}
                            </div>
                        </div>
                        <div class="mt-2 text-sm text-gray-700">
                            Status: {{ $verificationLabels[$verification->status] ?? $verification->status }}
                        </div>
                        @if ($verification->note)
                            <div class="mt-2 text-sm text-gray-600">Catatan: {{ $verification->note }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
