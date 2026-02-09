@php
    use App\Models\CrisisReport;

    $statusLabels = [
        CrisisReport::STATUS_NEW => 'Baru',
        CrisisReport::STATUS_IN_PROGRESS => 'Proses',
        CrisisReport::STATUS_DONE => 'Selesai',
        CrisisReport::STATUS_CLOSED => 'Ditutup',
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        Timeline Penanganan
    </x-slot>
    <x-slot name="breadcrumb">
        Beranda / Laporan Krisis / Timeline
    </x-slot>

    <div class="mb-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('reports.show', $report) }}" class="inline-flex items-center rounded-md border border-gray-200 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                &larr; Kembali
            </a>
        </div>
        <span class="text-sm text-gray-500">Diperbarui terakhir: {{ optional($updates->last()?->occurred_at ?? $report->updated_at)->format('d M Y, H:i') }}</span>
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
                    <div class="text-base font-semibold text-gray-900">{{ $report->urgencyLevel->name }} (Level {{ $report->urgencyLevel->level }})</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Status Penanganan</div>
                    <div class="text-base font-semibold text-gray-900">{{ $statusLabels[$report->status] ?? $report->status }}</div>
                </div>
            </div>
            <p class="mt-4 text-sm text-gray-600">{{ $report->address }}</p>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Tambah Update Progres</h3>
                    <p class="mt-1 text-sm text-gray-600">Status hanya boleh bergerak maju (kecuali Administrator).</p>
                </div>
            </div>

            <form method="POST" action="{{ route('reports.updates.store', $report) }}" class="mt-4 space-y-4">
                @csrf
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label for="status" class="text-sm font-medium text-gray-700">Status</label>
                        <select
                            id="status"
                            name="status"
                            class="mt-1 w-full rounded-md border border-gray-200 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                            required
                        >
                            @foreach ($statusOptions as $status)
                                <option value="{{ $status }}" @selected(old('status', $report->status) === $status)>
                                    {{ $statusLabels[$status] ?? $status }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>

                    <div>
                        <label for="progress_percent" class="text-sm font-medium text-gray-700">Progres (%)</label>
                        <input
                            type="number"
                            id="progress_percent"
                            name="progress_percent"
                            min="0"
                            max="100"
                            step="1"
                            value="{{ old('progress_percent') }}"
                            class="mt-1 w-full rounded-md border border-gray-200 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                            required
                        />
                        <x-input-error :messages="$errors->get('progress_percent')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label for="occurred_at" class="text-sm font-medium text-gray-700">Waktu Kejadian</label>
                        <input
                            type="datetime-local"
                            id="occurred_at"
                            name="occurred_at"
                            value="{{ old('occurred_at', now()->format('Y-m-d\\TH:i')) }}"
                            class="mt-1 w-full rounded-md border border-gray-200 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                            required
                        />
                        <x-input-error :messages="$errors->get('occurred_at')" class="mt-2" />
                    </div>

                    <div>
                        <label for="note" class="text-sm font-medium text-gray-700">Catatan</label>
                        <textarea
                            id="note"
                            name="note"
                            rows="3"
                            class="mt-1 w-full rounded-md border border-gray-200 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        >{{ old('note') }}</textarea>
                        <x-input-error :messages="$errors->get('note')" class="mt-2" />
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <x-primary-button>
                        Simpan Update
                    </x-primary-button>
                </div>
            </form>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Timeline Progres</h3>
                <span class="text-sm text-gray-500">{{ $updates->count() }} update</span>
            </div>

            <div class="mt-4 space-y-4">
                @forelse ($updates as $update)
                    <div class="flex items-start gap-3">
                        <span class="mt-1 inline-flex h-2 w-2 rounded-full bg-indigo-500"></span>
                        <div class="flex-1 rounded-lg border border-gray-100 bg-gray-50 p-3">
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
    </div>
</x-app-layout>
