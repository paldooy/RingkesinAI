@extends('layouts.app')

@section('title', 'Profil - Ringkesin')

@section('content')
<div class="flex-1 bg-[#F9FAFB] overflow-auto">
    <div class="max-w-4xl mx-auto p-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-[#1E293B] mb-2">Profil Saya</h1>
            <p class="text-sm text-[#1E293B]/60">
                Kelola informasi akun dan preferensi kamu
            </p>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-600 rounded-xl p-4 mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-600 rounded-xl p-4 mb-6">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Profile Header Card -->
        <div class="bg-gradient-to-r from-[#2C74B3] to-[#5B8EC9] rounded-2xl p-8 mb-6 text-white shadow-lg">
            <div class="flex items-center gap-6">
                <div class="relative">
                    <div class="w-24 h-24 rounded-full bg-white/20 backdrop-blur-sm border-4 border-white/30 flex items-center justify-center text-4xl font-bold">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full rounded-full object-cover">
                        @else
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        @endif
                    </div>
                    <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-green-500 rounded-full border-4 border-white shadow-lg"></div>
                </div>
                <div class="flex-1">
                    <h2 class="text-2xl font-bold mb-2">{{ $user->name }}</h2>
                    <p class="text-white/80 mb-3">{{ $user->email }}</p>
                    <div class="flex items-center gap-4 text-sm">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Bergabung {{ $daysJoined }} hari yang lalu
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB]">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-[#1E293B]/60 mb-1">Total Catatan</p>
                        <p class="text-2xl font-bold text-[#1E293B]">{{ $totalNotes }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB]">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-[#1E293B]/60 mb-1">Kategori</p>
                        <p class="text-2xl font-bold text-[#1E293B]">{{ $totalCategories }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB]">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-[#1E293B]/60 mb-1">Pencapaian</p>
                        <p class="text-2xl font-bold text-[#1E293B]">🏆 Pemula</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Profile Form -->
        <div class="bg-white rounded-2xl p-8 shadow-sm border border-[#E5E7EB]">
            <h2 class="text-2xl font-bold text-[#1E293B] mb-6">Edit Profil</h2>
            
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Avatar Upload -->
                <div x-data="{ imagePreview: '{{ $user->avatar ? asset('storage/' . $user->avatar) : '' }}' }">
                    <label class="block text-sm font-medium text-[#1E293B] mb-3">
                        Foto Profil
                    </label>
                    <div class="flex items-center gap-4">
                        <div class="w-20 h-20 rounded-full bg-[#F9FAFB] border-2 border-[#E5E7EB] flex items-center justify-center text-2xl font-bold overflow-hidden">
                            <template x-if="imagePreview">
                                <img :src="imagePreview" alt="Preview" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!imagePreview">
                                <span>{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                            </template>
                        </div>
                        <div>
                            <input 
                                type="file" 
                                name="avatar" 
                                id="avatar"
                                accept="image/*"
                                @change="imagePreview = URL.createObjectURL($event.target.files[0])"
                                class="hidden"
                            />
                            <label for="avatar" class="inline-block bg-[#2C74B3] hover:bg-[#205295] text-white px-4 py-2 rounded-lg cursor-pointer transition-colors text-sm">
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
                    <label for="name" class="block text-sm font-medium text-[#1E293B] mb-2">
                        Nama Lengkap
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name', $user->name) }}"
                        required
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#2C74B3] focus:ring-2 focus:ring-[#2C74B3]/20 outline-none transition"
                    />
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-[#1E293B] mb-2">
                        Email
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email', $user->email) }}"
                        required
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#2C74B3] focus:ring-2 focus:ring-[#2C74B3]/20 outline-none transition"
                    />
                </div>

                <!-- Bio -->
                <div>
                    <label for="bio" class="block text-sm font-medium text-[#1E293B] mb-2">
                        Bio
                    </label>
                    <textarea 
                        id="bio" 
                        name="bio" 
                        rows="4"
                        placeholder="Ceritakan sedikit tentang kamu..."
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#2C74B3] focus:ring-2 focus:ring-[#2C74B3]/20 outline-none transition resize-none"
                    >{{ old('bio', $user->bio) }}</textarea>
                </div>

                <!-- Submit Button -->
                <div class="flex gap-3 pt-4">
                    <button 
                        type="submit"
                        class="bg-[#2C74B3] hover:bg-[#205295] text-white font-medium px-8 py-3 rounded-xl transition-colors flex items-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Perubahan
                    </button>
                    <a 
                        href="{{ route('dashboard') }}"
                        class="border border-[#E5E7EB] hover:bg-[#F9FAFB] text-[#1E293B] font-medium px-8 py-3 rounded-xl transition-colors"
                    >
                        Batal
                    </a>
                </div>
            </form>
        </div>

        <!-- Change Password Section -->
        <div class="bg-white rounded-2xl p-8 shadow-sm border border-[#E5E7EB] mt-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-[#1E293B]">Ubah Password</h2>
                    <p class="text-sm text-[#1E293B]/60">Pastikan menggunakan password yang kuat dan unik</p>
                </div>
            </div>
            
            <form method="POST" action="{{ route('profile.password') }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Current Password -->
                <div x-data="{ showCurrentPassword: false }">
                    <label for="current_password" class="block text-sm font-medium text-[#1E293B] mb-2">
                        Password Saat Ini <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            :type="showCurrentPassword ? 'text' : 'password'"
                            id="current_password" 
                            name="current_password" 
                            required
                            placeholder="Masukkan password saat ini"
                            class="w-full px-4 py-3 pr-12 rounded-xl border border-gray-300 focus:border-[#2C74B3] focus:ring-2 focus:ring-[#2C74B3]/20 outline-none transition"
                        />
                        <button 
                            type="button"
                            @click="showCurrentPassword = !showCurrentPassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                        >
                            <svg x-show="!showCurrentPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showCurrentPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    @error('current_password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- New Password -->
                <div x-data="{ showNewPassword: false }">
                    <label for="new_password" class="block text-sm font-medium text-[#1E293B] mb-2">
                        Password Baru <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            :type="showNewPassword ? 'text' : 'password'"
                            id="new_password" 
                            name="new_password" 
                            required
                            placeholder="Minimal 8 karakter"
                            class="w-full px-4 py-3 pr-12 rounded-xl border border-gray-300 focus:border-[#2C74B3] focus:ring-2 focus:ring-[#2C74B3]/20 outline-none transition"
                        />
                        <button 
                            type="button"
                            @click="showNewPassword = !showNewPassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                        >
                            <svg x-show="!showNewPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showNewPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    <p class="text-xs text-[#1E293B]/50 mt-1">
                        Gunakan kombinasi huruf, angka, dan simbol untuk keamanan maksimal
                    </p>
                    @error('new_password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm New Password -->
                <div x-data="{ showConfirmPassword: false }">
                    <label for="new_password_confirmation" class="block text-sm font-medium text-[#1E293B] mb-2">
                        Konfirmasi Password Baru <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            :type="showConfirmPassword ? 'text' : 'password'"
                            id="new_password_confirmation" 
                            name="new_password_confirmation" 
                            required
                            placeholder="Ketik ulang password baru"
                            class="w-full px-4 py-3 pr-12 rounded-xl border border-gray-300 focus:border-[#2C74B3] focus:ring-2 focus:ring-[#2C74B3]/20 outline-none transition"
                        />
                        <button 
                            type="button"
                            @click="showConfirmPassword = !showConfirmPassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                        >
                            <svg x-show="!showConfirmPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showConfirmPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Info Alert -->
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="text-sm text-blue-800">
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
                <div class="flex gap-3 pt-4 border-t border-gray-200">
                    <button 
                        type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white font-medium px-8 py-3 rounded-xl transition-colors flex items-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Ubah Password
                    </button>
                    <button 
                        type="reset"
                        class="border border-[#E5E7EB] hover:bg-[#F9FAFB] text-[#1E293B] font-medium px-8 py-3 rounded-xl transition-colors"
                    >
                        Reset
                    </button>
                </div>
            </form>
        </div>

        <!-- Achievement Badge (Optional) -->
        <div class="mt-6 bg-gradient-to-br from-yellow-50 to-orange-50 rounded-2xl p-6 border-2 border-yellow-200">
            <div class="flex items-center gap-4">
                <div class="text-5xl">🏆</div>
                <div>
                    <h3 class="text-xl font-bold text-[#1E293B] mb-1">Badge Pencapaian</h3>
                    <p class="text-sm text-[#1E293B]/70 mb-2">
                        Kamu mendapat badge "Pemula" karena sudah membuat {{ $totalNotes }} catatan!
                    </p>
                    <div class="flex gap-2">
                        <span class="px-3 py-1 bg-yellow-200 text-yellow-800 rounded-lg text-xs font-medium">🥉 Pemula (1-10 catatan)</span>
                    </div>
                </div>
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
