<x-app-layout>
    <x-slot name="header">
        Ubah Jenis Krisis
    </x-slot>
    <x-slot name="breadcrumb">
        Beranda / Master Data / Jenis Krisis / Ubah
    </x-slot>

    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-gray-900">Perbarui Jenis Krisis</h3>
        <p class="mt-2 text-sm text-gray-600">Sesuaikan detail jenis krisis di bawah ini.</p>

        <form method="POST" action="{{ route('admin.crisis-types.update', $crisisType) }}" class="mt-6">
            @include('admin.crisis-types.partials.form', [
                'crisisType' => $crisisType,
                'submitLabel' => 'Perbarui',
            ])
        </form>
    </div>
</x-app-layout>
