<x-app-layout>
    <x-slot name="header">
        Buat Laporan Krisis
    </x-slot>
    <x-slot name="breadcrumb">
        Beranda / Laporan Krisis / Buat
    </x-slot>

    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-gray-900">Detail Laporan</h3>
        <p class="mt-2 text-sm text-gray-600">Isi informasi dasar kejadian beserta lokasi.</p>

        <form method="POST" action="{{ route('reports.store') }}" class="mt-6 space-y-5">
            @csrf

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="crisis_type_id" class="text-sm font-medium text-gray-700">Jenis Krisis</label>
                    <select
                        id="crisis_type_id"
                        name="crisis_type_id"
                        class="mt-1 w-full rounded-md border border-gray-200 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        required
                    >
                        <option value="">Pilih jenis krisis</option>
                        @foreach ($crisisTypes as $type)
                            <option value="{{ $type->id }}" @selected(old('crisis_type_id') == $type->id)>{{ $type->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('crisis_type_id')" class="mt-2" />
                </div>

                <div>
                    <label for="urgency_level_id" class="text-sm font-medium text-gray-700">Tingkat Urgensi</label>
                    <select
                        id="urgency_level_id"
                        name="urgency_level_id"
                        class="mt-1 w-full rounded-md border border-gray-200 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        required
                    >
                        <option value="">Pilih urgensi</option>
                        @foreach ($urgencyLevels as $level)
                            <option value="{{ $level->id }}" @selected(old('urgency_level_id') == $level->id)>
                                {{ $level->name }} (Level {{ $level->level }})
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('urgency_level_id')" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="province_id" class="text-sm font-medium text-gray-700">Provinsi (Pulau Jawa)</label>
                    <select
                        id="province_id"
                        name="province_id"
                        class="mt-1 w-full rounded-md border border-gray-200 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        required
                    >
                        <option value="">Pilih provinsi</option>
                        @foreach ($provinces as $province)
                            <option value="{{ $province->id }}" @selected(old('province_id') == $province->id)>{{ $province->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('province_id')" class="mt-2" />
                </div>

                <div>
                    <label for="city_id" class="text-sm font-medium text-gray-700">Kabupaten/Kota</label>
                    <select
                        id="city_id"
                        name="city_id"
                        class="mt-1 w-full rounded-md border border-gray-200 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        required
                    >
                        <option value="">Pilih kabupaten/kota</option>
                    </select>
                    <x-input-error :messages="$errors->get('city_id')" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="district_id" class="text-sm font-medium text-gray-700">Kecamatan</label>
                    <select
                        id="district_id"
                        name="district_id"
                        class="mt-1 w-full rounded-md border border-gray-200 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        required
                    >
                        <option value="">Pilih kecamatan</option>
                    </select>
                    <x-input-error :messages="$errors->get('district_id')" class="mt-2" />
                </div>

                <div>
                    <label for="region_id" class="text-sm font-medium text-gray-700">Kelurahan/Desa</label>
                    <select
                        id="region_id"
                        name="region_id"
                        class="mt-1 w-full rounded-md border border-gray-200 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        required
                    >
                        <option value="">Pilih kelurahan/desa</option>
                    </select>
                    <x-input-error :messages="$errors->get('region_id')" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="occurred_at" class="text-sm font-medium text-gray-700">Waktu Kejadian</label>
                    <input
                        id="occurred_at"
                        name="occurred_at"
                        type="datetime-local"
                        value="{{ old('occurred_at') }}"
                        class="mt-1 w-full rounded-md border border-gray-200 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        required
                    >
                    <x-input-error :messages="$errors->get('occurred_at')" class="mt-2" />
                </div>

                <div>
                    <label for="address" class="text-sm font-medium text-gray-700">Alamat</label>
                    <input
                        id="address"
                        name="address"
                        type="text"
                        value="{{ old('address') }}"
                        class="mt-1 w-full rounded-md border border-gray-200 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        required
                    >
                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="latitude" class="text-sm font-medium text-gray-700">Latitude</label>
                    <input
                        id="latitude"
                        name="latitude"
                        type="number"
                        step="any"
                        value="{{ old('latitude') }}"
                        class="mt-1 w-full rounded-md border border-gray-200 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        required
                    >
                    <x-input-error :messages="$errors->get('latitude')" class="mt-2" />
                </div>

                <div>
                    <label for="longitude" class="text-sm font-medium text-gray-700">Longitude</label>
                    <input
                        id="longitude"
                        name="longitude"
                        type="number"
                        step="any"
                        value="{{ old('longitude') }}"
                        class="mt-1 w-full rounded-md border border-gray-200 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        required
                    >
                    <x-input-error :messages="$errors->get('longitude')" class="mt-2" />
                </div>
            </div>

            <div>
                <label for="description" class="text-sm font-medium text-gray-700">Deskripsi Kejadian</label>
                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    class="mt-1 w-full rounded-md border border-gray-200 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                    required
                >{{ old('description') }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('reports.index') }}" class="inline-flex items-center rounded-md border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">Batal</a>
                <x-primary-button>Simpan</x-primary-button>
            </div>
        </form>
    </div>

    <script>
        (() => {
            const provinces = @json($provinces);
            const cities = @json($cities);
            const districts = @json($districts);
            const villages = @json($villages);

            const provinceSelect = document.getElementById('province_id');
            const citySelect = document.getElementById('city_id');
            const districtSelect = document.getElementById('district_id');
            const villageSelect = document.getElementById('region_id');

            const preset = {
                province: Number(@json(old('province_id'))) || null,
                city: Number(@json(old('city_id'))) || null,
                district: Number(@json(old('district_id'))) || null,
                village: Number(@json(old('region_id'))) || null,
            };

            const setOptions = (select, options, selectedId, placeholder) => {
                select.innerHTML = '';
                const placeholderOption = document.createElement('option');
                placeholderOption.value = '';
                placeholderOption.textContent = placeholder;
                select.appendChild(placeholderOption);

                options.forEach((item) => {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = item.name;
                    if (selectedId && Number(selectedId) === Number(item.id)) {
                        option.selected = true;
                    }
                    select.appendChild(option);
                });
            };

            const filterByParent = (collection, parentId) =>
                collection.filter((item) => Number(item.parent_id) === Number(parentId));

            const updateVillages = (districtId, selectedVillageId = null) => {
                const filteredVillages = districtId ? filterByParent(villages, districtId) : [];
                setOptions(villageSelect, filteredVillages, selectedVillageId, 'Pilih kelurahan/desa');
            };

            const updateDistricts = (cityId, selectedDistrictId = null, selectedVillageId = null) => {
                const filteredDistricts = cityId ? filterByParent(districts, cityId) : [];
                setOptions(districtSelect, filteredDistricts, selectedDistrictId, 'Pilih kecamatan');
                updateVillages(selectedDistrictId ?? null, selectedVillageId);
            };

            const updateCities = (provinceId, selectedCityId = null, selectedDistrictId = null, selectedVillageId = null) => {
                const filteredCities = provinceId ? filterByParent(cities, provinceId) : [];
                setOptions(citySelect, filteredCities, selectedCityId, 'Pilih kabupaten/kota');
                updateDistricts(selectedCityId ?? null, selectedDistrictId, selectedVillageId);
            };

            provinceSelect.addEventListener('change', (event) => {
                const provinceId = event.target.value || null;
                updateCities(provinceId, null, null, null);
            });

            citySelect.addEventListener('change', (event) => {
                const cityId = event.target.value || null;
                updateDistricts(cityId, null, null);
            });

            districtSelect.addEventListener('change', (event) => {
                const districtId = event.target.value || null;
                updateVillages(districtId, null);
            });

            // Initial render with preset selections (e.g., after validation errors)
            setOptions(provinceSelect, provinces, preset.province, 'Pilih provinsi');
            updateCities(preset.province, preset.city, preset.district, preset.village);
        })();
    </script>
</x-app-layout>
