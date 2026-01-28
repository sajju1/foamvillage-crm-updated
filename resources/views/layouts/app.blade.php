<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Foam Village CRM') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>


</head>


    </div>
    <div class="min-h-screen flex">

        {{-- Sidebar --}}
        @include('layouts.sidebar')

        {{-- Main content --}}
        <div class="flex-1 flex flex-col">

            {{-- Navbar --}}
            @include('layouts.navbar')

            {{-- Page Content --}}
            <main class="flex-1 p-6">

                {{-- Global Alerts --}}
                @include('components.alert')

                @yield('content')

            </main>

        </div>
    </div>
    @stack('scripts')
    <script>
        function openAddProductModal() {
            document.getElementById('addProductModal').style.display = 'flex';
        }

        function closeAddProductModal() {
            document.getElementById('addProductModal').style.display = 'none';
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


</body>

</html>
