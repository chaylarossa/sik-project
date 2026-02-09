@csrf
@if(isset($unit))
    @method('PUT')
@endif

<div class="space-y-6">
    <div>
        <x-input-label for="name" value="Nama Unit" />
        <x-text-input
            id="name"
            name="name"
            type="text"
            class="mt-2 w-full"
            :value="old('name', $unit->name ?? '')"
            maxlength="100"
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
            :value="old('code', $unit->code ?? '')"
            maxlength="50"
            placeholder="BPBD"
            required
        />
        <p class="mt-1 text-sm text-gray-500">Gunakan huruf/angka tanpa spasi (mis. BPBD, DAMKAR).</p>
        <x-input-error class="mt-2" :messages="$errors->get('code')" />
    </div>

    <div>
        <x-input-label for="contact_phone" value="Kontak (opsional)" />
        <x-text-input
            id="contact_phone"
            name="contact_phone"
            type="text"
            class="mt-2 w-full"
            :value="old('contact_phone', $unit->contact_phone ?? '')"
            maxlength="30"
            placeholder="08xxxxxxxx"
        />
        <x-input-error class="mt-2" :messages="$errors->get('contact_phone')" />
    </div>

    <div class="flex items-center gap-3">
        <input type="hidden" name="is_active" value="0">
        <input
            id="is_active"
            name="is_active"
            type="checkbox"
            value="1"
            class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
            @checked(old('is_active', $unit->is_active ?? true))
        >
        <label for="is_active" class="text-sm font-medium text-gray-700">Aktif</label>
    </div>

    <div class="flex items-center justify-between">
        <a
            href="{{ route('admin.units.index') }}"
            class="inline-flex items-center rounded-md border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
        >
            Batal
        </a>
        <x-primary-button>{{ $submitLabel }}</x-primary-button>
    </div>
</div>
