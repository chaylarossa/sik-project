@php
    use App\Models\CrisisHandlingLog;
    // Sorting logs descending
    $sortedLogs = $logs->sortByDesc('created_at');
@endphp

<div class="flow-root">
    <ul role="list" class="-mb-8">
        @forelse($sortedLogs as $log)
            <li>
                <div class="relative pb-8">
                    @if(!$loop->last)
                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                    @endif
                    <div class="relative flex space-x-3">
                        <!-- Icon based on type -->
                        <div>
                            @switch($log->type)
                                @case(CrisisHandlingLog::TYPE_ASSIGNMENT)
                                    <span class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center ring-8 ring-white">
                                        <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </span>
                                    @break
                                @case(CrisisHandlingLog::TYPE_PROGRESS)
                                    <span class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center ring-8 ring-white">
                                         <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                        </svg>
                                    </span>
                                    @break
                                @case(CrisisHandlingLog::TYPE_STATUS)
                                    <span class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center ring-8 ring-white">
                                         <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </span>
                                    @break
                                @default
                                    <span class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center ring-8 ring-white">
                                        <svg class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </span>
                            @endswitch
                        </div>
                        
                        <!-- Content -->
                        <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                            <div>
                                <p class="text-sm font-bold text-gray-900">
                                    @switch($log->type)
                                        @case(CrisisHandlingLog::TYPE_ASSIGNMENT)
                                            Penugasan Unit
                                            @break
                                        @case(CrisisHandlingLog::TYPE_PROGRESS)
                                            Update Progres
                                            @break
                                        @case(CrisisHandlingLog::TYPE_STATUS)
                                            Perubahan Status
                                            @break
                                        @default
                                            Catatan Sistem
                                    @endswitch
                                </p>
                                <div class="mt-1 text-sm text-gray-700">
                                    {{-- Custom rendering per type --}}
                                    @if($log->type === CrisisHandlingLog::TYPE_ASSIGNMENT)
                                        <p class="mb-1">Menugaskan unit: <span class="font-medium">{{ implode(', ', $log->payload['unit_names'] ?? []) }}</span></p>
                                        @if(!empty($log->payload['note']))
                                            <p class="italic text-gray-500">"{{ $log->payload['note'] }}"</p>
                                        @endif
                                    
                                    @elseif($log->type === CrisisHandlingLog::TYPE_PROGRESS)
                                        <p class="mb-1">Progres: <span class="font-mono text-xs bg-gray-100 px-1 rounded">{{ $log->payload['old_progress'] ?? 0 }}%</span> &rarr; <span class="font-bold text-indigo-600">{{ $log->payload['new_progress'] }}%</span></p>
                                        <p class="whitespace-pre-line text-gray-600">{{ $log->payload['description'] ?? '' }}</p>

                                    @elseif($log->type === CrisisHandlingLog::TYPE_STATUS)
                                        <p>Status berubah dari <span class="badge-sm">{{ str_replace('_', ' ', $log->payload['old_status'] ?? '-') }}</span> menjadi <span class="font-bold text-gray-900">{{ str_replace('_', ' ', $log->payload['new_status'] ?? '-') }}</span></p>
                                        @if(!empty($log->payload['note']))
                                            <p class="mt-1 italic text-gray-500">"{{ $log->payload['note'] }}"</p>
                                        @endif
                                    @else
                                        {{ json_encode($log->payload) }}
                                    @endif
                                </div>
                            </div>
                            <div class="whitespace-nowrap text-right text-sm text-gray-500">
                                <time datetime="{{ $log->created_at }}">{{ $log->created_at->format('d M H:i') }}</time>
                                <p class="text-xs text-gray-400 mt-1">{{ $log->creator->name ?? 'System' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        @empty
            <li class="py-4 text-center">
                <p class="text-sm text-gray-500 italic">Belum ada riwayat aktivitas.</p>
            </li>
        @endforelse
    </ul>
    
    <style>
        .badge-sm {
            @apply inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800;
        }
    </style>
</div>