<x-app-layout>
    <x-slot name="header">
        Tambah Jenis Krisis
    </x-slot>
    <x-slot name="breadcrumb">
        Beranda / Master Data / Jenis Krisis / Tambah
    </x-slot>

    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-gray-900">Form Jenis Krisis</h3>
        <p class="mt-2 text-sm text-gray-600">Lengkapi nama dan kode untuk menambah jenis krisis baru.</p>

        <form method="POST" action="{{ route('admin.crisis-types.store') }}" class="mt-6">
            @include('admin.crisis-types.partials.form', [
                'submitLabel' => 'Simpan',
            ])
        </form>
    </div>
</x-app-layout>
