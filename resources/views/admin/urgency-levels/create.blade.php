<x-app-layout>
    <x-slot name="header">
        Tambah Tingkat Urgensi
    </x-slot>
    <x-slot name="breadcrumb">
        Beranda / Master Data / Tingkat Urgensi / Tambah
    </x-slot>

    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-gray-900">Form Tingkat Urgensi</h3>
        <p class="mt-2 text-sm text-gray-600">Isi nama, level, dan opsi prioritas untuk menambah tingkat urgensi baru.</p>

        <form method="POST" action="{{ route('admin.urgency-levels.store') }}" class="mt-6">
            @include('admin.urgency-levels.partials.form', [
                'submitLabel' => 'Simpan',
            ])
        </form>
    </div>
</x-app-layout>
