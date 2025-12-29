<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Ringkesin - Login')</title>
    
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        
        .auth-background {
            position: relative;
            min-height: 100vh;
            background-image: url('{{ asset('images/background.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        
        .auth-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to left, rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.4));
        }
        
        .auth-content {
            position: relative;
            z-index: 10;
        }
    </style>
</head>
<body class="auth-background flex items-center justify-center p-4">
    <div class="auth-overlay"></div>
    <div class="auth-content">
        @yield('content')
    </div>
</body>
</html>
