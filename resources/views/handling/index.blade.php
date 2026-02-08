<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Daftar Penanganan') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            
            <!-- Filters -->
            <div class="card p-4 mb-6">
                <form method="GET" action="{{ route('handling.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-2">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari Laporan</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" 
                            placeholder="Judul, Lokasi, atau Deskripsi..." 
                            class="input">
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status Penanganan</label>
                        <select name="status" id="status" class="input">
                            <option value="all">Semua Status</option>
                            @foreach(\App\Models\CrisisHandling::ALLOWED_STATUSES as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                    {{ str_replace('_', ' ', $status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                     <div class="flex items-end">
                        <button type="submit" class="btn btn-primary w-full">
                            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Info Krisis</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status & Progres</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tim</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Updated</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($reports as $report)
                                @php
                                    $handling = $report->handling;
                                    $status = $handling ? $handling->status : 'BARU';
                                    $progress = $handling ? $handling->progress : 0;
                                    $statusClass = match($status) {
                                        'BARU' => 'badge-baru',
                                        'DALAM_PENANGANAN' => 'badge-proses',
                                        'SELESAI' => 'badge-selesai',
                                        'DITUTUP' => 'badge-ditutup',
                                        default => 'badge-baru',
                                    };
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        #{{ $report->id }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-gray-900">{{ $report->crisisType->name }}</div>
                                        <div class="text-xs text-gray-500 truncate max-w-xs">{{ $report->description }}</div>
                                        <span class="inline-flex mt-1 items-center px-2 py-0.5 rounded text-[10px] font-medium 
                                            {{ match($report->urgencyLevel->name ?? '') { 
                                                'Tinggi', 'Critical' => 'bg-red-100 text-red-800', 
                                                'Sedang' => 'bg-yellow-100 text-yellow-800', 
                                                default => 'bg-green-100 text-green-800' 
                                            } }}">
                                            {{ $report->urgencyLevel->name ?? 'Normal' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ $report->region->name }}
                                    </td>
                                    <td class="px-6 py-4 w-48">
                                        <div class="flex flex-col gap-2">
                                            <span class="badge {{ $statusClass }} w-fit">
                                                {{ str_replace('_', ' ', $status) }}
                                            </span>
                                            <div class="w-full bg-gray-200 rounded-full h-1.5 dark:bg-gray-200">
                                                <div class="bg-indigo-600 h-1.5 rounded-full" style="width: {{ $progress }}%"></div>
                                            </div>
                                            <span class="text-xs text-right text-gray-500 font-medium">{{ $progress }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($report->units->isEmpty())
                                            <span class="text-xs text-gray-400 italic">Belum ada</span>
                                        @else
                                            <div class="flex -space-x-2 overflow-hidden">
                                                @foreach($report->units->take(3) as $unit)
                                                    <div class="inline-block h-6 w-6 rounded-full ring-2 ring-white bg-indigo-100 flex items-center justify-center text-[10px] font-bold text-indigo-600" title="{{ $unit->name }}">
                                                        {{ substr($unit->name, 0, 1) }}
                                                    </div>
                                                @endforeach
                                                @if($report->units->count() > 3)
                                                    <div class="inline-block h-6 w-6 rounded-full ring-2 ring-white bg-gray-100 flex items-center justify-center text-[10px] font-bold text-gray-500">
                                                        +{{ $report->units->count() - 3 }}
                                                    </div>
                                                @endif
                                            </div>
                                            <span class="text-xs text-gray-500 mt-1 block">{{ $report->units->count() }} Unit</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $handling ? $handling->updated_at->diffForHumans() : $report->updated_at->diffForHumans() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <a href="{{ route('handling.show', $report->id) }}" class="btn btn-secondary text-xs">
                                            Buka
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="h-10 w-10 text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                            <p>Tidak ada laporan penanganan yang ditemukan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $reports->withQueryString()->links() }}
            </div>
        </div>
    </div>
</x-app-layout>