@php
    use App\Enums\PermissionName;
@endphp

@props(['navItems' => []])

<div class="flex h-full flex-col bg-white shadow-sm">
    <div class="flex h-16 items-center gap-2 border-b border-gray-100 px-4">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-lg font-semibold text-gray-900">
            <x-application-logo class="h-8 w-8 text-indigo-600" />
            <span>{{ config('app.name', 'SIK') }}</span>
        </a>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
        @foreach ($navItems as $item)
            @php
                $roles = $item['roles'] ?? [];
                $permissions = $item['permissions'] ?? [];
                $user = auth()->user();
                $canAccess = $user && ($user->canAny($permissions) || $user->hasAnyRole($roles));
            @endphp

            @if ($canAccess)
                <x-nav-link :href="route($item['route'])" :active="request()->routeIs($item['active'] ?? $item['route'])">
                    <span class="text-gray-400">
                        {!! $item['icon'] !!}
                    </span>
                    <span class="truncate">{{ $item['label'] }}</span>
                </x-nav-link>
            @endif
        @endforeach
    </nav>
</div>
