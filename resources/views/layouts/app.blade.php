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
        <style>
            /* === Minimal CSS Framework (Fallback/Utility) === */
            
            /* 1. Reset & Base */
            *, *::before, *::after { box-sizing: border-box; }
            /* Note: Body font is handled by Tailwind classes usually, but here's a fallback */
            
            /* 2. Components: Card */
            .card { 
                background-color: white; 
                border-radius: 0.75rem; 
                border: 1px solid #f3f4f6; 
                box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05); 
                overflow: hidden; 
            }

            /* 3. Components: Buttons */
            .btn { 
                display: inline-flex; 
                align-items: center; 
                justify-content: center; 
                padding: 0.5rem 1rem; 
                border-radius: 0.5rem; 
                font-size: 0.875rem; 
                font-weight: 600; 
                text-transform: uppercase; 
                letter-spacing: 0.025em; 
                transition: all 150ms; 
                cursor: pointer; 
                border: 1px solid transparent;
            }
            .btn-primary { background-color: #4f46e5; color: white; } 
            .btn-primary:hover { background-color: #4338ca; }
            .btn-primary:disabled { opacity: 0.7; cursor: not-allowed; }
            
            .btn-danger { background-color: #ef4444; color: white; } 
            .btn-danger:hover { background-color: #dc2626; }
            
            .btn-secondary { background-color: white; border: 1px solid #d1d5db; color: #374151; } 
            .btn-secondary:hover { background-color: #f9fafb; color: #111827; }

            /* 4. Components: Badge */
            .badge { 
                display: inline-flex; 
                align-items: center; 
                padding: 0.125rem 0.625rem; 
                border-radius: 9999px; 
                font-size: 0.75rem; 
                font-weight: 700; 
                text-transform: uppercase;
            }
            .badge-baru { background-color: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; }
            .badge-proses { background-color: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
            .badge-selesai { background-color: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
            .badge-ditutup { background-color: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

            /* 5. Components: Tabs */
            .tabs { display: flex; border-bottom: 2px solid #f3f4f6; gap: 1.5rem; margin-bottom: 1.5rem; }
            .tab-link { 
                padding: 1rem 0.25rem; 
                font-size: 0.875rem; 
                font-weight: 500; 
                color: #6b7280; 
                border-bottom: 2px solid transparent; 
                margin-bottom: -2px; 
                text-decoration: none; 
            }
            .tab-link:hover { color: #374151; border-color: #d1d5db; }
            .tab-active { color: #4f46e5; border-color: #4f46e5; }
            
            /* 6. Components: Timeline (Pure CSS version if needed) */
            .timeline { position: relative; padding-left: 1rem; }
            .timeline-item { position: relative; padding-bottom: 2rem; border-left: 2px solid #e5e7eb; padding-left: 2rem; }
            .timeline-item:last-child { border-left-color: transparent; }
            .timeline-icon { 
                position: absolute; 
                left: -0.6rem; 
                top: 0; 
                width: 1.25rem; 
                height: 1.25rem; 
                border-radius: 50%; 
                background: white; 
                border: 2px solid #e5e7eb; 
                z-index: 10;
            }

            /* 7. Components: Alert */
            .alert { padding: 1rem; border-radius: 0.5rem; border: 1px solid transparent; margin-bottom: 1rem; font-size: 0.875rem; }
            .alert-success { background-color: #ecfdf5; color: #065f46; border-color: #a7f3d0; }
            .alert-error { background-color: #fef2f2; color: #991b1b; border-color: #fecaca; }
            .alert-info { background-color: #eff6ff; color: #1e40af; border-color: #bfdbfe; }

            /* 8. Responsive Grid Helpers (Simplified) */
            .grid-stack-mobile { display: grid; grid-template-columns: 1fr; gap: 1rem; }
            @media (min-width: 1024px) {
                .grid-stack-mobile { grid-template-columns: repeat(4, 1fr); }
            }

            /* 9. Forms */
            .input, .form-input { 
                display: block; 
                width: 100%; 
                border-radius: 0.5rem; 
                border: 1px solid #d1d5db; 
                padding: 0.5rem 0.75rem; 
                box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05); 
                transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            }
            .input:focus, .form-input:focus { outline: none; border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1); }
            
            /* Animation Utility */
            .animate-fade-in { animation: fadeIn 0.3s ease-out forwards; }
            @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
        </style>

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
                            <!-- Notifications Bell -->
                            <a href="{{ route('notifications.index') }}" class="relative p-2 text-gray-500 hover:bg-gray-100 rounded-md transition duration-150">
                                <span class="sr-only">Notifikasi</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                </svg>
                                @if(auth()->user()->unreadNotifications->isNotEmpty())
                                    <span class="absolute top-1.5 right-1.5 h-2.5 w-2.5 rounded-full bg-red-500 ring-2 ring-white"></span>
                                @endif
                            </a>
                            
                            <span class="hidden text-sm text-gray-500 sm:inline border-l border-gray-200 pl-3 ml-1">{{ auth()->user()->email }}</span>

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
                        <!-- Global Flash Messages -->
                        @if(session('success') || session('error') || session('warning') || session('info') || $errors->any())
                            <div class="mb-6 space-y-2" x-data="{ show: true }" x-show="show">
                                @if(session('success'))
                                    <div class="flex items-center justify-between rounded-lg border border-green-200 bg-green-50 p-4 text-green-700 shadow-sm">
                                        <div class="flex items-center">
                                            <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            {{ session('success') }}
                                        </div>
                                        <button @click="show = false" class="text-green-500 hover:text-green-700"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                                    </div>
                                @endif

                                @if(session('error'))
                                    <div class="flex items-center justify-between rounded-lg border border-red-200 bg-red-50 p-4 text-red-700 shadow-sm">
                                        <div class="flex items-center">
                                            <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ session('error') }}
                                        </div>
                                        <button @click="show = false" class="text-red-500 hover:text-red-700"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                                    </div>
                                @endif

                                @if($errors->any())
                                    <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-700 shadow-sm">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="font-semibold flex items-center">
                                                <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                Terdapat kesalahan input:
                                            </span>
                                            <button @click="show = false" class="text-red-500 hover:text-red-700"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                                        </div>
                                        <ul class="list-inside list-disc text-sm ml-7">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
