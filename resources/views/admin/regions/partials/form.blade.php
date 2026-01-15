@csrf
@if(isset($region))
    @method('PUT')
@endif

<div class="space-y-6">
    <div>
        <x-input-label for="name" value="Nama" />
        <x-text-input
            id="name"
            name="name"
            type="text"
            class="mt-2 w-full"
            :value="old('name', $region->name ?? '')"
            maxlength="150"
            required
        />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <div>
        <x-input-label for="code" value="Kode" />
        <x-text-input
            id="code"
            name="code"
            type="text"
            class="mt-2 w-full"
            :value="old('code', $region->code ?? '')"
            maxlength="50"
            required
        />
        <p class="mt-1 text-sm text-gray-500">Gunakan huruf/angka tanpa spasi (mis. JBR, BDG-CB01).</p>
        <x-input-error class="mt-2" :messages="$errors->get('code')" />
    </div>

    <div>
        <x-input-label for="level" value="Level" />
        <select
            id="level"
            name="level"
            class="mt-2 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            required
        >
            @foreach ($levelLabels as $levelKey => $label)
                <option value="{{ $levelKey }}" @selected(old('level', $region->level ?? \App\Models\Region::LEVEL_PROVINCE) === $levelKey)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        <p class="mt-1 text-sm text-gray-500">Level menentukan induk yang harus dipilih.</p>
        <x-input-error class="mt-2" :messages="$errors->get('level')" />
    </div>

    <div class="space-y-4" id="parent_container">
        <div id="province_row" class="hidden">
            <x-input-label for="province_select" value="Provinsi Induk" />
            <select
                id="province_select"
                class="mt-2 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
                <option value="">Pilih provinsi</option>
            </select>
        </div>

        <div id="city_row" class="hidden">
            <x-input-label for="city_select" value="Kota/Kabupaten Induk" />
            <select
                id="city_select"
                class="mt-2 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
                <option value="">Pilih kota/kabupaten</option>
            </select>
        </div>

        <div id="district_row" class="hidden">
            <x-input-label for="district_select" value="Kecamatan Induk" />
            <select
                id="district_select"
                class="mt-2 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
                <option value="">Pilih kecamatan</option>
            </select>
        </div>

        <x-input-error class="mt-2" :messages="$errors->get('parent_id')" />
    </div>

    <div class="flex items-center justify-between">
        <a
            href="{{ route('admin.regions.index') }}"
            class="inline-flex items-center rounded-md border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
        >
            Batal
        </a>
        <x-primary-button>{{ $submitLabel }}</x-primary-button>
    </div>
</div>

<script>
    (() => {
        const regions = @json($regionsData);
        const levelSelect = document.getElementById('level');
        const provinceRow = document.getElementById('province_row');
        const cityRow = document.getElementById('city_row');
        const districtRow = document.getElementById('district_row');
        const provinceSelect = document.getElementById('province_select');
        const citySelect = document.getElementById('city_select');
        const districtSelect = document.getElementById('district_select');
        const parentSelects = [provinceSelect, citySelect, districtSelect];
        const initialParentId = @json(old('parent_id', $region->parent_id ?? null));

        const findRegion = (id) => regions.find((region) => region.id === id);
        const ancestors = (() => {
            const map = {};
            let current = findRegion(initialParentId);
            while (current) {
                map[current.level] = current.id;
                current = findRegion(current.parent_id);
            }
            return map;
        })();

        const filterRegions = (level, parentId = null) => regions.filter((region) => {
            if (region.level !== level) {
                return false;
            }

            if (parentId === null) {
                return true;
            }

            return region.parent_id === parentId;
        });

        const setOptions = (select, options, placeholder) => {
            select.innerHTML = '';
            const placeholderOption = document.createElement('option');
            placeholderOption.value = '';
            placeholderOption.textContent = placeholder;
            select.appendChild(placeholderOption);

            options.forEach((option) => {
                const el = document.createElement('option');
                el.value = option.id;
                el.textContent = option.name;
                select.appendChild(el);
            });
        };

        const setParentName = (targetSelect) => {
            parentSelects.forEach((select) => {
                if (select === targetSelect) {
                    select.name = 'parent_id';
                    select.required = true;
                } else {
                    select.name = '';
                    select.required = false;
                }
            });
        };

        const populateProvinces = (selectedId = null) => {
            setOptions(provinceSelect, filterRegions('province'), 'Pilih provinsi');
            if (selectedId) {
                provinceSelect.value = selectedId;
            }
        };

        const populateCities = (provinceId = null, selectedId = null) => {
            if (!provinceId) {
                setOptions(citySelect, [], 'Pilih provinsi dulu');
                citySelect.value = '';
                return;
            }

            setOptions(citySelect, filterRegions('city', provinceId), 'Pilih kota/kabupaten');
            if (selectedId) {
                citySelect.value = selectedId;
            }
        };

        const populateDistricts = (cityId = null, selectedId = null) => {
            if (!cityId) {
                setOptions(districtSelect, [], 'Pilih kota/kabupaten dulu');
                districtSelect.value = '';
                return;
            }

            setOptions(districtSelect, filterRegions('district', cityId), 'Pilih kecamatan');
            if (selectedId) {
                districtSelect.value = selectedId;
            }
        };

        const syncVisibility = () => {
            const level = levelSelect.value;
            provinceRow.classList.toggle('hidden', level === 'province');
            cityRow.classList.toggle('hidden', !(level === 'district' || level === 'village'));
            districtRow.classList.toggle('hidden', level !== 'village');

            if (level === 'city') {
                setParentName(provinceSelect);
            } else if (level === 'district') {
                setParentName(citySelect);
            } else if (level === 'village') {
                setParentName(districtSelect);
            } else {
                setParentName(null);
            }
        };

        const syncForLevel = (preserveSelection = false) => {
            const level = levelSelect.value;
            const selectedProvince = preserveSelection ? provinceSelect.value : ancestors['province'] ?? null;
            const selectedCity = preserveSelection ? citySelect.value : ancestors['city'] ?? null;
            const selectedDistrict = preserveSelection ? districtSelect.value : ancestors['district'] ?? null;

            populateProvinces(selectedProvince);

            if (level === 'district' || level === 'village') {
                populateCities(provinceSelect.value || selectedProvince, selectedCity);
            } else {
                setOptions(citySelect, [], 'Tidak diperlukan');
            }

            if (level === 'village') {
                populateDistricts(citySelect.value || selectedCity, selectedDistrict);
            } else {
                setOptions(districtSelect, [], 'Tidak diperlukan');
            }

            syncVisibility();
        };

        levelSelect.addEventListener('change', () => {
            ancestors['province'] = null;
            ancestors['city'] = null;
            ancestors['district'] = null;
            syncForLevel();
        });

        provinceSelect.addEventListener('change', () => {
            populateCities(provinceSelect.value, null);
            setOptions(districtSelect, [], 'Pilih kota/kabupaten dulu');
        });

        citySelect.addEventListener('change', () => {
            populateDistricts(citySelect.value, null);
        });

        syncForLevel();
    })();
</script>
