<x-app-layout>
    <x-slot name="header">
        Verifikasi Laporan
    </x-slot>
    <x-slot name="breadcrumb">
        Beranda / Verifikasi
    </x-slot>

    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-gray-900">Daftar Verifikasi</h3>
        <p class="mt-2 text-sm text-gray-600">Daftar laporan yang menunggu verifikasi.</p>

        @if ($reports->isEmpty())
            <p class="mt-6 text-sm text-gray-600">Belum ada laporan untuk diverifikasi.</p>
        @else
            <div class="mt-6 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-4 py-3 text-left">Jenis</th>
                            <th class="px-4 py-3 text-left">Urgensi</th>
                            <th class="px-4 py-3 text-left">Wilayah</th>
                            <th class="px-4 py-3 text-left">Waktu</th>
                            <th class="px-4 py-3 text-left">Pelapor</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($reports as $report)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $report->crisisType->name }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $report->urgencyLevel->name }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $report->region->name }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $report->occurred_at->format('d M Y, H:i') }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $report->creator->name }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('reports.verify', $report) }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-xs font-semibold text-white shadow-sm hover:bg-indigo-500">
                                        Verifikasi
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $reports->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
