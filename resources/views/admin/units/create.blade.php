<x-app-layout>
    <x-slot name="header">
        Tambah Unit
    </x-slot>
    <x-slot name="breadcrumb">
        Beranda / Master Data / Unit / Tambah
    </x-slot>

    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-gray-900">Tambah Unit</h3>
        <p class="mt-2 text-sm text-gray-600">Lengkapi data unit/instansi yang tersedia untuk penugasan.</p>

        <form method="POST" action="{{ route('admin.units.store') }}" class="mt-6">
            @include('admin.units.partials.form', ['submitLabel' => 'Simpan'])
        </form>
    </div>
</x-app-layout>
