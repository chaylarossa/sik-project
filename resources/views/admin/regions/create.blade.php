<x-app-layout>
    <x-slot name="header">
        Tambah Wilayah
    </x-slot>
    <x-slot name="breadcrumb">
        Beranda / Master Data / Wilayah / Tambah
    </x-slot>

    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-gray-900">Form Wilayah</h3>
        <p class="mt-2 text-sm text-gray-600">Lengkapi level dan induk untuk membentuk hierarki wilayah.</p>

        <form method="POST" action="{{ route('admin.regions.store') }}" class="mt-6">
            @include('admin.regions.partials.form', [
                'submitLabel' => 'Simpan',
            ])
        </form>
    </div>
</x-app-layout>
