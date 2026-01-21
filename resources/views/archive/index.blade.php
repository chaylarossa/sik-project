<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Arsip Laporan Krisis') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('archive.index') }}" class="mb-6 grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        
                        <!-- Date From -->
                        <div>
                            <x-input-label for="date_from" :value="__('Dari Tanggal')" />
                            <x-text-input id="date_from" class="block mt-1 w-full" type="date" name="date_from" :value="request('date_from')" />
                            <x-input-error :messages="$errors->get('date_from')" class="mt-2" />
                        </div>

                        <!-- Date To -->
                        <div>
                            <x-input-label for="date_to" :value="__('Sampai Tanggal')" />
                            <x-text-input id="date_to" class="block mt-1 w-full" type="date" name="date_to" :value="request('date_to')" />
                            <x-input-error :messages="$errors->get('date_to')" class="mt-2" />
                        </div>

                        <!-- Crisis Type -->
                        <div>
                            <x-input-label for="crisis_type_id" :value="__('Jenis Krisis')" />
                            <select id="crisis_type_id" name="crisis_type_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">{{ __('Semua Jenis') }}</option>
                                @foreach($crisisTypes as $type)
                                    <option value="{{ $type->id }}" {{ request('crisis_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Urgency Level -->
                        <div>
                            <x-input-label for="urgency_level_id" :value="__('Tingkat Urgensi')" />
                            <select id="urgency_level_id" name="urgency_level_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">{{ __('Semua Urgensi') }}</option>
                                @foreach($urgencyLevels as $level)
                                    <option value="{{ $level->id }}" {{ request('urgency_level_id') == $level->id ? 'selected' : '' }}>
                                        {{ $level->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Region -->
                        <div>
                            <x-input-label for="region_id" :value="__('Wilayah')" />
                            <select id="region_id" name="region_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">{{ __('Semua Wilayah') }}</option>
                                @foreach($regions as $region)
                                    <option value="{{ $region->id }}" {{ request('region_id') == $region->id ? 'selected' : '' }}>
                                        {{ $region->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Verification Status -->
                        <div>
                            <x-input-label for="verification_status" :value="__('Status Verifikasi')" />
                            <select id="verification_status" name="verification_status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">{{ __('Semua Status') }}</option>
                                @foreach(['pending', 'approved', 'rejected'] as $status)
                                    <option value="{{ $status }}" {{ request('verification_status') == $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status -->
                        <div>
                            <x-input-label for="status" :value="__('Status Penanganan')" />
                            <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">{{ __('Semua Status') }}</option>
                                @foreach(['new', 'in_progress', 'done', 'closed'] as $status)
                                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                        {{ str_replace('_', ' ', ucfirst($status)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-end gap-2">
                            <x-primary-button type="submit">
                                {{ __('Filter') }}
                            </x-primary-button>
                            <a href="{{ route('archive.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Reset') }}
                            </a>
                        </div>
                    </form>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tingkat Urgensi</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wilayah</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Verifikasi</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($reports as $report)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            #{{ $report->id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $report->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $report->crisisType->name ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" style="background-color: {{ $report->urgencyLevel->color ?? '#eee' }}; color: white;">
                                                {{ $report->urgencyLevel->name ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $report->region->name ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($report->verification_status == 'approved')
                                                <span class="text-green-600 font-bold">Approved</span>
                                            @elseif($report->verification_status == 'rejected')
                                                <span class="text-red-600 font-bold">Rejected</span>
                                            @else
                                                <span class="text-yellow-600 font-bold">Pending</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            {{ str_replace('_', ' ', ucfirst($report->status)) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('reports.show', $report) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('Detail') }}</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            {{ __('Tidak ada data laporan.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $reports->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
