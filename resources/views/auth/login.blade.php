@extends('layouts.auth')

@section('title', 'Login - Ringkesin')

@section('content')
<div class="w-full max-w-xl mx-auto md:min-w-[28rem]">
    <!-- Logo -->
    <div class="text-center">
        <div class="inline-flex items-center justify-center">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-40 w-auto">
        </div>
    </div>

    <!-- Login Card -->
    <div class="bg-white/15 backdrop-blur-xl border border-white/25 rounded-2xl shadow-2xl p-8 text-white">
        <h2 class="text-2xl font-bold text-white mb-6">Masuk</h2>
        
        @if ($errors->any())
            <div class="bg-red-500/15 border border-red-200/40 text-red-100 rounded-xl p-4 mb-4 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('info'))
            <div class="bg-blue-500/15 border border-blue-200/40 text-white rounded-xl p-4 mb-4 text-sm flex items-start gap-3">
                <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('info') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf
            
            <div>
                <label for="email" class="block text-sm font-medium text-white mb-2">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email') }}"
                    placeholder="nama@email.com"
                    required
                    class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/30 text-white placeholder-white/70 focus:border-white/70 focus:ring-2 focus:ring-white/30 outline-none transition"
                />
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-white mb-2">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Masukkan password"
                    required
                    class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/30 text-white placeholder-white/70 focus:border-white/70 focus:ring-2 focus:ring-white/30 outline-none transition"
                />
            </div>

            <div class="flex items-center gap-2">
                <input 
                    type="checkbox" 
                    id="remember" 
                    name="remember"
                    class="w-4 h-4 rounded border-white/60 bg-white/10 text-[#3B82F6] focus:ring-[#93C5FD]"
                />
                <label for="remember" class="text-sm text-white cursor-pointer">
                    Ingat saya
                </label>
            </div>

            <button 
                type="submit"
                class="w-full bg-[#3B82F6] hover:bg-[#2563EB] text-white font-semibold py-3 rounded-xl transition-colors"
            >
                Masuk
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-sm text-white">
                Belum punya akun? 
                <a href="{{ route('register') }}" class="text-[#93C5FD] hover:text-white underline-offset-4 hover:underline font-semibold">
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
