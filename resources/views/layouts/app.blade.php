<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Ringkesin - AI Note Platform')</title>
    
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js for interactive components -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @stack('styles')
</head>
<body class="bg-[#F9FAFB] min-h-screen">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        @include('layouts.components.sidebar')

        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            @yield('content')
        </div>
    </div>

    @stack('scripts')
</body>
</html>
