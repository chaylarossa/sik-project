<x-app-layout>
    <x-slot name="header">
        Wilayah
    </x-slot>
    <x-slot name="breadcrumb">
        Beranda / Master Data / Wilayah
    </x-slot>

    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form method="GET" action="{{ route('admin.regions.index') }}" class="flex w-full flex-col gap-3 rounded-lg border border-gray-200 bg-white p-4 shadow-sm sm:w-auto sm:flex-row sm:items-end">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                <label for="search" class="text-sm font-medium text-gray-700">Cari</label>
                <input
                    id="search"
                    name="search"
                    type="search"
                    value="{{ $search }}"
                    placeholder="Nama atau kode"
                    class="w-full rounded-md border border-gray-200 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 sm:w-52"
                >
            </div>

            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                <label for="filter_level" class="text-sm font-medium text-gray-700">Level</label>
                <select
                    id="filter_level"
                    name="level"
                    class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:w-48"
                >
                    <option value="">Semua level</option>
                    @foreach ($levelLabels as $levelKey => $label)
                        <option value="{{ $levelKey }}" @selected($levelFilter === $levelKey)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col gap-2 sm:flex-row sm:items-center" id="filter_parent_wrapper" hidden>
                <label for="filter_parent" class="text-sm font-medium text-gray-700">Induk</label>
                <select
                    id="filter_parent"
                    name="parent_id"
                    class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:w-64"
                >
                    <option value="">Pilih induk</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    Terapkan
                </button>
                <a href="{{ route('admin.regions.index') }}" class="text-sm text-gray-600 hover:text-gray-800">Reset</a>
            </div>
        </form>

        <a
            href="{{ route('admin.regions.create') }}"
            class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
        >
            + Tambah Wilayah
        </a>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('status') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            {{ session('error') }}
        </div>
    @endif

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Nama</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Kode</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Level</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Induk</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Diperbarui</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse ($regions as $region)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $region->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $region->code }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ \App\Models\Region::labelForLevel($region->level) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $region->parent?->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $region->updated_at->format('d M Y, H:i') }}</td>
                        <td class="px-6 py-4 text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2">
                                <a
                                    href="{{ route('admin.regions.edit', $region) }}"
                                    class="inline-flex items-center rounded-md border border-gray-200 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50"
                                >
                                    Ubah
                                </a>
                                <form method="POST" action="{{ route('admin.regions.destroy', $region) }}" onsubmit="return confirm('Hapus wilayah ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center rounded-md bg-red-50 px-3 py-1.5 text-sm font-medium text-red-700 hover:bg-red-100">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-6 text-center text-sm text-gray-500">Belum ada data wilayah.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $regions->links() }}
    </div>

    <script>
        (() => {
            const regions = @json($regionsData);
            const levelSelect = document.getElementById('filter_level');
            const parentWrapper = document.getElementById('filter_parent_wrapper');
            const parentSelect = document.getElementById('filter_parent');
            let initialParent = @json($parentFilter);
            const parentMap = {
                city: 'province',
                district: 'city',
                village: 'district',
            };

            const resetParentSelect = () => {
                parentSelect.innerHTML = '<option value="">Pilih induk</option>';
                parentSelect.value = '';
            };

            const populateParentOptions = () => {
                const level = levelSelect.value;
                const parentLevel = parentMap[level];

                if (!parentLevel) {
                    parentWrapper.hidden = true;
                    resetParentSelect();
                    return;
                }

                parentWrapper.hidden = false;
                resetParentSelect();

                regions
                    .filter((region) => region.level === parentLevel)
                    .forEach((region) => {
                        const option = document.createElement('option');
                        option.value = region.id;
                        option.textContent = region.name;
                        parentSelect.appendChild(option);
                    });

                if (initialParent) {
                    parentSelect.value = initialParent;
                    initialParent = null;
                }
            };

            levelSelect.addEventListener('change', populateParentOptions);
            populateParentOptions();
        })();
    </script>
</x-app-layout>
