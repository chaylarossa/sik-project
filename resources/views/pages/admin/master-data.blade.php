<x-app-layout>
    <x-slot name="header">
        Master Data
    </x-slot>
    <x-slot name="breadcrumb">
        Beranda / Master Data
    </x-slot>

    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-gray-900">Pengelolaan Master</h3>
        <p class="mt-2 text-sm text-gray-600">Gunakan halaman ini untuk mengelola referensi krusial seperti jenis krisis, wilayah, dan unit.</p>

        <div class="mt-6 grid gap-4 sm:grid-cols-2">
            <a
                href="{{ route('admin.crisis-types.index') }}"
                class="flex items-center justify-between rounded-lg border border-gray-100 bg-gray-50 px-4 py-3 text-left transition hover:border-indigo-200 hover:bg-indigo-50"
            >
                <div>
                    <div class="text-sm font-semibold text-gray-900">Jenis Krisis</div>
                    <div class="text-xs text-gray-600">Kelola daftar kategori krisis operasional.</div>
                </div>
                <span class="text-indigo-600">&rarr;</span>
            </a>

            <a
                href="{{ route('admin.urgency-levels.index') }}"
                class="flex items-center justify-between rounded-lg border border-gray-100 bg-gray-50 px-4 py-3 text-left transition hover:border-indigo-200 hover:bg-indigo-50"
            >
                <div>
                    <div class="text-sm font-semibold text-gray-900">Tingkat Urgensi</div>
                    <div class="text-xs text-gray-600">Kelola prioritas dan alert urgensi.</div>
                </div>
                <span class="text-indigo-600">&rarr;</span>
            </a>

            <a
                href="{{ route('admin.units.index') }}"
                class="flex items-center justify-between rounded-lg border border-gray-100 bg-gray-50 px-4 py-3 text-left transition hover:border-indigo-200 hover:bg-indigo-50"
            >
                <div>
                    <div class="text-sm font-semibold text-gray-900">Unit/Instansi</div>
                    <div class="text-xs text-gray-600">Kelola unit yang terlibat penanganan.</div>
                </div>
                <span class="text-indigo-600">&rarr;</span>
            </a>
        </div>
    </div>
</x-app-layout>
