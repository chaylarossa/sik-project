<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        @php
            use App\Enums\PermissionName;

            $navItems = [
                [
                    'label' => 'Dashboard',
                    'route' => 'dashboard',
                    'permissions' => [PermissionName::ViewDashboard->value],
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l9-9 9 9M4.5 10.5V21h15V10.5" /></svg>',
                ],
                [
                    'label' => 'Laporan Krisis',
                    'route' => 'reports.index',
                    'active' => 'reports.*',
                    'permissions' => [
                        PermissionName::ViewReport->value,
                        PermissionName::CreateReport->value,
                        PermissionName::EditReport->value,
                    ],
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 4h10a2 2 0 012 2v12a2 2 0 01-2 2H8m0-16H6a2 2 0 00-2 2v12a2 2 0 002 2h2m0-16v16m4-12h4m-4 4h4" /></svg>',
                ],
                [
                    'label' => 'Verifikasi',
                    'route' => 'verifications.index',
                    'roles' => [
                        \App\Enums\RoleName::Administrator->value,
                        \App\Enums\RoleName::OperatorLapangan->value,
                        \App\Enums\RoleName::Verifikator->value,
                    ],
                    'permissions' => [PermissionName::VerifyReport->value],
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7" /></svg>',
                ],
                [
                    'label' => 'Penanganan',
                    'route' => 'handling.index',
                    'permissions' => [PermissionName::ManageHandling->value],
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-7 4h8m3-6a8 8 0 11-16 0 8 8 0 0116 0z" /></svg>',
                ],
                [
                    'label' => 'Master Data',
                    'route' => 'admin.master-data',
                    'permissions' => [PermissionName::ManageMasterData->value],
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" /></svg>',
                ],
                [
                    'label' => 'Arsip & Export',
                    'route' => 'archive.index',
                    'permissions' => [PermissionName::ExportData->value],
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4 4m0 0l4-4m-4 4V4m6 4h4m0 0l-4-4m4 4v12" /></svg>',
                ],
                [
                    'label' => 'Audit Log',
                    'route' => 'audit-log.index',
                    'permissions' => [PermissionName::ViewAuditLog->value],
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l2.5 2.5M12 3a9 9 0 100 18 9 9 0 000-18z" /></svg>',
                ],
            ];
        @endphp

        <div
            x-data="{ sidebarOpen: false }"
            class="min-h-screen bg-gray-50"
        >
            <div
                x-show="sidebarOpen"
                class="fixed inset-0 z-30 bg-black/25 md:hidden"
                aria-hidden="true"
                x-transition.opacity
                @click="sidebarOpen = false"
            ></div>

            <aside
                class="fixed inset-y-0 left-0 z-40 w-72 transform bg-white md:hidden"
                x-show="sidebarOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="-translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="-translate-x-full"
                x-cloak
            >
                @include('partials.sidebar', ['navItems' => $navItems])
            </aside>

            <aside class="fixed inset-y-0 left-0 z-30 hidden w-72 md:block">
                @include('partials.sidebar', ['navItems' => $navItems])
            </aside>

            <div class="flex min-h-screen flex-col md:ps-72">
                <header class="sticky top-0 z-20 bg-white/90 backdrop-blur border-b border-gray-100">
                    <div class="flex items-center justify-between px-4 py-3 md:px-6">
                        <div class="flex items-center gap-3">
                            <button
                                type="button"
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-500 hover:bg-gray-100 focus:outline-none md:hidden"
                                @click="sidebarOpen = true"
                                aria-label="Buka menu"
                            >
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" /></svg>
                            </button>

                            <div>
                                <div class="text-lg font-semibold text-gray-900">
                                    {{ $header ?? 'Panel' }}
                                </div>
                                @isset($breadcrumb)
                                    <div class="text-sm text-gray-500">{{ $breadcrumb }}</div>
                                @endisset
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <span class="hidden text-sm text-gray-500 sm:inline">{{ auth()->user()->email }}</span>

                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center rounded-md border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none">
                                        <span class="me-2">{{ Auth::user()->name }}</span>
                                        <svg class="h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 9l6 6 6-6" /></svg>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <x-dropdown-link :href="route('profile.edit')">
                                        {{ __('Profile') }}
                                    </x-dropdown-link>

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf

                                        <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                            {{ __('Log Out') }}
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    </div>
                </header>

                <main class="flex-1 p-4 md:p-6">
                    <div class="mx-auto max-w-6xl">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
