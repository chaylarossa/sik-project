@csrf
@if(isset($urgencyLevel))
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
            :value="old('name', $urgencyLevel->name ?? '')"
            maxlength="100"
            required
        />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <div>
        <x-input-label for="level" value="Level (1-5)" />
        <select
            id="level"
            name="level"
            class="mt-2 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            required
        >
            <option value="" disabled {{ old('level', $urgencyLevel->level ?? '') === '' ? 'selected' : '' }}>Pilih level</option>
            @foreach (range(1, 5) as $level)
                <option value="{{ $level }}" @selected((int) old('level', $urgencyLevel->level ?? 0) === $level)>
                    Level {{ $level }}
                </option>
            @endforeach
        </select>
        <p class="mt-1 text-sm text-gray-500">Level unik, 1 = terendah, 5 = tertinggi.</p>
        <x-input-error class="mt-2" :messages="$errors->get('level')" />
    </div>

    <div>
        <x-input-label for="color" value="Warna (opsional)" />
        <div class="mt-2 grid gap-3 sm:grid-cols-[1fr_auto] sm:items-center sm:gap-4">
            <x-text-input
                id="color"
                name="color"
                type="text"
                class="w-full"
                :value="old('color', $urgencyLevel->color ?? '')"
                maxlength="20"
                placeholder="#FF0000 atau cyan"
            />
            <input
                id="color_preview"
                type="color"
                class="h-10 w-16 cursor-pointer rounded border border-gray-200 bg-white shadow-sm"
                aria-label="Pratinjau warna"
            >
        </div>
        <p class="mt-1 text-sm text-gray-500">Bisa ketik nama warna atau hex, dan gunakan picker untuk memilih cepat.</p>
        <x-input-error class="mt-2" :messages="$errors->get('color')" />
    </div>

    <script>
        (() => {
            const textInput = document.getElementById('color');
            const picker = document.getElementById('color_preview');
            if (!textInput || !picker) return;

            const normalize = (value) => {
                if (!value) return null;
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                try {
                    ctx.fillStyle = value;
                    const normalized = ctx.fillStyle;
                    return normalized.startsWith('#') ? normalized : null;
                } catch (e) {
                    return null;
                }
            };

            const syncPickerFromText = () => {
                const normalized = normalize(textInput.value.trim());
                if (normalized) {
                    picker.value = normalized;
                }
            };

            const syncTextFromPicker = () => {
                textInput.value = picker.value;
            };

            picker.addEventListener('input', syncTextFromPicker);
            textInput.addEventListener('input', syncPickerFromText);

            const initial = normalize(textInput.value.trim()) ?? '#2563eb';
            picker.value = initial;
            if (!textInput.value) {
                textInput.value = initial;
            }
        })();
    </script>

    <div class="flex items-center gap-3">
        <input type="hidden" name="is_high_priority" value="0">
        <input
            id="is_high_priority"
            name="is_high_priority"
            type="checkbox"
            value="1"
            class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
            @checked(old('is_high_priority', $urgencyLevel->is_high_priority ?? false))
        >
        <label for="is_high_priority" class="text-sm font-medium text-gray-700">Tandai sebagai prioritas tinggi</label>
    </div>

    <div class="flex items-center justify-between">
        <a
            href="{{ route('admin.urgency-levels.index') }}"
            class="inline-flex items-center rounded-md border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
        >
            Batal
        </a>
        <x-primary-button>{{ $submitLabel }}</x-primary-button>
    </div>
</div>
