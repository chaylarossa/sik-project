<x-app-layout>
    <x-slot name="header">
        Tingkat Urgensi
    </x-slot>
    <x-slot name="breadcrumb">
        Beranda / Master Data / Tingkat Urgensi
    </x-slot>

    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form method="GET" action="{{ route('admin.urgency-levels.index') }}" class="flex flex-col gap-2 sm:flex-row sm:items-center">
            <label for="search" class="text-sm font-medium text-gray-700">Cari</label>
            <div class="flex items-center gap-2">
                <input
                    id="search"
                    name="search"
                    type="search"
                    value="{{ $search }}"
                    placeholder="Nama atau level"
                    class="w-full rounded-md border border-gray-200 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 sm:w-64"
                >
                <button type="submit" class="inline-flex items-center rounded-md border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                    Cari
                </button>
            </div>
        </form>

        <a
            href="{{ route('admin.urgency-levels.create') }}"
            class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
        >
            + Tambah Urgensi
        </a>
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
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Nama</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Level</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Warna</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Prioritas</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Diperbarui</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse ($urgencyLevels as $urgencyLevel)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $urgencyLevel->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $urgencyLevel->level }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            @if ($urgencyLevel->color)
                                <span class="inline-flex items-center gap-2 rounded-full border border-gray-200 px-3 py-1 text-xs font-medium text-gray-700">
                                    <span class="h-3 w-3 rounded-full" style="background-color: {{ $urgencyLevel->color }}"></span>
                                    {{ $urgencyLevel->color }}
                                </span>
                            @else
                                <span class="text-xs text-gray-500">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if ($urgencyLevel->is_high_priority)
                                <span class="inline-flex items-center rounded-full bg-red-50 px-3 py-1 text-xs font-medium text-red-700">Tinggi</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600">Normal</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $urgencyLevel->updated_at->format('d M Y, H:i') }}</td>
                        <td class="px-6 py-4 text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2">
                                <a
                                    href="{{ route('admin.urgency-levels.edit', $urgencyLevel) }}"
                                    class="inline-flex items-center rounded-md border border-gray-200 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50"
                                >
                                    Ubah
                                </a>
                                <form method="POST" action="{{ route('admin.urgency-levels.destroy', $urgencyLevel) }}" onsubmit="return confirm('Hapus tingkat urgensi ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center rounded-md bg-red-50 px-3 py-1.5 text-sm font-medium text-red-700 hover:bg-red-100">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-6 text-center text-sm text-gray-500">Belum ada data tingkat urgensi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $urgencyLevels->links() }}
    </div>
</x-app-layout>
