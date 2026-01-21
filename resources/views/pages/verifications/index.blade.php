<x-app-layout>
    <x-slot name="header">
        Verifikasi Laporan
    </x-slot>
    <x-slot name="breadcrumb">
        Beranda / Verifikasi
    </x-slot>

    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Daftar Verifikasi</h3>
                <p class="mt-1 text-sm text-gray-600">Laporan menunggu persetujuan atau penolakan.</p>
            </div>
            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">{{ $reports->total() }} laporan</span>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">
                    <tr>
                        <th class="px-4 py-3">Jenis Krisis</th>
                        <th class="px-4 py-3">Tingkat Urgensi</th>
                        <th class="px-4 py-3">Wilayah</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($reports as $report)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $report->crisisType->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-800">
                                    {{ $report->urgencyLevel->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-700">{{ $report->region->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ optional($report->occurred_at)->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <form method="POST" action="{{ route('verifications.approve', $report) }}">
                                        @csrf
                                        <button type="submit" class="rounded-md bg-emerald-600 px-3 py-2 text-xs font-semibold text-white shadow hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                            Setujui
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('verifications.reject', $report) }}">
                                        @csrf
                                        <button type="submit" class="rounded-md bg-red-600 px-3 py-2 text-xs font-semibold text-white shadow hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                            Tolak
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-500">Belum ada laporan yang menunggu verifikasi.</td>
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
