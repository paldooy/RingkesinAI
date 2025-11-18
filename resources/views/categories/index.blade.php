@extends('layouts.app')

@section('title', 'Kategori - Ringkesin')

@section('content')
<div class="flex-1 bg-[#F9FAFB] overflow-auto" x-data="{ 
    showModal: false, 
    selectedIcon: '📚', 
    selectedColor: 'bg-blue-500',
    categoryName: '',
    icons: ['📐', '🧬', '⚛️', '🧪', '📜', '📖', '🎨', '🎵', '💡', '🔬', '📊', '🌍', '⚡', '🎯', '🔨', '🎓'],
    colors: [
        { name: 'Biru', class: 'bg-blue-500', hex: '#3b82f6' },
        { name: 'Ungu', class: 'bg-purple-500', hex: '#a855f7' },
        { name: 'Pink', class: 'bg-pink-500', hex: '#ec4899' },
        { name: 'Hijau', class: 'bg-green-500', hex: '#22c55e' },
        { name: 'Oranye', class: 'bg-orange-500', hex: '#f97316' },
        { name: 'Merah', class: 'bg-red-500', hex: '#ef4444' },
        { name: 'Cyan', class: 'bg-cyan-500', hex: '#06b6d4' },
        { name: 'Indigo', class: 'bg-indigo-500', hex: '#6366f1' }
    ],
    getBgOpacity(colorClass) {
        return colorClass + '/10';
    }
}">
    <div class="max-w-7xl mx-auto p-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-[#1E293B] mb-2">Kategori</h1>
                <p class="text-sm text-[#1E293B]/60">
                    Kelola dan organisir catatanmu berdasarkan kategori
                </p>
            </div>

            <button 
                @click="showModal = true"
                class="bg-[#2C74B3] hover:bg-[#205295] text-white font-medium px-6 py-3 rounded-xl transition-colors flex items-center gap-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Kategori
            </button>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-600 rounded-xl p-4 mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- Categories Grid -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
            @foreach($categories as $category)
                @php
                    $bgOpacity = str_replace('bg-', '', $category->color) . '/10';
                    $bgClass = 'bg-' . str_replace('bg-', '', $category->color) . '-500/10';
                @endphp
                
                <div 
                    class="p-4 rounded-2xl border-2 cursor-pointer group relative overflow-hidden transition-all hover:shadow-xl {{ $bgClass }}"
                    style="border-color: transparent;"
                    @mouseenter="$el.style.borderColor = '{{ str_replace('bg-', '#', $category->color) }}'"
                    @mouseleave="$el.style.borderColor = 'transparent'"
                >
                    <!-- Gradient overlay on hover -->
                    <div class="absolute inset-0 {{ $category->color }} opacity-0 group-hover:opacity-5 transition-opacity"></div>
                    
                    <div class="flex flex-col items-center text-center relative z-10">
                        <div class="w-14 h-14 {{ $category->color }} rounded-xl flex items-center justify-center text-2xl shadow-lg mb-3 group-hover:scale-110 group-hover:rotate-3 transition-all">
                            {{ $category->icon }}
                        </div>
                        <h3 class="text-sm text-[#1E293B] mb-1 line-clamp-1 group-hover:font-medium transition-all">
                            {{ $category->name }}
                        </h3>
                        <p class="text-xs text-[#1E293B]/50">
                            {{ $category->notes_count }} catatan
                        </p>
                    </div>

                    <!-- Action buttons on hover -->
                    <div class="flex gap-1 mt-3 pt-3 border-t border-[#E5E7EB]/50 opacity-0 group-hover:opacity-100 transition-opacity relative z-10">
                        <button
                            class="flex-1 p-1.5 hover:bg-white/80 rounded transition-colors text-[#1E293B]/60 hover:text-[#2C74B3]"
                            title="Edit"
                        >
                            <svg class="w-3.5 h-3.5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <form action="{{ route('categories.destroy', $category) }}" method="POST" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                onclick="return confirm('Yakin ingin menghapus kategori ini?')"
                                class="w-full p-1.5 hover:bg-red-50 rounded transition-colors text-[#1E293B]/60 hover:text-red-600"
                                title="Hapus"
                            >
                                <svg class="w-3.5 h-3.5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        @if($categories->isEmpty())
            <div class="text-center py-20">
                <div class="w-24 h-24 bg-[#F9FAFB] rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-[#1E293B]/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-xl text-[#1E293B] mb-2">Belum ada kategori</h3>
                <p class="text-[#1E293B]/60 mb-6">
                    Buat kategori pertamamu untuk mulai mengorganisir catatan
                </p>
            </div>
        @endif
    </div>

    <!-- Add Category Modal -->
    <div 
        x-show="showModal"
        x-cloak
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
        @click.self="showModal = false"
    >
        <div class="bg-white rounded-2xl max-w-md w-full p-6" @click.stop>
            <h2 class="text-2xl font-bold text-[#1E293B] mb-2">Tambah Kategori Baru</h2>
            <p class="text-sm text-[#1E293B]/60 mb-6">
                Buat kategori custom untuk mengorganisir catatan
            </p>

            <!-- Preview -->
            <div class="bg-[#F9FAFB] rounded-xl p-6 border-2 border-[#E5E7EB] mb-6">
                <p class="text-xs text-[#1E293B]/60 mb-2">Preview Kategori:</p>
                <div 
                    :class="selectedColor" 
                    class="rounded-xl p-4 text-white inline-flex items-center gap-3"
                >
                    <span class="text-2xl" x-text="selectedIcon"></span>
                    <span class="text-lg" x-text="categoryName || 'Nama Kategori'"></span>
                </div>
            </div>

            <form action="{{ route('categories.store') }}" method="POST" class="space-y-5">
                @csrf

                <!-- Name Input -->
                <div>
                    <label for="name" class="block text-sm font-medium text-[#1E293B] mb-2">
                        Nama Kategori
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name"
                        x-model="categoryName"
                        placeholder="Contoh: Matematika, Biologi, dll"
                        required
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#2C74B3] focus:ring-2 focus:ring-[#2C74B3]/20 outline-none transition"
                    />
                </div>

                <!-- Icon Selection -->
                <div>
                    <label class="block text-sm font-medium text-[#1E293B] mb-3">Pilih Icon</label>
                    <input type="hidden" name="icon" :value="selectedIcon" />
                    <div class="grid grid-cols-8 gap-2">
                        <template x-for="icon in icons" :key="icon">
                            <button
                                type="button"
                                @click="selectedIcon = icon"
                                :class="selectedIcon === icon ? 'bg-[#2C74B3] scale-110 shadow-lg' : 'bg-white border-2 border-[#E5E7EB] hover:border-[#A7C7E7]'"
                                class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl transition-all"
                                x-text="icon"
                            ></button>
                        </template>
                    </div>
                </div>

                <!-- Color Selection -->
                <div>
                    <label class="block text-sm font-medium text-[#1E293B] mb-3">Pilih Warna</label>
                    <input type="hidden" name="color" :value="selectedColor" />
                    <div class="grid grid-cols-8 gap-2">
                        <template x-for="color in colors" :key="color.class">
                            <button
                                type="button"
                                @click="selectedColor = color.class"
                                :class="[color.class, selectedColor === color.class ? 'scale-110 shadow-lg ring-4 ring-[#A7C7E7]' : 'hover:scale-105']"
                                :title="color.name"
                                class="w-12 h-12 rounded-xl transition-all"
                            ></button>
                        </template>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3 pt-4">
                    <button 
                        type="submit"
                        :disabled="!categoryName.trim()"
                        class="flex-1 bg-[#2C74B3] hover:bg-[#205295] disabled:bg-gray-300 text-white font-medium py-3 rounded-xl transition-colors"
                    >
                        Simpan Kategori
                    </button>
                    <button 
                        type="button"
                        @click="showModal = false; categoryName = ''; selectedIcon = '📚'; selectedColor = 'bg-blue-500'"
                        class="flex-1 border border-[#E5E7EB] hover:bg-[#F9FAFB] text-[#1E293B] font-medium py-3 rounded-xl transition-colors"
                    >
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
