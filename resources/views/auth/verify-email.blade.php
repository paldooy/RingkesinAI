<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - Ringkesin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gradient-to-br from-[#E8F1F5] via-white to-[#F0F8FF] min-h-screen">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-md w-full">
            <!-- Logo -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-[#2C74B3] rounded-2xl mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-[#2C74B3]">Verifikasi Email</h1>
                <p class="text-gray-600 mt-2">Cek inbox email Anda untuk melanjutkan</p>
            </div>

            <!-- Main Card -->
            <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                @if(session('success'))
                    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <div class="text-center">
                    <!-- Email Icon -->
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-blue-50 rounded-full mb-6">
                        <svg class="w-10 h-10 text-[#2C74B3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"/>
                        </svg>
                    </div>

                    <h2 class="text-xl font-bold text-gray-800 mb-3">
                        Email Verifikasi Terkirim!
                    </h2>
                    
                    <p class="text-gray-600 mb-2">
                        Kami telah mengirim link verifikasi ke:
                    </p>
                    <p class="text-[#2C74B3] font-semibold text-lg mb-6">
                        {{ session('email') ?? 'email Anda' }}
                    </p>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-6 text-left">
                        <p class="text-sm text-yellow-800 flex items-start gap-2">
                            <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>
                                <strong>Catatan:</strong> Jika tidak menemukan email di inbox, periksa folder <strong>Spam</strong> atau <strong>Promosi</strong>.
                            </span>
                        </p>
                    </div>

                    <!-- Resend Form -->
                    <div x-data="{ sending: false }">
                        <form action="{{ route('verification.resend') }}" method="POST" @submit="sending = true">
                            @csrf
                            <input type="hidden" name="email" value="{{ session('email') }}">
                            
                            <button 
                                type="submit"
                                :disabled="sending"
                                class="w-full px-6 py-3 bg-[#2C74B3] hover:bg-[#205295] disabled:bg-gray-400 text-white rounded-xl transition-colors font-medium flex items-center justify-center gap-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                <span x-show="!sending">Kirim Ulang Email</span>
                                <span x-show="sending" class="flex items-center gap-2">
                                    <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Mengirim...
                                </span>
                            </button>
                        </form>

                        <a 
                            href="{{ route('login') }}" 
                            class="block mt-4 text-gray-600 hover:text-[#2C74B3] transition-colors"
                        >
                            Kembali ke Login
                        </a>
                    </div>
                </div>
            </div>

            <!-- Help Text -->
            <div class="mt-6 text-center text-sm text-gray-600">
                <p>Link verifikasi akan kadaluarsa dalam 24 jam</p>
            </div>
        </div>
    </div>
</body>
</html>
