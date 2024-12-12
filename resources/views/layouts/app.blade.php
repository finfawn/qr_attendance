<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'sQRypt') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link href="{{ asset('assets/bootstrap-5.3.3-dist/css/bootstrap.css') }}" rel="stylesheet">

        <!-- Scripts -->
        <script src="https://unpkg.com/html5-qrcode"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <script src="https://cdn.tailwindcss.com"></script>
        <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
        <script src="https://unpkg.com/qr-scanner/qr-scanner.umd.min.js"></script>
        
        <!-- Tailwind Config -->
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            primary: {"50":"#eff6ff","100":"#dbeafe","200":"#bfdbfe","300":"#93c5fd","400":"#60a5fa","500":"#3b82f6","600":"#2563eb","700":"#1d4ed8","800":"#1e40af","900":"#1e3a8a","950":"#172554"}
                        }
                    }
                }
            }
        </script>

        <!-- Custom Styles -->
        <style type="text/css">
            input[type='text'], input[type='email'], input[type='password'] {
                border-width: 1px;
                border-radius: 0.375rem;
                width: 100%;
                padding: 0.5rem 0.75rem;
                font-size: 1rem;
                line-height: 1.5rem;
                --tw-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
                --tw-shadow-colored: 0 1px 2px 0 var(--tw-shadow-color);
                box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
                border-color: #d1d5db;
            }
            input[type='text']:focus, input[type='email']:focus, input[type='password']:focus {
                outline: 2px solid transparent;
                outline-offset: 2px;
                --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
                --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(1px + var(--tw-ring-offset-width)) var(--tw-ring-color);
                box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000);
                border-color: #6366f1;
                --tw-ring-opacity: 1;
                --tw-ring-color: rgb(99 102 241 / var(--tw-ring-opacity));
            }
            .dark input[type='text'], .dark input[type='email'], .dark input[type='password'] {
                background-color: #111827;
                border-color: #374151;
                color: #d1d5db;
            }
            .dark input[type='text']:focus, .dark input[type='email']:focus, .dark input[type='password']:focus {
                border-color: #818cf8;
                --tw-ring-opacity: 1;
                --tw-ring-color: rgb(129 140 248 / var(--tw-ring-opacity));
            }
        </style>
        
        @stack('scripts')
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-2xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <script src="{{ asset('assets/bootstrap-5.3.3-dist/js/bootstrap.js') }}"></script>
        @stack('modals')
        @stack('scripts')

        @if (session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    showFloatingNotification("{{ session('success') }}", 'success');
                });
            </script>
        @endif

        @if (session('error'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    showFloatingNotification("{{ session('error') }}", 'error');
                });
            </script>
        @endif

        @if (session('warning'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    showFloatingNotification("{{ session('warning') }}", 'warning');
                });
            </script>
        @endif
    </body>
</html>
