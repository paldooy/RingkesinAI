@extends('layouts.app')

@section('title', 'Import Catatan - Ringkesin')

@section('content')
<div class="flex-1 bg-[#F9FAFB] overflow-auto">
    <div class="max-w-2xl mx-auto p-4 md:p-8">
        <div class="text-center my-6">
            <div class="w-20 h-20 bg-[#2C74B3]/10 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-[#2C74B3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-[#1E293B] mb-2">Import Catatan</h1>
            <p class="text-sm text-[#1E293B]/60">Masukkan kode share untuk mengimport catatan</p>
        </div>

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-600 rounded-xl p-4 mb-6">
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-600 rounded-xl p-4 mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- Form Input Kode -->
        <div class="bg-white rounded-2xl shadow-lg border-2 border-[#E5E7EB] p-8">
            <form action="{{ route('notes.import.process') }}" method="POST">
                @csrf

                <div class="mb-6">
                    <label for="share_code" class="block text-sm font-medium text-[#1E293B] mb-2">
                        Kode Share <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="share_code"
                        name="share_code"
                        value="{{ old('share_code') }}"
                        required
                        maxlength="6"
                        class="w-full px-4 py-4 text-center text-2xl font-bold tracking-widest uppercase rounded-xl border-2 border-gray-300 focus:border-[#2C74B3] focus:ring-2 focus:ring-[#2C74B3]/20 outline-none"
                        placeholder="XXXXXX"
                        style="letter-spacing: 0.5em;"
                    />
                    @error('share_code')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-2 text-center">Masukkan 6 digit kode share yang Anda terima</p>
                </div>

                <button 
                    type="submit"
                    class="w-full bg-[#2C74B3] hover:bg-[#205295] text-white font-medium px-6 py-4 rounded-xl transition-colors flex items-center justify-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Cari Catatan
                </button>
            </form>
        </div>

        <!-- Info Section -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white rounded-xl p-6 border border-gray-200">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-[#1E293B] mb-1">Punya Link?</h3>
                        <p class="text-sm text-gray-600">Jika Anda menerima link share, klik langsung link tersebut untuk import catatan</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-200">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-[#1E293B] mb-1">Hanya Punya Kode?</h3>
                        <p class="text-sm text-gray-600">Masukkan 6 digit kode di form di atas untuk mengakses catatan</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- How it Works -->
        <div class="mt-8 bg-gradient-to-br from-[#2C74B3]/10 to-[#5B8EC9]/10 rounded-2xl p-6 border border-[#2C74B3]/20">
            <h3 class="text-lg font-bold text-[#1E293B] mb-4">📚 Cara Kerja Import Catatan</h3>
            <ol class="space-y-3">
                <li class="flex items-start gap-3">
                    <span class="flex items-center justify-center w-6 h-6 bg-[#2C74B3] text-white rounded-full text-sm font-bold flex-shrink-0">1</span>
                    <p class="text-sm text-[#1E293B]">Terima kode share dari teman Anda (6 digit)</p>
                </li>
                <li class="flex items-start gap-3">
                    <span class="flex items-center justify-center w-6 h-6 bg-[#2C74B3] text-white rounded-full text-sm font-bold flex-shrink-0">2</span>
                    <p class="text-sm text-[#1E293B]">Masukkan kode di form atau klik link share</p>
                </li>
                <li class="flex items-start gap-3">
                    <span class="flex items-center justify-center w-6 h-6 bg-[#2C74B3] text-white rounded-full text-sm font-bold flex-shrink-0">3</span>
                    <p class="text-sm text-[#1E293B]">Preview catatan dan sesuaikan judul, kategori, tags</p>
                </li>
                <li class="flex items-start gap-3">
                    <span class="flex items-center justify-center w-6 h-6 bg-[#2C74B3] text-white rounded-full text-sm font-bold flex-shrink-0">4</span>
                    <p class="text-sm text-[#1E293B]">Klik "Simpan ke Akun Saya" - Catatan tersimpan di akun Anda!</p>
                </li>
            </ol>
        </div>

        <div class="mt-6 text-center">
            <a href="{{ route('notes.index') }}" class="text-sm text-[#2C74B3] hover:underline">
                ← Kembali ke Catatan Saya
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto uppercase input
    document.getElementById('share_code').addEventListener('input', function(e) {
        e.target.value = e.target.value.toUpperCase();
    });
</script>
@endpush
