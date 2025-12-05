@extends('layouts.app')

@section('title', 'Kategori - Ringkesin')

@section('content')
<div class="flex-1 bg-[#F9FAFB] overflow-auto" x-data="categoryManager()">
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
                @click="showAddModal = true"
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
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($categories as $category)
                @php
                    $color = $category->color;
                    // Convert hex to RGB and create light version
                    if (str_starts_with($color, '#')) {
                        $hex = ltrim($color, '#');
                        $r = hexdec(substr($hex, 0, 2));
                        $g = hexdec(substr($hex, 2, 2));
                        $b = hexdec(substr($hex, 4, 2));
                        $bgColor = "rgba($r, $g, $b, 0.1)";
                        $borderColor = "rgba($r, $g, $b, 0.3)";
                    } else {
                        $bgColor = '';
                        $borderColor = '';
                    }
                @endphp
                
                <a href="{{ route('notes.index', ['category_id' => $category->id]) }}"
                   class="p-6 rounded-2xl border-2 cursor-pointer group relative overflow-hidden transition-all hover:shadow-xl"
                   style="background-color: {{ $bgColor ?: 'rgba(148, 163, 184, 0.1)' }}; border-color: {{ $borderColor ?: 'rgba(148, 163, 184, 0.3)' }};">
                    
                    <!-- Gradient overlay on hover -->
                    <div class="absolute inset-0 bg-gradient-to-br from-white/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    
                    <div class="flex flex-col items-center text-center relative z-10">
                        <div class="w-20 h-20 rounded-2xl flex items-center justify-center text-4xl shadow-lg mb-4 group-hover:scale-110 group-hover:rotate-3 transition-all text-white"
                             style="background-color: {{ $color }};">
                            {{ $category->icon }}
                        </div>
                        <h3 class="text-lg font-bold text-[#1E293B] mb-2 group-hover:text-[#2C74B3] transition-colors">
                            {{ $category->name }}
                        </h3>
                        <p class="text-sm text-[#1E293B]/60">
                            {{ $category->notes_count }} catatan
                        </p>
                    </div>

                    <!-- Action buttons on hover -->
                    <div class="flex gap-2 mt-4 pt-4 border-t border-[#E5E7EB]/50 opacity-0 group-hover:opacity-100 transition-opacity relative z-10">
                        <button
                            type="button"
                            @click.prevent="editCategory({{ $category->id }}, '{{ $category->name }}', '{{ $category->icon }}', '{{ $category->color }}')"
                            class="flex-1 p-2 bg-white hover:bg-blue-50 rounded-lg transition-colors text-[#1E293B]/60 hover:text-[#2C74B3] border border-gray-200"
                            title="Edit"
                        >
                            <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <form action="{{ route('categories.destroy', $category) }}" method="POST" class="flex-1" @submit.prevent="if(confirm('Yakin ingin menghapus kategori ini?')) $el.submit()">
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                class="w-full p-2 bg-white hover:bg-red-50 rounded-lg transition-colors text-[#1E293B]/60 hover:text-red-600 border border-gray-200"
                                title="Hapus"
                            >
                                <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </a>
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
        x-show="showAddModal"
        x-cloak
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
        @click.self="showAddModal = false"
    >
        <div class="bg-white rounded-2xl max-w-md w-full p-6 max-h-[90vh] overflow-y-auto" @click.stop>
            <h2 class="text-2xl font-bold text-[#1E293B] mb-2">Tambah Kategori Baru</h2>
            <p class="text-sm text-[#1E293B]/60 mb-6">
                Buat kategori custom untuk mengorganisir catatan
            </p>

            <!-- Preview -->
            <div class="bg-[#F9FAFB] rounded-xl p-6 border-2 border-[#E5E7EB] mb-6">
                <p class="text-xs text-[#1E293B]/60 mb-2">Preview Kategori:</p>
                <div 
                    class="rounded-xl p-4 text-white inline-flex items-center gap-3"
                    :style="'background-color: ' + selectedColor"
                >
                    <span class="text-2xl" x-text="selectedIcon"></span>
                    <span class="text-lg" x-text="categoryName || 'Nama Kategori'"></span>
                </div>
            </div>

            <form action="{{ route('categories.store') }}" method="POST" class="space-y-5">
                @csrf

                <!-- Name Input -->
                <div>
                    <label for="add_name" class="block text-sm font-medium text-[#1E293B] mb-2">
                        Nama Kategori
                    </label>
                    <input 
                        type="text" 
                        id="add_name" 
                        name="name"
                        x-model="categoryName"
                        placeholder="Contoh: Matematika, Biologi, dll"
                        required
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#2C74B3] focus:ring-2 focus:ring-[#2C74B3]/20 outline-none transition"
                    />
                </div>

                <!-- Icon and Color in Grid -->
                <div class="grid grid-cols-2 gap-3">
                    <!-- Emoji Picker -->
                    <div class="relative">
                        <label class="block text-sm font-medium text-[#1E293B] mb-2">Emoji</label>
                        <input 
                            type="text"
                            x-model="selectedIcon"
                            name="icon"
                            @click="toggleEmojiPicker()"
                            readonly
                            placeholder="📚"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-[#2C74B3] focus:ring-2 focus:ring-[#2C74B3]/20 outline-none transition text-2xl text-center cursor-pointer"
                        />
                        
                        <!-- Emoji Picker Popup -->
                        <div x-show="showEmojiPicker"
                             x-cloak
                             @click.away="showEmojiPicker = false"
                             class="absolute z-[100] mt-2 w-64 bg-white rounded-xl shadow-2xl border-2 border-[#2C74B3] p-3 max-h-48 overflow-y-auto">
                            <div class="grid grid-cols-8 gap-1">
                                <template x-for="(emoji, index) in emojis" :key="index">
                                    <button 
                                        type="button"
                                        @click="selectEmoji(emoji)"
                                        class="text-2xl hover:bg-blue-100 rounded p-1 transition-colors"
                                        x-text="emoji">
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Color Picker -->
                    <div>
                        <label class="block text-sm font-medium text-[#1E293B] mb-2">Warna</label>
                        <input 
                            type="color"
                            x-model="selectedColor"
                            name="color"
                            class="w-full h-[42px] rounded-lg border border-gray-300 cursor-pointer"
                        />
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
                        @click="closeAddModal()"
                        class="flex-1 border border-[#E5E7EB] hover:bg-[#F9FAFB] text-[#1E293B] font-medium py-3 rounded-xl transition-colors"
                    >
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div 
        x-show="showEditModal"
        x-cloak
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
        @click.self="showEditModal = false"
    >
        <div class="bg-white rounded-2xl max-w-md w-full p-6 max-h-[90vh] overflow-y-auto" @click.stop>
            <h2 class="text-2xl font-bold text-[#1E293B] mb-2">Edit Kategori</h2>
            <p class="text-sm text-[#1E293B]/60 mb-6">
                Ubah nama, emoji, atau warna kategori
            </p>

            <!-- Preview -->
            <div class="bg-[#F9FAFB] rounded-xl p-6 border-2 border-[#E5E7EB] mb-6">
                <p class="text-xs text-[#1E293B]/60 mb-2">Preview Kategori:</p>
                <div 
                    class="rounded-xl p-4 text-white inline-flex items-center gap-3"
                    :style="'background-color: ' + editSelectedColor"
                >
                    <span class="text-2xl" x-text="editSelectedIcon"></span>
                    <span class="text-lg" x-text="editCategoryName || 'Nama Kategori'"></span>
                </div>
            </div>

            <form :action="'/categories/' + editCategoryId" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <!-- Name Input -->
                <div>
                    <label for="edit_name" class="block text-sm font-medium text-[#1E293B] mb-2">
                        Nama Kategori
                    </label>
                    <input 
                        type="text" 
                        id="edit_name" 
                        name="name"
                        x-model="editCategoryName"
                        placeholder="Contoh: Matematika, Biologi, dll"
                        required
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#2C74B3] focus:ring-2 focus:ring-[#2C74B3]/20 outline-none transition"
                    />
                </div>

                <!-- Icon and Color in Grid -->
                <div class="grid grid-cols-2 gap-3">
                    <!-- Emoji Picker -->
                    <div class="relative">
                        <label class="block text-sm font-medium text-[#1E293B] mb-2">Emoji</label>
                        <input 
                            type="text"
                            x-model="editSelectedIcon"
                            name="icon"
                            @click="toggleEditEmojiPicker()"
                            readonly
                            placeholder="📚"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-[#2C74B3] focus:ring-2 focus:ring-[#2C74B3]/20 outline-none transition text-2xl text-center cursor-pointer"
                        />
                        
                        <!-- Emoji Picker Popup -->
                        <div x-show="showEditEmojiPicker"
                             x-cloak
                             @click.away="showEditEmojiPicker = false"
                             class="absolute z-[100] mt-2 w-64 bg-white rounded-xl shadow-2xl border-2 border-[#2C74B3] p-3 max-h-48 overflow-y-auto">
                            <div class="grid grid-cols-8 gap-1">
                                <template x-for="(emoji, index) in emojis" :key="index">
                                    <button 
                                        type="button"
                                        @click="selectEditEmoji(emoji)"
                                        class="text-2xl hover:bg-blue-100 rounded p-1 transition-colors"
                                        x-text="emoji">
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Color Picker -->
                    <div>
                        <label class="block text-sm font-medium text-[#1E293B] mb-2">Warna</label>
                        <input 
                            type="color"
                            x-model="editSelectedColor"
                            name="color"
                            class="w-full h-[42px] rounded-lg border border-gray-300 cursor-pointer"
                        />
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3 pt-4">
                    <button 
                        type="submit"
                        :disabled="!editCategoryName.trim()"
                        class="flex-1 bg-[#2C74B3] hover:bg-[#205295] disabled:bg-gray-300 text-white font-medium py-3 rounded-xl transition-colors"
                    >
                        Update Kategori
                    </button>
                    <button 
                        type="button"
                        @click="closeEditModal()"
                        class="flex-1 border border-[#E5E7EB] hover:bg-[#F9FAFB] text-[#1E293B] font-medium py-3 rounded-xl transition-colors"
                    >
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function categoryManager() {
    return {
        // Add Modal
        showAddModal: false,
        categoryName: '',
        selectedIcon: '📁',
        selectedColor: '#3B82F6',
        showEmojiPicker: false,
        
        // Edit Modal
        showEditModal: false,
        editCategoryId: null,
        editCategoryName: '',
        editSelectedIcon: '📁',
        editSelectedColor: '#3B82F6',
        showEditEmojiPicker: false,
        
        // Emoji list
        emojis: ['📁', '📚', '📖', '📝', '📊', '💼', '🎓', '🔬', '🧪', '📐', '📏', '🖊️', '✏️', '📌', '📍', '🎨', '🎭', '🎪', '🎬', '🎮', '🎯', '🎲', '🧩', '🎸', '🎹', '🎺', '🎻', '🥁', '💻', '⌨️', '🖥️', '🖨️', '📱', '☎️', '📞', '📟', '📠', '📡', '🔋', '🔌', '💡', '🔦', '🕯️', '🧯', '🛢️', '💰', '💴', '💵', '💶', '💷', '💸', '💳', '🧾', '✉️', '📧', '📨', '📩', '📤', '📥', '📦', '📫', '📪', '📬', '📭', '📮', '🗳️', '✒️', '🖋️', '🖌️', '🖍️', '📂', '🗂️', '📅', '📆', '🗒️', '🗓️', '📇', '📈', '📉', '📋', '📎', '🖇️', '✂️', '🗃️', '🗄️', '🗑️'],
        
        // Add Modal Methods
        toggleEmojiPicker() {
            this.showEmojiPicker = !this.showEmojiPicker;
        },
        
        selectEmoji(emoji) {
            this.selectedIcon = emoji;
            this.showEmojiPicker = false;
        },
        
        closeAddModal() {
            this.showAddModal = false;
            this.categoryName = '';
            this.selectedIcon = '📁';
            this.selectedColor = '#3B82F6';
        },
        
        // Edit Modal Methods
        editCategory(id, name, icon, color) {
            this.editCategoryId = id;
            this.editCategoryName = name;
            this.editSelectedIcon = icon;
            this.editSelectedColor = color;
            this.showEditModal = true;
        },
        
        toggleEditEmojiPicker() {
            this.showEditEmojiPicker = !this.showEditEmojiPicker;
        },
        
        selectEditEmoji(emoji) {
            this.editSelectedIcon = emoji;
            this.showEditEmojiPicker = false;
        },
        
        closeEditModal() {
            this.showEditModal = false;
            this.editCategoryId = null;
            this.editCategoryName = '';
            this.editSelectedIcon = '📁';
            this.editSelectedColor = '#3B82F6';
        }
    }
}
</script>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
