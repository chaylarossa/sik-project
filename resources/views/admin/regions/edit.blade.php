<x-app-layout>
    <x-slot name="header">
        Ubah Wilayah
    </x-slot>
    <x-slot name="breadcrumb">
        Beranda / Master Data / Wilayah / Ubah
    </x-slot>

    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-gray-900">Perbarui Wilayah</h3>
        <p class="mt-2 text-sm text-gray-600">Sesuaikan detail wilayah dan induknya.</p>

        <form method="POST" action="{{ route('admin.regions.update', $region) }}" class="mt-6">
            @include('admin.regions.partials.form', [
                'region' => $region,
                'submitLabel' => 'Perbarui',
            ])
        </form>
    </div>
</x-app-layout>
