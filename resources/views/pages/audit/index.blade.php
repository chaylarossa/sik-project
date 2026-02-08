<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Audit Log / Jejak Aktivitas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <!-- Filters -->
                    <form method="GET" action="{{ route('audit-log.index') }}" class="mb-6 grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <x-input-label for="user_id" value="User" />
                            <select name="user_id" id="user_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Semua User</option>
                                @foreach($users as $id => $name)
                                    <option value="{{ $id }}" {{ request('user_id') == $id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <x-input-label for="event" value="Action / Event" />
                            <select name="event" id="event" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Semua Event</option>
                                @foreach($events as $evt)
                                    <option value="{{ $evt }}" {{ request('event') == $evt ? 'selected' : '' }}>
                                        {{ ucfirst($evt) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="date_from" value="Dari Tanggal" />
                            <x-text-input id="date_from" name="date_from" type="date" class="block mt-1 w-full" :value="request('date_from')" />
                        </div>

                        <div>
                            <x-input-label for="date_to" value="Sampai Tanggal" />
                            <x-text-input id="date_to" name="date_to" type="date" class="block mt-1 w-full" :value="request('date_to')" />
                        </div>

                        <div class="flex items-end">
                            <x-primary-button class="w-full justify-center">
                                Filter
                            </x-primary-button>
                        </div>
                    </form>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metadata (IP/Agent)</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($activities as $activity)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $activity->created_at->format('Y-m-d H:i:s') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $activity->causer->name ?? 'System' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $activity->event ?? 'default' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" title="{{ $activity->description }}">
                                            {{ $activity->description }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            <div class="text-xs">
                                                IP: {{ $activity->getExtraProperty('ip') ?? '-' }}<br>
                                                <span class="truncate w-32 inline-block" title="{{ $activity->getExtraProperty('user_agent') }}">
                                                    {{ $activity->getExtraProperty('user_agent') ?? '-' }}
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                            Tidak ada data audit log.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $activities->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
