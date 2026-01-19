@php
    use App\Models\CrisisReport;
@endphp

<x-app-layout>
    <x-slot name="header">
        Verifikasi Laporan
    </x-slot>
    <x-slot name="breadcrumb">
        Beranda / Verifikasi / Laporan
    </x-slot>

    <div class="mb-4">
        <a href="{{ route('reports.show', $report) }}" class="inline-flex items-center rounded-md border border-gray-200 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
            &larr; Kembali
        </a>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <div class="text-sm text-gray-500">Jenis</div>
                <div class="text-xl font-semibold text-gray-900">{{ $report->crisisType->name }}</div>
                <div class="mt-1 text-sm text-gray-600">Urgensi: {{ $report->urgencyLevel->name }} (Level {{ $report->urgencyLevel->level }})</div>
            </div>
            <div>
                <span class="inline-flex rounded-full bg-amber-50 px-4 py-2 text-sm font-semibold text-amber-700">
                    {{ $report->verification_status === CrisisReport::VERIFICATION_PENDING ? 'Menunggu Verifikasi' : $report->verification_status }}
                </span>
            </div>
        </div>

        <form method="POST" action="{{ route('reports.verify.store', $report) }}" class="mt-6 space-y-5">
            @csrf
            <div>
                <label for="action" class="text-sm font-medium text-gray-700">Aksi</label>
                <select id="action" name="action" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm">
                    <option value="approve" @selected(old('action') === 'approve')>Setujui</option>
                    <option value="reject" @selected(old('action') === 'reject')>Tolak</option>
                </select>
                @error('action')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="note" class="text-sm font-medium text-gray-700">Catatan (wajib jika ditolak)</label>
                <textarea id="note" name="note" rows="4" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm">{{ old('note') }}</textarea>
                @error('note')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('reports.show', $report) }}" class="rounded-md border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</a>
                <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-500">Simpan Verifikasi</button>
            </div>
        </form>
    </div>
</x-app-layout>
