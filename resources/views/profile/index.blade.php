@extends('layouts.app')

@section('title', 'Profil - Ringkesin')

@section('content')
<div class="flex-1 bg-[#F9FAFB] overflow-auto">
    <div class="max-w-4xl mx-auto p-4 md:p-8">
        <!-- Header -->
        <div class="my-6 sm:mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-[#1E293B] mb-2">Profil Saya</h1>
            <p class="text-xs sm:text-sm text-[#1E293B]/60">
                Kelola informasi akun dan preferensi kamu
            </p>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-600 rounded-xl p-3 sm:p-4 mb-6 text-xs sm:text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-600 rounded-xl p-3 sm:p-4 mb-6">
                <ul class="list-disc list-inside text-xs sm:text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Profile Header Card -->
        <div class="bg-gradient-to-r from-[#2C74B3] to-[#5B8EC9] rounded-2xl p-4 sm:p-6 lg:p-8 mb-6 text-white shadow-lg">
            <div class="flex flex-col sm:flex-row items-center gap-4 sm:gap-6">
                <div class="relative flex-shrink-0">
                    <div class="w-20 sm:w-24 h-20 sm:h-24 rounded-full bg-white/20 backdrop-blur-sm border-4 border-white/30 flex items-center justify-center text-3xl sm:text-4xl font-bold">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full rounded-full object-cover">
                        @else
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        @endif
                    </div>
                    <div class="absolute -bottom-2 -right-2 w-6 sm:w-8 h-6 sm:h-8 bg-green-500 rounded-full border-4 border-white shadow-lg"></div>
                </div>
                <div class="flex-1 text-center sm:text-left">
                    <h2 class="text-lg sm:text-2xl font-bold mb-1 sm:mb-2 break-words">{{ $user->name }}</h2>
                    <p class="text-white/80 text-xs sm:text-base break-all">{{ $user->email }}</p>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 gap-3 sm:gap-6 mb-6">
            <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-sm border border-[#E5E7EB]">
                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="w-10 sm:w-12 h-10 sm:h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 sm:w-6 h-5 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs sm:text-sm text-[#1E293B]/60 mb-1">Total Catatan</p>
                        <p class="text-lg sm:text-2xl font-bold text-[#1E293B]">{{ $totalNotes }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-sm border border-[#E5E7EB]">
                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="w-10 sm:w-12 h-10 sm:h-12 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 sm:w-6 h-5 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs sm:text-sm text-[#1E293B]/60 mb-1">Kategori</p>
                        <p class="text-lg sm:text-2xl font-bold text-[#1E293B]">{{ $totalCategories }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Profile Form -->
        <div class="bg-white rounded-2xl p-4 sm:p-6 lg:p-8 shadow-sm border border-[#E5E7EB]">
            <h2 class="text-lg sm:text-2xl font-bold text-[#1E293B] mb-4 sm:mb-6">Edit Profil</h2>
            
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-4 sm:space-y-6">
                @csrf
                @method('PUT')

                <!-- Avatar Upload -->
                <div x-data="{ 
                    imagePreview: '{{ $user->avatar ? asset('storage/' . $user->avatar) : '' }}',
                    hasAvatar: {{ $user->avatar ? 'true' : 'false' }}
                }">
                    <label class="block text-xs sm:text-sm font-medium text-[#1E293B] mb-2 sm:mb-3">
                        Foto Profil
                    </label>
                    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-3 sm:gap-4">
                        <div class="w-16 sm:w-20 h-16 sm:h-20 rounded-full bg-[#F9FAFB] border-2 border-[#E5E7EB] flex items-center justify-center text-xl sm:text-2xl font-bold overflow-hidden flex-shrink-0">
                            <img 
                                x-show="imagePreview" 
                                :src="imagePreview" 
                                alt="Avatar" 
                                class="w-full h-full object-cover"
                                x-on:error="imagePreview = ''; hasAvatar = false"
                            >
                            <span 
                                x-show="!imagePreview" 
                                class="text-[#2C74B3]"
                            >{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                        </div>
                        <div class="w-full sm:w-auto flex flex-col items-center sm:items-start">
                            <input 
                                type="file" 
                                name="avatar" 
                                id="avatar"
                                accept="image/*"
                                @change="if($event.target.files.length > 0) { imagePreview = URL.createObjectURL($event.target.files[0]); hasAvatar = true; }"
                                class="hidden"
                            />
                            <label for="avatar" class="inline-block bg-[#2C74B3] hover:bg-[#205295] text-white px-3 sm:px-4 py-2 rounded-lg cursor-pointer transition-colors text-xs sm:text-sm">
                                Upload Foto
                            </label>
                            <p class="text-xs text-[#1E293B]/50 mt-1">
                                JPG, PNG, max 2MB
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Name -->
                <div>
                    <label for="name" class="block text-xs sm:text-sm font-medium text-[#1E293B] mb-2">
                        Nama Lengkap
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name', $user->name) }}"
                        required
                        class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm rounded-xl border border-gray-300 focus:border-[#2C74B3] focus:ring-2 focus:ring-[#2C74B3]/20 outline-none transition"
                    />
                </div>

                <!-- Email (Display Only - Edit Separately) -->
                <div x-data>
                    <label class="block text-xs sm:text-sm font-medium text-[#1E293B] mb-2">
                        Email <span class="text-xs text-gray-500">(untuk mengubah email, klik tombol di bawah)</span>
                    </label>
                    <div class="flex items-center gap-2 sm:gap-3">
                        <input 
                            type="email" 
                            value="{{ $user->email }}"
                            disabled
                            class="flex-1 min-w-0 px-3 sm:px-4 py-2 sm:py-3 text-sm rounded-xl border border-gray-300 bg-gray-50 text-gray-500 cursor-not-allowed"
                        />
                        <button 
                            type="button"
                            @click="$dispatch('open-email-modal')"
                            class="px-3 sm:px-4 py-2 sm:py-3 bg-[#2C74B3] hover:bg-[#205295] text-white rounded-xl transition-colors font-medium whitespace-nowrap flex items-center justify-center gap-2 text-xs sm:text-sm"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                            Ubah Email
                        </button>
                    </div>
                    <div class="mt-2 bg-yellow-50 border border-yellow-200 rounded-lg p-2 sm:p-3">
                        <p class="text-xs text-yellow-800 flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span>
                                <strong>Keamanan Berlapis:</strong> Perubahan email memerlukan verifikasi kode OTP dari email lama dan konfirmasi link dari email baru. Pastikan Anda memiliki akses ke kedua email.
                            </span>
                        </p>
                    </div>
                </div>

                <!-- Bio -->
                <div>
                    <label for="bio" class="block text-xs sm:text-sm font-medium text-[#1E293B] mb-2">
                        Bio
                    </label>
                    <textarea 
                        id="bio" 
                        name="bio" 
                        rows="4"
                        placeholder="Ceritakan sedikit tentang kamu..."
                        class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm rounded-xl border border-gray-300 focus:border-[#2C74B3] focus:ring-2 focus:ring-[#2C74B3]/20 outline-none transition resize-none"
                    >{{ old('bio', $user->bio) }}</textarea>
                </div>

                <!-- Submit Button -->
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 pt-4">
                    <button 
                        type="submit"
                        class="bg-[#2C74B3] hover:bg-[#205295] text-white font-medium px-4 sm:px-8 py-2 sm:py-3 rounded-xl transition-colors flex items-center justify-center gap-2 text-sm sm:text-base"
                    >
                        <svg class="w-4 sm:w-5 h-4 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Perubahan
                    </button>
                    <a 
                        href="{{ route('dashboard') }}"
                        class="border border-[#E5E7EB] hover:bg-[#F9FAFB] text-[#1E293B] font-medium px-4 sm:px-8 py-2 sm:py-3 rounded-xl transition-colors text-sm sm:text-base text-center"
                    >
                        Batal
                    </a>
                </div>
            </form>
        </div>

        <!-- Change Password Section -->
        <div class="bg-white rounded-2xl p-4 sm:p-6 lg:p-8 shadow-sm border border-[#E5E7EB] mt-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-3 mb-4 sm:mb-6">
                <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg sm:text-2xl font-bold text-[#1E293B]">Ubah Password</h2>
                    <p class="text-xs sm:text-sm text-[#1E293B]/60">Pastikan menggunakan password yang kuat dan unik</p>
                </div>
            </div>
            
            <form method="POST" action="{{ route('profile.password') }}" class="space-y-4 sm:space-y-6">
                @csrf
                @method('PUT')

                <!-- Current Password -->
                <div x-data="{ showCurrentPassword: false }">
                    <label for="current_password" class="block text-xs sm:text-sm font-medium text-[#1E293B] mb-2">
                        Password Saat Ini <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            :type="showCurrentPassword ? 'text' : 'password'"
                            id="current_password" 
                            name="current_password" 
                            required
                            autocomplete="off"
                            readonly
                            onfocus="this.removeAttribute('readonly');"
                            placeholder="Masukkan password saat ini"
                            class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm pr-10 rounded-xl border border-gray-300 focus:border-[#2C74B3] focus:ring-2 focus:ring-[#2C74B3]/20 outline-none transition"
                        />
                        <button 
                            type="button"
                            @click="showCurrentPassword = !showCurrentPassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                        >
                            <svg x-show="!showCurrentPassword" class="w-4 sm:w-5 h-4 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showCurrentPassword" class="w-4 sm:w-5 h-4 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    @error('current_password')
                        <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- New Password -->
                <div x-data="{ showNewPassword: false }">
                    <label for="new_password" class="block text-xs sm:text-sm font-medium text-[#1E293B] mb-2">
                        Password Baru <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            :type="showNewPassword ? 'text' : 'password'"
                            id="new_password" 
                            name="new_password" 
                            required
                            autocomplete="off"                            readonly
                            onfocus="this.removeAttribute('readonly');"                            placeholder="Minimal 8 karakter"
                            class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm pr-10 rounded-xl border border-gray-300 focus:border-[#2C74B3] focus:ring-2 focus:ring-[#2C74B3]/20 outline-none transition"
                        />
                        <button 
                            type="button"
                            @click="showNewPassword = !showNewPassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                        >
                            <svg x-show="!showNewPassword" class="w-4 sm:w-5 h-4 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showNewPassword" class="w-4 sm:w-5 h-4 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    <p class="text-xs text-[#1E293B]/50 mt-1">
                        Gunakan kombinasi huruf, angka, dan simbol untuk keamanan maksimal
                    </p>
                    @error('new_password')
                        <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm New Password -->
                <div x-data="{ showConfirmPassword: false }">
                    <label for="new_password_confirmation" class="block text-xs sm:text-sm font-medium text-[#1E293B] mb-2">
                        Konfirmasi Password Baru <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            :type="showConfirmPassword ? 'text' : 'password'"
                            id="new_password_confirmation" 
                            name="new_password_confirmation" 
                            required
                            autocomplete="off"                            readonly
                            onfocus="this.removeAttribute('readonly');"                            placeholder="Ketik ulang password baru"
                            class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm pr-10 rounded-xl border border-gray-300 focus:border-[#2C74B3] focus:ring-2 focus:ring-[#2C74B3]/20 outline-none transition"
                        />
                        <button 
                            type="button"
                            @click="showConfirmPassword = !showConfirmPassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                        >
                            <svg x-show="!showConfirmPassword" class="w-4 sm:w-5 h-4 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showConfirmPassword" class="w-4 sm:w-5 h-4 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Info Alert -->
                <div class="bg-blue-50 border-l-4 border-blue-500 p-3 sm:p-4 rounded-lg">
                    <div class="flex items-start gap-2 sm:gap-3">
                        <svg class="w-4 sm:w-5 h-4 sm:h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="text-xs sm:text-sm text-blue-800">
                            <p class="font-medium mb-1">Tips Keamanan Password:</p>
                            <ul class="list-disc list-inside space-y-0.5 text-xs">
                                <li>Minimal 8 karakter</li>
                                <li>Kombinasi huruf besar dan kecil</li>
                                <li>Sertakan angka dan karakter khusus (@, #, $, dll)</li>
                                <li>Jangan gunakan password yang sama dengan akun lain</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 pt-4 border-t border-gray-200">
                    <button 
                        type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white font-medium px-4 sm:px-8 py-2 sm:py-3 rounded-xl transition-colors flex items-center justify-center gap-2 text-sm sm:text-base"
                    >
                        <svg class="w-4 sm:w-5 h-4 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Ubah Password
                    </button>
                    <button 
                        type="reset"
                        class="border border-[#E5E7EB] hover:bg-[#F9FAFB] text-[#1E293B] font-medium px-4 sm:px-8 py-2 sm:py-3 rounded-xl transition-colors text-sm sm:text-base"
                    >
                        Reset
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Change Email Modal -->
    <div 
        x-data="{ 
            showEmailModal: false,
            step: 'request',
            newEmail: '',
            otpCode: '',
            verifying: false,
            error: '',
            success: ''
        }"
        @open-email-modal.window="showEmailModal = true; step = 'request'; error = ''; success = ''"
        x-show="showEmailModal"
        x-cloak
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
        @click.self="showEmailModal = false"
    >
        <div class="bg-white rounded-2xl max-w-md w-full p-4 sm:p-6" @click.stop>
            <h2 class="text-lg sm:text-2xl font-bold text-[#1E293B] mb-2">Ubah Email</h2>
            <p class="text-xs sm:text-sm text-[#1E293B]/60 mb-6">
                Verifikasi diperlukan untuk keamanan akun Anda
            </p>

            <!-- Step 1: Request Email Change -->
            <div x-show="step === 'request'">
                <form @submit.prevent="
                    verifying = true;
                    error = '';
                    fetch('{{ route('profile.email.request') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ new_email: newEmail })
                    })
                    .then(r => r.json())
                    .then(data => {
                        verifying = false;
                        if(data.success) {
                            step = 'verify';
                        } else {
                            error = data.error || 'Terjadi kesalahan';
                        }
                    })
                    .catch(e => {
                        verifying = false;
                        error = 'Terjadi kesalahan koneksi';
                    })
                " class="space-y-3 sm:space-y-4">
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-[#1E293B] mb-2">
                            Email Saat Ini
                        </label>
                        <input 
                            type="email" 
                            value="{{ $user->email }}"
                            disabled
                            class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm rounded-xl border border-gray-300 bg-gray-50 text-gray-500"
                        />
                    </div>

                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-[#1E293B] mb-2">
                            Email Baru <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="email" 
                            x-model="newEmail"
                            required
                            autocomplete="off"
                            placeholder="email.baru@example.com"
                            class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm rounded-xl border border-gray-300 focus:border-[#2C74B3] focus:ring-2 focus:ring-[#2C74B3]/20 outline-none transition"
                        />
                    </div>

                    <div x-show="error" class="bg-red-50 border border-red-200 text-red-600 rounded-xl p-2 sm:p-3 text-xs sm:text-sm" x-text="error"></div>

                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-2 sm:p-3 text-xs sm:text-sm text-blue-700">
                        <p class="font-medium mb-1">📧 Proses Verifikasi:</p>
                        <ol class="list-decimal list-inside space-y-1 text-xs">
                            <li>Kode OTP akan dikirim ke email BARU yang Anda masukkan</li>
                            <li>Masukkan kode OTP untuk memverifikasi email tersebut</li>
                            <li>Email akun Anda akan langsung berubah setelah verifikasi</li>
                        </ol>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2 pt-4">
                        <button 
                            type="submit"
                            :disabled="verifying || !newEmail"
                            class="flex-1 bg-[#2C74B3] hover:bg-[#205295] disabled:bg-gray-300 text-white font-medium py-2 sm:py-3 rounded-xl transition-colors text-sm"
                        >
                            <span x-show="!verifying">Kirim Kode Verifikasi</span>
                            <span x-show="verifying">Mengirim...</span>
                        </button>
                        <button 
                            type="button"
                            @click="showEmailModal = false"
                            class="px-4 sm:px-6 border border-[#E5E7EB] hover:bg-[#F9FAFB] text-[#1E293B] font-medium py-2 sm:py-3 rounded-xl transition-colors text-sm"
                        >
                            Batal
                        </button>
                    </div>
                </form>
            </div>

            <!-- Step 2: Verify OTP -->
            <div x-show="step === 'verify'">
                <form @submit.prevent="
                    verifying = true;
                    error = '';
                    fetch('{{ route('profile.email.verify') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ 
                            new_email: newEmail,
                            otp_code: otpCode 
                        })
                    })
                    .then(r => r.json())
                    .then(data => {
                        verifying = false;
                        if(data.success) {
                            success = data.message;
                            setTimeout(() => location.reload(), 2000);
                        } else {
                            error = data.error || 'Kode OTP tidak valid';
                        }
                    })
                    .catch(e => {
                        verifying = false;
                        error = 'Terjadi kesalahan koneksi';
                    })
                " class="space-y-3 sm:space-y-4">
                    <div class="bg-green-50 border border-green-200 rounded-xl p-2 sm:p-4 text-xs sm:text-sm text-green-700">
                        <p class="font-medium mb-1">✅ Kode verifikasi telah dikirim!</p>
                        <p class="text-xs">
                            Cek email baru Anda (<span x-text="newEmail"></span>) untuk mendapatkan kode OTP.
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-[#1E293B] mb-2">
                            Kode OTP (dari email baru) <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            x-model="otpCode"
                            required
                            maxlength="6"
                            placeholder="000000"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#2C74B3] focus:ring-2 focus:ring-[#2C74B3]/20 outline-none transition text-center text-xl sm:text-2xl font-mono tracking-widest"
                        />
                        <p class="text-xs text-[#1E293B]/60 mt-2">
                            Masukkan kode 6 digit yang dikirim ke email baru Anda
                        </p>
                    </div>

                    <div x-show="error" class="bg-red-50 border border-red-200 text-red-600 rounded-xl p-2 sm:p-3 text-xs sm:text-sm" x-text="error"></div>
                    <div x-show="success" class="bg-green-50 border border-green-200 text-green-600 rounded-xl p-2 sm:p-3 text-xs sm:text-sm" x-text="success"></div>

                    <div class="flex flex-col sm:flex-row gap-2 pt-4">
                        <button 
                            type="submit"
                            :disabled="verifying || otpCode.length !== 6"
                            class="flex-1 bg-[#2C74B3] hover:bg-[#205295] disabled:bg-gray-300 text-white font-medium py-2 sm:py-3 rounded-xl transition-colors text-sm"
                        >
                            <span x-show="!verifying">Verifikasi & Ubah Email</span>
                            <span x-show="verifying">Memverifikasi...</span>
                        </button>
                        <button 
                            type="button"
                            @click="step = 'request'; otpCode = ''"
                            class="px-4 sm:px-6 border border-[#E5E7EB] hover:bg-[#F9FAFB] text-[#1E293B] font-medium py-2 sm:py-3 rounded-xl transition-colors text-sm"
                        >
                            Kembali
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush
