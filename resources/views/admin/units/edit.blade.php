<x-app-layout>
    <x-slot name="header">
        Ubah Unit
    </x-slot>
    <x-slot name="breadcrumb">
        Beranda / Master Data / Unit / Ubah
    </x-slot>

    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-gray-900">Ubah Unit</h3>
        <p class="mt-2 text-sm text-gray-600">Perbarui informasi unit/instansi.</p>

        <form method="POST" action="{{ route('admin.units.update', $unit) }}" class="mt-6">
            @include('admin.units.partials.form', ['submitLabel' => 'Simpan Perubahan', 'unit' => $unit])
        </form>
    </div>
</x-app-layout>
