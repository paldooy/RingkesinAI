@extends('layouts.app')

@section('title', 'Import Catatan - Ringkesin')

@section('content')
<div class="flex-1 bg-[#F9FAFB] overflow-auto">
    <div class="mx-auto p-6">
        <!-- Header -->
        <div class="mb-6 max-w-7xl mx-auto">
            <div class="flex items-center gap-2 mb-2">
                <a href="{{ route('notes.index') }}" class="text-[#1E293B]/60 hover:text-[#2C74B3] transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-[#1E293B]">Import Catatan</h1>
            </div>
            <p class="text-sm text-[#1E293B]/60">
                Simpan catatan yang dibagikan temanmu ke akunmu sendiri
            </p>
        </div>

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-600 rounded-xl p-4 mb-6 max-w-7xl mx-auto">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('notes.import.save', $share->share_code) }}" method="POST" class="max-w-7xl mx-auto">
            @csrf

            <!-- Main Content Area -->
            <div class="space-y-6">
                <!-- Title -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB]">
                    <label for="title" class="block text-sm font-medium text-[#1E293B] mb-2">
                        Judul Catatan <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="title" 
                        name="title" 
                        value="{{ old('title', $note->title) }}"
                        placeholder="Masukkan judul catatan"
                        required
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#2C74B3] focus:ring-2 focus:ring-[#2C74B3]/20 outline-none transition"
                    />
                    @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Content Preview -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB]">
                    <label class="block text-sm font-medium text-[#1E293B] mb-2">
                        Isi Catatan (Preview)
                    </label>
                    <div class="bg-gray-50 rounded-xl p-6 border-2 border-gray-300 max-h-[500px] overflow-y-auto">
                        <div class="prose prose-sm md:prose-base max-w-none">
                            {!! markdown_to_html($note->content) !!}
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">💡 Isi catatan akan otomatis tersalin saat Anda menyimpan</p>
                </div>

                <!-- Metadata & Actions Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pb-16">
                    <!-- Actions -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB]">
                        <h3 class="text-lg font-bold text-[#1E293B] mb-4">Aksi</h3>
                        <div class="space-y-3">
                            <button 
                                type="submit"
                                class="w-full bg-[#2C74B3] hover:bg-[#205295] text-white font-medium py-3 rounded-xl transition-colors flex items-center justify-center gap-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Simpan ke Akun Saya
                            </button>
                            <a 
                                href="{{ route('notes.index') }}"
                                class="w-full border border-gray-300 hover:bg-gray-50 text-[#1E293B] font-medium py-3 rounded-xl transition-colors flex items-center justify-center gap-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Batal
                            </a>
                        </div>
                    </div>

                    <!-- Category -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB]"
                         x-data="categoryFormImport"
                         x-init="init()">
                        <h3 class="text-lg font-bold text-[#1E293B] mb-4">Kategori</h3>
                        
                        <select 
                            name="category_id" 
                            x-model="selectedCat"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#2C74B3] focus:ring-2 focus:ring-[#2C74B3]/20 outline-none transition mb-3"
                        >
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">
                                    {{ $category->icon }} {{ $category->name }}
                                </option>
                            @endforeach
                            <option value="new">➕ Buat Kategori Baru</option>
                        </select>

                        <!-- New Category Form -->
                        <div x-show="selectedCat === 'new'" x-cloak class="space-y-3 p-4 bg-blue-50 rounded-xl border border-blue-200">
                            <div>
                                <label class="block text-xs font-medium text-[#1E293B] mb-2">Nama Kategori</label>
                                <input 
                                    type="text"
                                    x-model="newCatName"
                                    name="new_category_name"
                                    placeholder="Contoh: Matematika"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-[#2C74B3] focus:ring-2 focus:ring-[#2C74B3]/20 outline-none transition text-sm"
                                />
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="relative">
                                    <label class="block text-xs font-medium text-[#1E293B] mb-2">Emoji</label>
                                    <div class="relative">
                                        <input 
                                            type="text"
                                            x-model="newCatEmoji"
                                            name="new_category_icon"
                                            @click="toggleEmojiPicker()"
                                            readonly
                                            placeholder="📚"
                                            class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-[#2C74B3] focus:ring-2 focus:ring-[#2C74B3]/20 outline-none transition text-sm text-center cursor-pointer"
                                        />
                                        
                                        <!-- Emoji Picker Popup -->
                                        <div x-show="showEmojiPicker"
                                             @click.away="showEmojiPicker = false"
                                             class="absolute z-50 mt-2 w-64 bg-white rounded-xl shadow-2xl border-2 border-[#2C74B3] p-3 max-h-48 overflow-y-auto">
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
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-[#1E293B] mb-2">Warna</label>
                                    <input 
                                        type="color"
                                        x-model="newCatColor"
                                        name="new_category_color"
                                        class="w-full h-10 rounded-lg border border-gray-300 cursor-pointer"
                                    />
                                </div>
                            </div>
                        </div>

                        @error('category_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tags -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB]" 
                         x-data="{ 
                             tags: {{ json_encode($note->tags->pluck('name')->toArray()) }}, 
                             tagInput: '' 
                         }">
                        <h3 class="text-lg font-bold text-[#1E293B] mb-4">Tags</h3>
                        
                        <div class="flex gap-2 mb-3">
                            <input 
                                type="text" 
                                x-model="tagInput"
                                @keydown.enter.prevent="if(tagInput.trim()) { tags.push(tagInput.trim()); tagInput = ''; }"
                                placeholder="Tambah tag..."
                                class="flex-1 px-3 py-2 rounded-lg border border-gray-300 focus:border-[#2C74B3] focus:ring-2 focus:ring-[#2C74B3]/20 outline-none transition text-sm"
                            />
                            <button 
                                type="button"
                                @click="if(tagInput.trim()) { tags.push(tagInput.trim()); tagInput = ''; }"
                                class="bg-[#2C74B3] hover:bg-[#205295] text-white px-4 py-2 rounded-lg transition-colors"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </button>
                        </div>

                        <div class="flex flex-wrap gap-2" x-show="tags.length > 0">
                            <template x-for="(tag, index) in tags" :key="index">
                                <span class="inline-flex items-center gap-1 px-3 py-1 bg-blue-50 text-blue-700 rounded-lg text-sm">
                                    <input type="hidden" name="tags[]" :value="tag" />
                                    <span x-text="tag"></span>
                                    <button type="button" @click="tags.splice(index, 1)" class="hover:text-blue-900">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </span>
                            </template>
                        </div>

                        <p class="text-xs text-[#1E293B]/50 mt-3">
                            Tekan Enter atau klik + untuk menambah tag
                        </p>
                        
                        @error('tags')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Share Info Card -->
                <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-2xl p-6 border border-blue-200">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-[#1E293B] mb-2">Informasi Share</h4>
                            <div class="text-sm text-gray-700 space-y-1">
                                <p><span class="font-medium">Dibagikan oleh:</span> {{ $note->user->name }}</p>
                                <p><span class="font-medium">Kode Share:</span> <code class="bg-white px-2 py-1 rounded font-mono text-blue-600">{{ $share->share_code }}</code></p>
                                <p><span class="font-medium">Dilihat:</span> {{ $share->view_count }} kali</p>
                                @if($share->expires_at)
                                    <p><span class="font-medium">Berlaku hingga:</span> {{ $share->expires_at->format('d M Y H:i') }}</p>
                                @else
                                    <p><span class="font-medium">Berlaku:</span> Tanpa batas waktu</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Alpine.js component for import category form
    document.addEventListener('alpine:init', () => {
        Alpine.data('categoryFormImport', () => ({
            selectedCat: '{{ old("category_id") }}',
            newCatName: '',
            newCatEmoji: '📁',
            newCatColor: '#3B82F6',
            showEmojiPicker: false,
            emojis: [],
            
            init() {
                // Initialize emoji list
                this.emojis = ['📁', '📚', '📖', '📝', '📊', '💼', '🎓', '🔬', '🧪', '📐', '📏', '🖊️', '✏️', '📌', '📍', '🎨', '🎭', '🎪', '🎬', '🎮', '🎯', '🎲', '🧩', '🎸', '🎹', '🎺', '🎻', '🥁', '💻', '⌨️', '🖥️', '🖨️', '📱', '☎️', '📞', '📟', '📠', '📡', '🔋', '🔌', '💡', '🔦', '🕯️', '🧯', '🛢️', '💰', '💴', '💵', '💶', '💷', '💸', '💳', '🧾', '✉️', '📧', '📨', '📩', '📤', '📥', '📦', '📫', '📪', '📬', '📭', '📮', '🗳️', '✏️', '✒️', '🖋️', '🖊️', '🖌️', '🖍️', '📝', '💼', '📁', '📂', '🗂️', '📅', '📆', '🗒️', '🗓️', '📇', '📈', '📉', '📊', '📋', '📌', '📍', '📎', '🖇️', '📏', '📐', '✂️', '🗃️', '🗄️', '🗑️'];
                console.log('✅ Import category form initialized');
            },
            
            selectEmoji(emoji) {
                this.newCatEmoji = emoji;
                this.showEmojiPicker = false;
            },
            
            toggleEmojiPicker() {
                this.showEmojiPicker = !this.showEmojiPicker;
            }
        }));
    });
</script>
@endpush

@push('styles')
<style>
    /* Alpine.js cloak */
    [x-cloak] { display: none !important; }
    
    /* Enhanced prose/markdown styling */
    .prose {
        color: #1E293B;
        line-height: 1.8;
        font-size: 1rem;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }
    
    .prose * {
        max-width: 100%;
    }
    
    .prose h1 {
        color: #0F172A;
        font-weight: 800;
        font-size: 2em;
        margin-top: 0;
        margin-bottom: 1em;
        line-height: 1.2;
        border-bottom: 3px solid #2C74B3;
        padding-bottom: 0.5em;
    }
    
    .prose h2 {
        color: #1E293B;
        font-weight: 700;
        font-size: 1.6em;
        margin-top: 2em;
        margin-bottom: 1em;
        line-height: 1.3;
        border-bottom: 2px solid #E2E8F0;
        padding-bottom: 0.4em;
    }
    
    .prose h3 {
        color: #1E293B;
        font-weight: 600;
        font-size: 1.3em;
        margin-top: 1.6em;
        margin-bottom: 0.8em;
        line-height: 1.4;
    }
    
    .prose h4 {
        color: #334155;
        font-weight: 600;
        font-size: 1.1em;
        margin-top: 1.4em;
        margin-bottom: 0.6em;
    }
    
    .prose p {
        margin-bottom: 1.5em;
        line-height: 1.8;
        text-align: justify;
    }
    
    .prose ul, .prose ol {
        margin-left: 1.625em;
        margin-bottom: 1.5em;
        padding-left: 0.375em;
    }
    
    .prose li {
        margin-bottom: 0.5em;
        line-height: 1.8;
    }
    
    .prose li > p {
        margin-bottom: 0.75em;
    }
    
    .prose ul > li {
        list-style-type: disc;
    }
    
    .prose ol > li {
        list-style-type: decimal;
    }
    
    .prose strong, .prose b {
        font-weight: 700;
        color: #0F172A;
    }
    
    .prose em, .prose i {
        font-style: italic;
    }
    
    .prose a {
        color: #2C74B3;
        text-decoration: underline;
        font-weight: 500;
    }
    
    .prose a:hover {
        color: #205295;
    }
    
    .prose code {
        background: #E2E8F0;
        color: #1E293B;
        padding: 0.2em 0.4em;
        border-radius: 0.25em;
        font-size: 0.875em;
        font-family: 'Courier New', Consolas, Monaco, monospace;
        font-weight: 500;
    }
    
    .prose pre {
        background: #1E293B;
        color: #F8FAFC;
        padding: 1.25em;
        border-radius: 0.5em;
        overflow-x: auto;
        margin-bottom: 1.5em;
        line-height: 1.7;
    }
    
    .prose pre code {
        background: transparent;
        color: inherit;
        padding: 0;
        font-size: 0.875em;
    }
    
    .prose blockquote {
        border-left: 4px solid #2C74B3;
        padding-left: 1.25em;
        margin-left: 0;
        margin-bottom: 1.5em;
        font-style: italic;
        color: #475569;
        background: #F8FAFC;
        padding: 1em 1.25em;
        border-radius: 0 0.5em 0.5em 0;
    }
    
    .prose blockquote p {
        margin-bottom: 0.5em;
    }
    
    .prose blockquote p:last-child {
        margin-bottom: 0;
    }
    
    .prose hr {
        border: 0;
        border-top: 2px solid #E2E8F0;
        margin: 2em 0;
    }
    
    .prose table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 1.5em;
        overflow-x: auto;
        display: block;
    }
    
    .prose th, .prose td {
        border: 1px solid #E2E8F0;
        padding: 0.75em;
        text-align: left;
    }
    
    .prose th {
        background: #F1F5F9;
        font-weight: 600;
        color: #1E293B;
    }
    
    .prose img {
        max-width: 100%;
        height: auto;
        border-radius: 0.5em;
        margin: 1.5em 0;
    }
    
    /* Scrollbar styling */
    .prose::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    
    .prose::-webkit-scrollbar-track {
        background: #F1F5F9;
        border-radius: 4px;
    }
    
    .prose::-webkit-scrollbar-thumb {
        background: #CBD5E1;
        border-radius: 4px;
    }
    
    .prose::-webkit-scrollbar-thumb:hover {
        background: #94A3B8;
    }
</style>
@endpush
