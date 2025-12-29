@extends('layouts.auth')

@section('title', 'Register - Ringkesin')

@section('content')
<div class="w-full max-w-xl mx-auto md:min-w-[28rem]">
    <!-- Logo -->
    <div class="text-center">
        <div class="inline-flex items-center justify-center">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-40 w-auto">
        </div>
    </div>

    <!-- Register Card -->
        <div class="bg-white/15 backdrop-blur-xl border border-white/25 rounded-2xl shadow-2xl p-8 text-white" x-data="{ 
        step: 'form',
        email: '{{ old('email') }}',
        otpSent: false,
        otpVerified: false,
        countdown: 0,
        sendingOtp: false,
        error: '',
        success: ''
    }">
        <h2 class="text-2xl font-bold text-white mb-6">Daftar Akun Baru</h2>
        
        @if ($errors->any())
            <div class="bg-red-500/15 border border-red-200/40 text-red-100 rounded-xl p-4 mb-4 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf
            
            <div>
                <label for="name" class="block text-sm font-medium text-white mb-2">Nama Lengkap</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name') }}"
                    placeholder="Nama lengkap kamu"
                    required
                    class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/30 text-white placeholder-white/70 focus:border-white/70 focus:ring-2 focus:ring-white/30 outline-none transition"
                />
            </div>

            <div>
                    <label for="email" class="block text-sm font-medium text-white mb-2">
                    Email <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-2">
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        x-model="email"
                        value="{{ old('email') }}"
                        placeholder="nama@email.com"
                        required
                        :readonly="otpSent"
                        class="flex-1 px-4 py-3 rounded-xl bg-white/10 border border-white/30 text-white placeholder-white/70 focus:border-white/70 focus:ring-2 focus:ring-white/30 outline-none transition read-only:bg-white/5 read-only:cursor-not-allowed"
                    />
                    <button 
                        type="button"
                        @click="
                            if(!email || sendingOtp || countdown > 0) return;
                            sendingOtp = true;
                            error = '';
                            fetch('{{ route('register.send-otp') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ email: email })
                            })
                            .then(r => r.json())
                            .then(data => {
                                sendingOtp = false;
                                if(data.success) {
                                    otpSent = true;
                                    success = data.message;
                                    countdown = 600;
                                    const interval = setInterval(() => {
                                        countdown--;
                                        if(countdown <= 0) clearInterval(interval);
                                    }, 1000);
                                } else {
                                    error = data.error || 'Gagal mengirim OTP';
                                }
                            })
                            .catch(e => {
                                sendingOtp = false;
                                error = 'Gagal mengirim OTP. Periksa koneksi internet Anda.';
                            })
                        "
                        :disabled="!email || sendingOtp || countdown > 0 || otpSent"
                        class="px-4 py-3 bg-[#2C74B3] hover:bg-[#205295] disabled:bg-gray-300 text-white rounded-xl transition-colors whitespace-nowrap text-sm font-medium"
                    >
                        <span x-show="!sendingOtp && !otpSent && countdown === 0">Kirim OTP</span>
                        <span x-show="sendingOtp">Mengirim...</span>
                        <span x-show="otpSent && countdown > 0" x-text="Math.floor(countdown / 60) + ':' + String(countdown % 60).padStart(2, '0')"></span>
                        <span x-show="otpSent && countdown === 0">Kirim Ulang</span>
                    </button>
                </div>
                <p class="text-xs text-amber-200 mt-1.5 flex items-start gap-1.5">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span><strong>Penting:</strong> Gunakan email <strong>AKTIF dan TERDAFTAR</strong> (Gmail/Yahoo/Outlook). OTP hanya dikirim ke email yang valid.</span>
                </p>
                <div x-show="error" class="mt-2 bg-red-500/15 border border-red-200/40 text-red-100 rounded-lg p-2 text-xs" x-text="error"></div>
                <div x-show="success" class="mt-2 bg-emerald-500/15 border border-emerald-200/40 text-emerald-100 rounded-lg p-2 text-xs" x-text="success"></div>
            </div>

            <div x-show="otpSent">
                <label for="otp_code" class="block text-sm font-medium text-white mb-2">
                    Kode OTP <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="otp_code" 
                    name="otp_code" 
                    maxlength="6"
                    placeholder="000000"
                    :required="otpSent"
                    class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/30 text-white placeholder-white/70 focus:border-white/70 focus:ring-2 focus:ring-white/30 outline-none transition text-center text-2xl font-mono tracking-widest"
                />
                <p class="text-xs text-white/80 mt-1.5 text-center">
                    Masukkan kode 6 digit yang dikirim ke email Anda
                </p>
            </div>

            <div>
                    <label for="password" class="block text-sm font-medium text-white mb-2">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Buat password"
                    required
                    class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/30 text-white placeholder-white/70 focus:border-white/70 focus:ring-2 focus:ring-white/30 outline-none transition"
                />
            </div>

            <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-white mb-2">Konfirmasi Password</label>
                <input 
                    type="password" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    placeholder="Ketik ulang password"
                    required
                    class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/30 text-white placeholder-white/70 focus:border-white/70 focus:ring-2 focus:ring-white/30 outline-none transition"
                />
            </div>

            <div x-show="!otpSent" class="bg-blue-500/15 border-l-4 border-blue-200/50 rounded-lg p-4 text-white">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <div class="text-sm text-white">
                        <p class="font-semibold mb-1">🔒 Verifikasi Email Wajib</p>
                        <ul class="list-disc list-inside space-y-0.5 text-xs text-white/90">
                            <li>Klik "Kirim OTP" untuk mendapat kode verifikasi</li>
                            <li>Cek inbox atau folder spam email Anda</li>
                            <li>Masukkan kode OTP yang diterima</li>
                            <li>Kode berlaku 10 menit</li>
                        </ul>
                    </div>
                </div>
            </div>

            <button 
                type="submit"
                :disabled="!otpSent"
                class="w-full bg-[#3B82F6] hover:bg-[#2563EB] disabled:bg-gray-300/60 disabled:cursor-not-allowed text-white font-semibold py-3 rounded-xl transition-colors"
            >
                <span x-show="!otpSent">Kirim OTP Dulu untuk Melanjutkan</span>
                <span x-show="otpSent">Daftar Sekarang</span>
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-sm text-white/80">
                Sudah punya akun? 
                <a href="{{ route('login') }}" class="text-white font-semibold underline-offset-4 hover:underline">
                    Masuk di sini
                </a>
            </p>
        </div>
    </div>

    <p class="text-center text-white/60 text-xs mt-6">
        © 2025 Ringkesin. Platform Pencatatan Belajar AI
    </p>
</div>
@endsection
