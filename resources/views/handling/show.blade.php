<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('handling.index') }}" class="flex h-8 w-8 items-center justify-center rounded-full bg-white border border-gray-200 text-gray-400 hover:text-gray-600 hover:bg-gray-50 transition-colors">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </a>
                <div>
                    <h2 class="text-xl font-bold leading-tight text-gray-800">
                        {{ __('Penanganan Krisis') }} #{{ $report->id }}
                    </h2>
                    <div class="flex items-center gap-2 mt-1">
                        @php
                            $statusClasses = [
                                'BARU' => 'bg-gray-100 text-gray-700 ring-gray-600/10',
                                'DALAM_PENANGANAN' => 'bg-blue-50 text-blue-700 ring-blue-700/10',
                                'SELESAI' => 'bg-green-50 text-green-700 ring-green-600/10',
                                'DITUTUP' => 'bg-red-50 text-red-700 ring-red-600/10',
                            ];
                            $currentStatus = $report->handling->status;
                            $isClosed = $report->handling->status === \App\Models\CrisisHandling::STATUS_DITUTUP;
                        @endphp
                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $statusClasses[$currentStatus] ?? $statusClasses['BARU'] }}">
                            {{ str_replace('_', ' ', $currentStatus) }}
                        </span>
                        <span class="text-xs text-gray-400">&bull;</span>
                        <span class="text-xs text-gray-500">{{ $report->created_at->translatedFormat('d F Y, H:i') }}</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-end sm:items-center gap-4">
                <div class="w-full sm:w-48 text-right">
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-gray-500 font-medium">Progress</span>
                        <span class="font-bold text-gray-900">{{ $report->handling->progress }}%</span>
                    </div>
                    <div class="h-2 w-full overflow-hidden rounded-full bg-gray-100">
                        <div class="h-2 rounded-full bg-indigo-600 transition-all duration-500 ease-out" 
                             style="width: {{ $report->handling->progress }}%"></div>
                    </div>
                </div>

                <!-- Status Change Dropdown/Form -->
                @if(!$isClosed)
                    <div x-data="{ open: false, selectedStatus: '' }" class="relative">
                        <button @click="open = !open" class="btn btn-secondary text-xs">
                            Ubah Status
                            <svg class="ml-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-72 rounded-lg bg-white p-4 shadow-xl ring-1 ring-black ring-opacity-5 z-50">
                            <h4 class="mb-2 text-xs font-semibold uppercase text-gray-500">Update Status</h4>
                            <form id="status-form" action="{{ route('handling.status') }}" method="POST">
                                @csrf
                                <input type="hidden" name="crisis_report_id" value="{{ $report->id }}">
                                
                                <div class="mb-3 space-y-2">
                                    @foreach(\App\Models\CrisisHandling::ALLOWED_STATUSES as $statusOption)
                                        @if($statusOption !== $currentStatus)
                                            <label class="flex items-center cursor-pointer p-2 rounded hover:bg-gray-50">
                                                <input type="radio" name="status" value="{{ $statusOption }}" class="text-indigo-600 focus:ring-indigo-500" required
                                                    @change="selectedStatus = '{{ $statusOption }}'">
                                                <span class="ml-2 text-sm text-gray-700">{{ str_replace('_', ' ', $statusOption) }}</span>
                                            </label>
                                        @endif
                                    @endforeach
                                </div>

                                <input type="text" name="note" placeholder="Catatan perubahan (opsional)" class="input text-xs mb-3">
                                
                                <!-- Use x-data logic for modal trigger from inside Alpine if needed, but here we mix vanilla and Alpine -->
                                <button type="button" 
                                    @click="if(selectedStatus === 'DITUTUP') { openConfirmDialog(); open = false; } else { $el.closest('form').submit(); }" 
                                    class="btn btn-primary w-full text-xs justify-center">
                                    Simpan Status
                                </button>
                                
                                <!-- Hidden Submit for Confirm Modal Trigger via Script -->
                                <button type="submit" id="real-submit-btn" class="hidden"></button>
                            </form>
                        </div>
                    </div>

                    <!-- Confirmation Modal (Native Dialog) -->
                    <dialog id="confirm-dialog" class="p-0 rounded-xl shadow-2xl backdrop:bg-gray-900/50 open:animate-fade-in z-[60]">
                        <div class="w-full max-w-md bg-white p-6">
                            <h3 class="text-lg font-bold text-red-600">Konfirmasi Penutupan</h3>
                            <p class="mt-2 text-sm text-gray-600">
                                Apakah Anda yakin ingin mengubah status menjadi <strong>DITUTUP</strong>? 
                                Tindakan ini bersifat permanen dan data penanganan tidak dapat diubah lagi.
                            </p>
                            <div class="mt-6 flex justify-end gap-3">
                                <button type="button" id="btn-cancel-dialog" class="btn btn-secondary">Batal</button>
                                <button type="button" id="btn-confirm-dialog" class="btn btn-danger">Ya, Tutup Laporan</button>
                            </div>
                        </div>
                    </dialog>
                @else
                   <div class="px-3 py-1 bg-red-50 border border-red-100 rounded-md text-xs text-red-700 font-medium">
                        Laporan Ditutup
                   </div>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 rounded-md bg-green-50 p-4 border border-green-200">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Info Grid -->
            <div class="mb-6 grid grid-cols-1 gap-4 lg:grid-cols-4">
                <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm lg:col-span-3">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-8">
                        <div>
                            <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Judul Laporan</span>
                            <div class="mt-1 flex items-center gap-2">
                                <p class="text-base font-bold text-gray-900">{{ $report->crisisType->name }}</p>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ match($report->urgencyLevel->name ?? '') { 'Tinggi', 'Critical' => 'bg-red-100 text-red-700', 'Sedang' => 'bg-yellow-100 text-yellow-700', default => 'bg-green-100 text-green-700' } }}">
                                    {{ $report->urgencyLevel->name }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Lokasi & Pelapor</span>
                            <div class="mt-1">
                                <p class="text-sm font-semibold text-gray-900">{{ $report->region->name }}</p>
                                <p class="text-xs text-gray-500">{{ $report->creator->name ?? 'Anonim' }}</p>
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">Deskripsi</span>
                            <p class="mt-1 text-sm text-gray-700 leading-relaxed">{{ $report->description }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-100 bg-gradient-to-br from-indigo-600 to-indigo-700 p-5 text-white shadow-md">
                    <h3 class="text-xs font-medium opacity-80 uppercase tracking-wide">Tim Bertugas</h3>
                    <div class="mt-2 text-3xl font-bold">{{ $report->units->count() }} <span class="text-sm font-normal opacity-80">Unit</span></div>
                    
                    <div class="mt-4 pt-4 border-t border-white/20">
                        <h4 class="text-xs font-medium opacity-80 uppercase tracking-wide">Update Terakhir</h4>
                        <p class="mt-1 text-sm font-semibold">
                            {{ $report->handling->logs->count() > 0 ? $report->handling->logs->sortByDesc('created_at')->first()->created_at->diffForHumans() : 'Belum ada' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
                <!-- Tabs Navigation & Content -->
                <div class="lg:col-span-3 space-y-6">
                    <!-- Tabs Header -->
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-6 overflow-x-auto" aria-label="Tabs">
                            @foreach(['ringkasan' => 'Ringkasan', 'assignment' => 'Penugasan Tim', 'progress' => 'Update Progres', 'timeline' => 'Timeline'] as $key => $label)
                                <a href="#{{ $key }}" 
                                   data-tab-target="{{ $key }}"
                                   class="whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                    {{ $label }}
                                </a>
                            @endforeach
                        </nav>
                    </div>

                    <!-- Tab Contents -->
                    <div class="min-h-[400px]">
                        <!-- A) Ringkasan -->
                        <div id="panel-ringkasan" data-tab-panel="ringkasan" class="space-y-6 hidden">
                            <div class="card p-6">
                                <h4 class="text-lg font-bold text-gray-900 mb-4">Status Tim Saat Ini</h4>
                                @if($report->units->isEmpty())
                                    <div class="text-center py-8 bg-gray-50 rounded-lg dashed-border border-gray-300">
                                        <p class="text-gray-500 text-sm">Belum ada tim yang ditugaskan.</p>
                                        @if(!$isClosed)
                                            <a href="#assignment" data-tab-target="assignment" class="mt-3 btn btn-primary text-xs inline-block">
                                                Tugaskan Tim Sekarang
                                            </a>
                                        @endif
                                    </div>
                                @else
                                    <div class="flex flex-wrap gap-2 mb-6">
                                        @foreach($report->units as $unit)
                                            <div class="inline-flex items-center rounded-full bg-white border border-indigo-200 px-3 py-1.5 shadow-sm">
                                                <div class="h-2 w-2 rounded-full bg-indigo-500 mr-2"></div>
                                                <span class="text-sm font-medium text-gray-900">{{ $unit->name }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    @if(!$isClosed)
                                        <div class="flex gap-3">
                                            <a href="#assignment" data-tab-target="assignment" class="btn btn-secondary text-xs inline-flex items-center">
                                                <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                                Tambah Tim
                                            </a>
                                            <a href="#progress" data-tab-target="progress" class="btn btn-primary text-xs inline-flex items-center">
                                                Update Progres
                                            </a>
                                        </div>
                                    @endif
                                @endif
                            </div>

                            @if($isClosed)
                                <div class="card bg-red-50 border-red-100 p-6">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-red-800">Laporan Ditutup Permanen</h3>
                                            <div class="mt-2 text-sm text-red-700">
                                                <p>
                                                    Tindakan penanganan telah selesai. Laporan ini diarsipkan sebagai referensi di masa depan.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- B) Penugasan Tim -->
                        <div id="panel-assignment" data-tab-panel="assignment" class="space-y-6 hidden">
                            <div class="card p-6">
                                <h4 class="text-lg font-bold text-gray-900 mb-4">Form Penugasan</h4>
                                
                                @if($isClosed)
                                    <div class="bg-gray-100 p-4 rounded text-center text-gray-500 mb-4">Fitur dinonaktifkan karena status laporan DITUTUP.</div>
                                @endif

                                <form action="{{ route('handling.assign') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="crisis_report_id" value="{{ $report->id }}">
                                    
                                    <div class="mb-6">
                                        <label class="block text-sm font-medium text-gray-700 mb-3">Pilih Unit Tersedia</label>
                                        @if($availableUnits->isEmpty())
                                            <div class="p-3 bg-yellow-50 text-yellow-700 text-sm rounded">Tidak ada unit lain yang tersedia saat ini.</div>
                                        @else
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-60 overflow-y-auto custom-scrollbar">
                                                @foreach($availableUnits as $unit)
                                                    <label class="relative flex items-center p-3 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors {{ $isClosed ? 'opacity-50 pointer-events-none' : '' }}">
                                                        <input type="checkbox" name="unit_ids[]" value="{{ $unit->id }}" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" {{ $isClosed ? 'disabled' : '' }}>
                                                        <span class="ml-3 text-sm font-medium text-gray-900">{{ $unit->name }}</span>
                                                        <span class="ml-auto text-xs text-gray-500">{{ $unit->type ?? 'Unit' }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Penugasan</label>
                                        <input type="text" name="note" class="input" placeholder="Instruksi khusus untuk tim..." {{ $isClosed ? 'disabled' : '' }}>
                                    </div>

                                    <div class="flex justify-end">
                                        <button type="submit" class="btn btn-primary" {{ $availableUnits->isEmpty() || $isClosed ? 'disabled' : '' }}>
                                            Tugaskan Tim Terpilih
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <div class="card overflow-hidden">
                                <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
                                    <h5 class="font-semibold text-gray-800">Riwayat Penugasan</h5>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Oleh</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @forelse($report->units as $unit)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $unit->name }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ \Carbon\Carbon::parse($unit->pivot->assigned_at ?? $unit->pivot->created_at)->translatedFormat('d M Y, H:i') }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">ID: {{ $unit->pivot->assigned_by }}</td>
                                                    <td class="px-6 py-4 text-sm text-gray-500 italic">{{ $unit->pivot->note ?? '-' }}</td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada data</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- C) Update Progres -->
                        <div id="panel-progress" data-tab-panel="progress" class="card p-6 hidden">
                            <h4 class="text-lg font-bold text-gray-900 mb-4">Update Progres & Aktivitas</h4>
                            
                            @if($isClosed)
                                <div class="bg-gray-100 p-4 rounded text-center text-gray-500 mb-4">Fitur dinonaktifkan karena status laporan DITUTUP.</div>
                            @endif

                            <form action="{{ route('handling.progress') }}" method="POST">
                                @csrf
                                <input type="hidden" name="crisis_report_id" value="{{ $report->id }}">
                                
                                <div class="mb-8">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tingkat Penyelesaian</label>
                                    <div class="flex items-center gap-4">
                                        <input type="range" name="progress" id="progress-range" min="0" max="100" value="{{ $report->handling->progress }}" 
                                            class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600"
                                            {{ $isClosed ? 'disabled' : '' }}>
                                        <div class="relative w-20">
                                            <input type="number" name="progress_val" id="progress-number" value="{{ $report->handling->progress }}" min="0" max="100" class="input text-center font-bold" {{ $isClosed ? 'disabled' : '' }}>
                                            <span class="absolute right-2 top-2 text-gray-500 text-xs">%</span>
                                        </div>
                                    </div>
                                    <p id="progress-warning" class="mt-2 text-xs text-indigo-600" style="display: {{ $report->handling->progress == 100 ? 'block' : 'none' }}">
                                        * Progres 100% memungkinkan status diubah menjadi <strong>Selesai</strong>.
                                    </p>
                                </div>

                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Aktivitas / Laporan Lapangan</label>
                                    <textarea name="description" rows="5" class="input" placeholder="Jelaskan perkembangan situasi, kendala, atau tindakan yang telah dilakukan..." required {{ $isClosed ? 'disabled' : '' }}></textarea>
                                </div>

                                <div class="flex justify-end">
                                    <button type="submit" class="btn btn-primary" {{ $isClosed ? 'disabled' : '' }}>
                                        Simpan Progres
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- D) Timeline -->
                        <div id="panel-timeline" data-tab-panel="timeline" class="card flex flex-col h-[600px] hidden">
                            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-xl">
                                <h4 class="font-bold text-gray-800">Timeline Aktivitas</h4>
                                <button type="button" 
                                    onclick="fetchTimelineVanilla(this, '{{ route('handling.timeline', $report->id) }}')"
                                    class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    Refresh Timeline
                                </button>
                            </div>
                            <!-- Container for AJAX Content -->
                            <div id="timeline-container" class="flex-1 overflow-y-auto p-0 relative">
                                <!-- Initial Content (Server Rendered First Time) -->
                                @include('handling._timeline', ['logs' => $report->handling->logs])
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar (Quick Info) -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="hidden lg:block card p-4 bg-gray-50 border-gray-100 sticky top-4">
                        <h5 class="text-xs font-bold text-gray-500 uppercase mb-3">Panduan Singkat</h5>
                        <ul class="text-xs text-gray-600 space-y-2 list-disc list-inside">
                            <li>Pastikan update progres berkala setiap ada perubahan.</li>
                            <li>Gunakan tab "Penugasan" untuk menambah unit bantuan.</li>
                            <li>Status "Selesai" direkomendasikan saat progres 100%.</li>
                            <li>Status "Ditutup" bersifat permanen (Arsip).</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('handling._scripts')

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 4px; }
        .dashed-border { border-style: dashed; }
        .open\:animate-fade-in[open] { animation: fadeIn 0.3s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
    </style>
</x-app-layout>