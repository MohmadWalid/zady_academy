<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zady Academy - @yield('title', 'الإدارة')</title>
    
    <!-- Google Fonts: IBM Plex Sans Arabic -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bg text-text-primary font-sans antialiased">

    <!-- Mobile Bottom Navigation (< 480px) -->
    <div class="block sm:hidden">
        <x-nav-bottom-bar />
    </div>

    <div class="flex h-screen overflow-hidden">
        <!-- Desktop/Tablet Side Drawer (>= 480px) -->
        <div class="hidden sm:block">
            <x-nav-side-drawer />
        </div>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-8 pb-24 sm:pb-8">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
