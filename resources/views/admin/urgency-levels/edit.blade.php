<x-app-layout>
    <x-slot name="header">
        Ubah Tingkat Urgensi
    </x-slot>
    <x-slot name="breadcrumb">
        Beranda / Master Data / Tingkat Urgensi / Ubah
    </x-slot>

    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-gray-900">Perbarui Tingkat Urgensi</h3>
        <p class="mt-2 text-sm text-gray-600">Sesuaikan detail tingkat urgensi di bawah ini.</p>

        <form method="POST" action="{{ route('admin.urgency-levels.update', $urgencyLevel) }}" class="mt-6">
            @include('admin.urgency-levels.partials.form', [
                'urgencyLevel' => $urgencyLevel,
                'submitLabel' => 'Perbarui',
            ])
        </form>
    </div>
</x-app-layout>
