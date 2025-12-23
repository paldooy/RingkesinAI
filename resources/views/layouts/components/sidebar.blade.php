@php
    $menuItems = [
        ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
        ['route' => 'notes.index', 'label' => 'Catatan', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        ['route' => 'summarize.index', 'label' => 'Summarize', 'icon' => 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z'],
        ['route' => 'notes.import.form', 'label' => 'Import', 'icon' => 'M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10'],
        ['route' => 'profile.index', 'label' => 'Profil', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
    ];
@endphp

<!-- Desktop Sidebar (lg and up) -->
<div class="hidden lg:flex w-64 h-screen bg-[#A7C7E7] flex-col">
    <!-- Logo -->
    <div class="p-6">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-[#2C74B3] rounded-xl flex items-center justify-center">
                <span class="text-white font-bold">R</span>
            </div>
            <div>
                <h1 class="text-xl font-bold text-[#1E293B]">Ringkesin</h1>
                <p class="text-xs text-[#1E293B]/70">AI Note Platform</p>
            </div>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 px-3">
        @foreach($menuItems as $item)
            <a href="{{ route($item['route']) }}" 
               class="w-full flex items-center gap-3 px-4 py-3 mb-1 rounded-xl transition-all {{ request()->routeIs($item['route']) ? 'bg-white text-[#2C74B3] shadow-md' : 'text-[#1E293B] hover:bg-white/50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                </svg>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <!-- Logout Button -->
    <div class="p-4">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-[#1E293B] hover:bg-white/50 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span>Logout</span>
            </button>
        </form>
    </div>
</div>

<!-- Mobile Bottom Navigation (below lg) -->
<nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-[#1E293B] border-t border-gray-700 z-50 px-2 pb-safe">
    <div class="flex items-center justify-around h-16">
        @foreach(array_slice($menuItems, 0, 4) as $item)
            <a href="{{ route($item['route']) }}" 
               class="flex flex-col items-center justify-center flex-1 py-2 px-1 transition-all {{ request()->routeIs($item['route']) ? 'text-[#A7C7E7]' : 'text-gray-400 hover:text-gray-200' }}">
                <div class="relative {{ request()->routeIs($item['route']) ? 'bg-[#2C74B3] rounded-full p-2 -mt-3 shadow-lg' : '' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                    </svg>
                </div>
                <span class="text-[10px] mt-1 font-medium">{{ $item['label'] }}</span>
            </a>
        @endforeach
        
        <!-- Profile/More Menu -->
        <a href="{{ route('profile.index') }}" 
           class="flex flex-col items-center justify-center flex-1 py-2 px-1 transition-all {{ request()->routeIs('profile.index') ? 'text-[#A7C7E7]' : 'text-gray-400 hover:text-gray-200' }}">
            <div class="relative {{ request()->routeIs('profile.index') ? 'bg-[#2C74B3] rounded-full p-2 -mt-3 shadow-lg' : '' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <span class="text-[10px] mt-1 font-medium">Profil</span>
        </a>
    </div>
</nav>
