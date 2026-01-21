@php
    use App\Models\CrisisReport;
    use App\Models\HandlingAssignment;

    $statusLabels = [
        HandlingAssignment::STATUS_ACTIVE => 'Aktif',
        HandlingAssignment::STATUS_COMPLETED => 'Selesai',
        HandlingAssignment::STATUS_CANCELLED => 'Dibatalkan',
    ];

    $verificationLabels = [
        CrisisReport::VERIFICATION_PENDING => 'Menunggu',
        CrisisReport::VERIFICATION_APPROVED => 'Disetujui',
        CrisisReport::VERIFICATION_REJECTED => 'Ditolak',
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        Penugasan Penanganan
    </x-slot>
    <x-slot name="breadcrumb">
        Beranda / Laporan Krisis / Penugasan
    </x-slot>

    <div class="mb-4 flex items-center justify-between">
        <a href="{{ route('reports.show', $report) }}" class="inline-flex items-center rounded-md border border-gray-200 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
            &larr; Kembali
        </a>
        <span class="text-sm text-gray-500">Status Verifikasi: {{ $verificationLabels[$report->verification_status] ?? $report->verification_status }}</span>
    </div>

    <div class="grid gap-6">
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900">Ringkasan Laporan</h3>
            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-3">
                <div>
                    <div class="text-sm text-gray-500">Jenis</div>
                    <div class="text-base font-semibold text-gray-900">{{ $report->crisisType->name }}</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Urgensi</div>
                    <div class="text-base font-semibold text-gray-900">{{ $report->urgencyLevel->name }}</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Status Penanganan</div>
                    <div class="text-base font-semibold text-gray-900">{{ $report->status }}</div>
                </div>
            </div>
            <p class="mt-4 text-sm text-gray-600">{{ $report->address }}</p>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900">Tambah Penugasan</h3>
            <p class="mt-2 text-sm text-gray-600">Pilih unit dan assignee untuk menangani laporan.</p>

            @if ($report->verification_status !== CrisisReport::VERIFICATION_APPROVED)
                <div class="mt-4 rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                    Laporan belum disetujui. Penugasan hanya tersedia setelah verifikasi disetujui.
                </div>
            @endif

            <form method="POST" action="{{ route('reports.assignments.store', $report) }}" class="mt-6 space-y-4">
                @csrf

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label for="unit_id" class="text-sm font-medium text-gray-700">Unit/Tim</label>
                        <select
                            id="unit_id"
                            name="unit_id"
                            class="mt-1 w-full rounded-md border border-gray-200 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                            required
                        >
                            <option value="">Pilih unit</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}" @selected(old('unit_id') == $unit->id)>{{ $unit->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('unit_id')" class="mt-2" />
                    </div>

                    <div>
                        <label for="assignee_id" class="text-sm font-medium text-gray-700">Assignee</label>
                        <select
                            id="assignee_id"
                            name="assignee_id"
                            class="mt-1 w-full rounded-md border border-gray-200 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                            required
                        >
                            <option value="">Pilih assignee</option>
                            @foreach ($assignees as $assignee)
                                <option value="{{ $assignee->id }}" @selected(old('assignee_id') == $assignee->id)>{{ $assignee->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('assignee_id')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label for="status" class="text-sm font-medium text-gray-700">Status</label>
                        <select
                            id="status"
                            name="status"
                            class="mt-1 w-full rounded-md border border-gray-200 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        >
                            @foreach ($statusOptions as $status)
                                <option value="{{ $status }}" @selected(old('status', HandlingAssignment::STATUS_ACTIVE) === $status)>
                                    {{ $statusLabels[$status] ?? $status }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>

                    <div>
                        <label for="note" class="text-sm font-medium text-gray-700">Catatan</label>
                        <textarea
                            id="note"
                            name="note"
                            rows="2"
                            class="mt-1 w-full rounded-md border border-gray-200 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        >{{ old('note') }}</textarea>
                        <x-input-error :messages="$errors->get('note')" class="mt-2" />
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <x-primary-button :disabled="$report->verification_status !== CrisisReport::VERIFICATION_APPROVED">
                        Tambah Penugasan
                    </x-primary-button>
                </div>
            </form>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Riwayat Penugasan</h3>
                <span class="text-sm text-gray-500">{{ $assignments->count() }} item</span>
            </div>

            <div class="mt-4 overflow-hidden rounded-lg border border-gray-100">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Unit</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Assignee</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Status</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Ditugaskan Oleh</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Catatan</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($assignments as $assignment)
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $assignment->unit?->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $assignment->assignee?->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-700">
                                    <span class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">
                                        {{ $statusLabels[$assignment->status] ?? $assignment->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-700">{{ $assignment->assignedBy?->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $assignment->note ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ $assignment->created_at->format('d M Y, H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-gray-500">Belum ada penugasan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
