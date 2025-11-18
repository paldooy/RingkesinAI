@extends('layouts.auth')

@section('title', 'Login - Ringkesin')

@section('content')
<div class="w-full max-w-md">
    <!-- Logo and Title -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-2xl shadow-lg mb-4">
            <span class="text-[#2C74B3] text-2xl font-bold">R</span>
        </div>
        <h1 class="text-3xl font-bold text-white mb-2">Ringkesin</h1>
        <p class="text-white/80 text-sm">Catat, ringkas, dan pahami pelajaranmu lebih cepat</p>
    </div>

    <!-- Login Card -->
    <div class="bg-white rounded-2xl shadow-xl p-8">
        <h2 class="text-2xl font-bold text-[#1E293B] mb-6">Masuk</h2>
        
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-600 rounded-xl p-4 mb-4 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf
            
            <div>
                <label for="email" class="block text-sm font-medium text-[#1E293B] mb-2">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email') }}"
                    placeholder="nama@email.com"
                    required
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#2C74B3] focus:ring-2 focus:ring-[#2C74B3]/20 outline-none transition"
                />
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-[#1E293B] mb-2">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Masukkan password"
                    required
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#2C74B3] focus:ring-2 focus:ring-[#2C74B3]/20 outline-none transition"
                />
            </div>

            <div class="flex items-center gap-2">
                <input 
                    type="checkbox" 
                    id="remember" 
                    name="remember"
                    class="w-4 h-4 rounded border-gray-300 text-[#2C74B3] focus:ring-[#2C74B3]"
                />
                <label for="remember" class="text-sm text-[#1E293B] cursor-pointer">
                    Ingat saya
                </label>
            </div>

            <button 
                type="submit"
                class="w-full bg-[#2C74B3] hover:bg-[#205295] text-white font-medium py-3 rounded-xl transition-colors"
            >
                Masuk
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-sm text-[#1E293B]/70">
                Belum punya akun? 
                <a href="{{ route('register') }}" class="text-[#2C74B3] hover:underline font-medium">
                    Daftar sekarang
                </a>
            </p>
        </div>
    </div>

    <p class="text-center text-white/60 text-xs mt-6">
        © 2025 Ringkesin. Platform Pencatatan Belajar AI
    </p>
</div>
@endsection
