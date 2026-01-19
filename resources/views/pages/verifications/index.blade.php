@php
    use App\Models\CrisisReport;

    $verificationLabels = [
        CrisisReport::VERIFICATION_PENDING => 'Menunggu',
        CrisisReport::VERIFICATION_APPROVED => 'Disetujui',
        CrisisReport::VERIFICATION_REJECTED => 'Ditolak',
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        Verifikasi Laporan
    </x-slot>
    <x-slot name="breadcrumb">
        Beranda / Verifikasi
    </x-slot>

    <div class="mb-4 flex flex-wrap items-center gap-3">
        <a href="{{ route('verifications.index', ['status' => CrisisReport::VERIFICATION_PENDING]) }}" class="inline-flex items-center rounded-full border px-3 py-1 text-sm font-semibold {{ $status === CrisisReport::VERIFICATION_PENDING ? 'border-indigo-200 bg-indigo-50 text-indigo-700' : 'border-gray-200 text-gray-700 hover:bg-gray-50' }}">
            Menunggu ({{ $counters[CrisisReport::VERIFICATION_PENDING] ?? 0 }})
        </a>
        <a href="{{ route('verifications.index', ['status' => CrisisReport::VERIFICATION_APPROVED]) }}" class="inline-flex items-center rounded-full border px-3 py-1 text-sm font-semibold {{ $status === CrisisReport::VERIFICATION_APPROVED ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-gray-200 text-gray-700 hover:bg-gray-50' }}">
            Disetujui ({{ $counters[CrisisReport::VERIFICATION_APPROVED] ?? 0 }})
        </a>
        <a href="{{ route('verifications.index', ['status' => CrisisReport::VERIFICATION_REJECTED]) }}" class="inline-flex items-center rounded-full border px-3 py-1 text-sm font-semibold {{ $status === CrisisReport::VERIFICATION_REJECTED ? 'border-rose-200 bg-rose-50 text-rose-700' : 'border-gray-200 text-gray-700 hover:bg-gray-50' }}">
            Ditolak ({{ $counters[CrisisReport::VERIFICATION_REJECTED] ?? 0 }})
        </a>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Daftar Verifikasi</h3>
                <p class="mt-1 text-sm text-gray-600">Status: {{ $verificationLabels[$status] ?? $status }}</p>
            </div>
            <span class="text-sm text-gray-500">{{ $reports->total() }} item</span>
        </div>

        <div class="mt-4 overflow-hidden rounded-lg border border-gray-100">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Jenis</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Urgensi</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Pembuat</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Status Verifikasi</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($reports as $report)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $report->crisisType->name }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $report->urgencyLevel->name }} (Level {{ $report->urgencyLevel->level }})</td>
                            <td class="px-4 py-3 text-gray-700">{{ $report->creator->name }}</td>
                            <td class="px-4 py-3 text-gray-700">
                                <span class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">{{ $verificationLabels[$report->verification_status] ?? $report->verification_status }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-700">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('reports.show', $report) }}" class="inline-flex items-center rounded-md border border-gray-200 px-3 py-1 text-xs font-semibold text-gray-700 hover:bg-gray-50">Detail</a>

                                    @if ($report->verification_status === CrisisReport::VERIFICATION_PENDING)
                                        <form method="POST" action="{{ route('verifications.approve', $report) }}">
                                            @csrf
                                            <x-primary-button type="submit" class="!px-3 !py-1 text-xs">Setujui</x-primary-button>
                                        </form>
                                        <form method="POST" action="{{ route('verifications.reject', $report) }}">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center rounded-md border border-rose-200 bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-100">Tolak</button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-500">Sudah diverifikasi</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-500">Tidak ada laporan untuk status ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $reports->links() }}
        </div>
    </div>
</x-app-layout>
