<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Notifikasi') }}
            </h2>
            @if($notifications->isNotEmpty())
            <form action="{{ route('notifications.mark-read') }}" method="POST">
                @csrf
                <button type="submit" class="text-sm text-indigo-600 hover:text-indigo-900">
                    Tandai semua sudah dibaca
                </button>
            </form>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @forelse ($notifications as $notification)
                        <div class="mb-4 pb-4 border-b border-gray-100 last:border-0 {{ $notification->read_at ? 'opacity-75' : 'bg-blue-50 -mx-6 px-6 py-4' }}">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ $notification->data['title'] ?? 'Notifikasi' }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">{{ $notification->data['message'] ?? '' }}</p>
                                    <p class="text-xs text-gray-500 mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    @if(isset($notification->data['url']))
                                        <a href="{{ $notification->data['url'] }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                                            Lihat Detail
                                        </a>
                                    @endif
                                    
                                    @if(is_null($notification->read_at))
                                        <form action="{{ route('notifications.mark-read', $notification->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-xs text-gray-500 hover:text-gray-700" title="Tandai sudah dibaca">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            Tidak ada notifikasi saat ini.
                        </div>
                    @endforelse

                    <div class="mt-4">
                        {{ $notifications->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
