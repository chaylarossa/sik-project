<x-app-layout>
    <x-slot name="header">
        Peta Krisis
    </x-slot>
    <x-slot name="breadcrumb">
        Beranda / Peta
    </x-slot>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <div class="mb-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <form id="map-filters" class="grid grid-cols-1 gap-3 md:grid-cols-4">
            <div>
                <label for="crisis_type_id" class="text-sm font-medium text-gray-700">Jenis Krisis</label>
                <select id="crisis_type_id" name="crisis_type_id" class="mt-1 w-full rounded-md border-gray-200 text-sm">
                    <option value="">Semua</option>
                    @foreach ($crisisTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="handling_status" class="text-sm font-medium text-gray-700">Status Penanganan</label>
                <select id="handling_status" name="handling_status" class="mt-1 w-full rounded-md border-gray-200 text-sm">
                    <option value="">Semua</option>
                    @foreach ($handlingStatuses as $status)
                        <option value="{{ $status }}">{{ $status }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="region_id" class="text-sm font-medium text-gray-700">Provinsi</label>
                <select id="region_id" name="region_id" class="mt-1 w-full rounded-md border-gray-200 text-sm">
                    <option value="">Semua</option>
                    @foreach ($regions as $region)
                        <option value="{{ $region->id }}">{{ $region->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <div id="map" class="h-[520px] w-full rounded-md"></div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    @vite(['resources/js/maps.js'])
</x-app-layout>
