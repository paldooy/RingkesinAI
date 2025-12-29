@extends('layouts.app')

@section('title', $note->title . ' - Ringkesin')

@section('content')
<div class="flex-1 bg-[#F9FAFB] overflow-auto" x-data="{ showShareModal: false, shareData: null, isGenerating: false, expiresIn: '1day' }">
    <div class="max-w-6xl mx-auto p-6 md:p-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center gap-3 mb-4">
                <a href="{{ route('notes.index') }}" class="text-[#1E293B]/60 hover:text-[#2C74B3] transition-colors p-2 hover:bg-white rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h1 class="text-2xl md:text-3xl font-bold text-[#1E293B]">Detail Catatan</h1>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-600 rounded-xl p-4 mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- Note Card -->
        <div class="bg-white rounded-2xl shadow-lg border-2 border-[#E5E7EB] overflow-hidden">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-[#2C74B3] to-[#5B8EC9] p-8 text-white">
                <div class="flex items-start gap-4 mb-4">
                    <div class="text-5xl">
                        {{ $note->category ? $note->category->icon : '📝' }}
                    </div>
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold mb-3">{{ $note->title }}</h2>
                        <div class="flex flex-wrap items-center gap-3 text-sm">
                            @if($note->category)
                                <span class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-lg">
                                    {{ $note->category->name }}
                                </span>
                            @endif
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $note->created_at->format('d M Y') }}
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $note->created_at->format('H:i') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Tags -->
                @if($note->tags->count() > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach($note->tags as $tag)
                            <span class="px-3 py-1 bg-white/10 backdrop-blur-sm rounded-lg text-sm">
                                #{{ $tag->name }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Content Section -->
            <div class="p-6 md:p-8">
                <div class="prose prose-slate max-w-none prose-headings:text-[#1E293B] prose-p:text-[#1E293B] prose-a:text-[#2C74B3] prose-strong:text-[#1E293B] prose-code:text-[#2C74B3] prose-pre:bg-[#F1F5F9] prose-li:text-[#1E293B]">
                    {!! format_note_content($note->content) !!}
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="p-6 bg-[#F9FAFB] border-t border-[#E5E7EB] flex flex-wrap items-center gap-3">
                <a 
                    href="{{ route('notes.edit', $note) }}"
                    class="bg-[#2C74B3] hover:bg-[#205295] text-white font-medium px-6 py-3 rounded-xl transition-colors flex items-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit Catatan
                </a>

                <a 
                    href="{{ route('notes.export-pdf', $note) }}"
                    class="border border-[#E5E7EB] hover:bg-white text-[#1E293B] font-medium px-6 py-3 rounded-xl transition-colors flex items-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                    </svg>
                    Export PDF
                </a>

                <button 
                    @click="showShareModal = true"
                    class="border border-[#2C74B3] bg-[#2C74B3] hover:bg-[#205295] text-white font-medium px-6 py-3 rounded-xl transition-colors flex items-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                    </svg>
                    Bagikan
                </button>

                <a
                    href="{{ route('notes.resummarize', $note) }}"
                    class="border border-[#E5E7EB] hover:bg-white text-[#1E293B] font-medium px-6 py-3 rounded-xl transition-colors flex items-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Re-summarize
                </a>

                <form action="{{ route('notes.destroy', $note) }}" method="POST" class="ml-auto">
                    @csrf
                    @method('DELETE')
                    <button 
                        type="submit"
                        onclick="return confirm('Yakin ingin menghapus catatan ini?')"
                        class="border border-red-200 hover:bg-red-50 text-red-600 font-medium px-6 py-3 rounded-xl transition-colors flex items-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Hapus
                    </button>
                </form>
            </div>
        </div>

        <!-- Metadata Card -->
        <div class="mt-6 bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB]">
            <h3 class="text-lg font-bold text-[#1E293B] mb-4">Informasi</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <p class="text-[#1E293B]/60 mb-1">Dibuat</p>
                    <p class="text-[#1E293B] font-medium">{{ $note->created_at->format('d M Y, H:i') }}</p>
                </div>
                <div>
                    <p class="text-[#1E293B]/60 mb-1">Terakhir Diubah</p>
                    <p class="text-[#1E293B] font-medium">{{ $note->updated_at->format('d M Y, H:i') }}</p>
                </div>
                <div>
                    <p class="text-[#1E293B]/60 mb-1">Jumlah Karakter</p>
                    <p class="text-[#1E293B] font-medium">{{ strlen($note->content) }} karakter</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Share Modal -->
    <div x-show="showShareModal"
         x-cloak
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
         @click.self="showShareModal = false">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6" @click.stop>
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-[#1E293B]">Bagikan Catatan</h3>
                <button @click="showShareModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div x-show="!shareData">
                <p class="text-sm text-[#1E293B]/60 mb-6">Generate kode untuk membagikan catatan ini. Pengguna lain bisa import catatan ini dengan kode atau link yang Anda bagikan.</p>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-[#1E293B] mb-2">Masa Berlaku</label>
                    <select x-model="expiresIn" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#2C74B3] focus:ring-2 focus:ring-[#2C74B3]/20 outline-none">
                        <option value="1hour">1 Jam</option>
                        <option value="1day" selected>1 Hari</option>
                        <option value="7days">7 Hari</option>
                        <option value="30days">30 Hari</option>
                        <option value="never">Tanpa Batas</option>
                    </select>
                </div>

                <button 
                    @click="generateShareCode()"
                    :disabled="isGenerating"
                    class="w-full bg-[#2C74B3] hover:bg-[#205295] text-white font-medium px-6 py-3 rounded-xl transition-colors disabled:opacity-50"
                >
                    <span x-show="!isGenerating">Generate Kode Share</span>
                    <span x-show="isGenerating">Generating...</span>
                </button>
            </div>

            <div x-show="shareData" x-cloak>
                <div class="bg-green-50 border-2 border-green-200 rounded-xl p-4 mb-6">
                    <p class="text-sm text-green-800 mb-3">✅ Kode share berhasil dibuat!</p>
                    <div class="bg-white rounded-lg p-4 mb-3">
                        <p class="text-xs text-gray-500 mb-1">Kode Share:</p>
                        <div class="flex items-center gap-2">
                            <code class="text-2xl font-bold text-[#2C74B3] tracking-widest" x-text="shareData?.share_code"></code>
                            <button @click="copyCode()" class="p-2 hover:bg-gray-100 rounded-lg" title="Salin kode">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">Link Share:</p>
                        <div class="flex items-center gap-2">
                            <code class="text-xs text-gray-700 break-all flex-1" x-text="shareData?.share_url"></code>
                            <button @click="copyLink()" class="p-2 hover:bg-gray-100 rounded-lg flex-shrink-0" title="Salin link">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <p class="text-xs text-gray-600 mt-3" x-show="shareData?.expires_at">
                        Berlaku hingga: <span x-text="shareData?.expires_at"></span>
                    </p>
                    <p class="text-xs text-gray-600 mt-1" x-show="!shareData?.expires_at">
                        Berlaku tanpa batas waktu
                    </p>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-4">
                    <p class="text-sm text-blue-800 font-medium mb-2">📋 Cara Membagikan:</p>
                    <ol class="text-xs text-blue-700 space-y-1 ml-4 list-decimal">
                        <li>Salin kode atau link di atas</li>
                        <li>Kirim ke teman via WA, email, atau platform lainnya</li>
                        <li>Teman Anda bisa buka link atau input kode di menu "Import Catatan"</li>
                        <li>Mereka bisa menyimpan catatan ini ke akun mereka sendiri</li>
                    </ol>
                </div>

                <button 
                    @click="shareData = null; showShareModal = false"
                    class="w-full bg-gray-100 hover:bg-gray-200 text-[#1E293B] font-medium px-6 py-3 rounded-xl transition-colors"
                >
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function copyToClipboard() {
        const content = `{{ $note->title }}\n\n{{ $note->content }}`;
        navigator.clipboard.writeText(content).then(() => {
            alert('Catatan berhasil disalin!');
        });
    }
</script>
@endpush

@push('styles')
<style>
    /* Enhanced prose/markdown styling */
    .prose {
        font-size: 1rem;
        line-height: 1.75;
    }
    
    .prose h1 {
        font-size: 1.875rem;
        font-weight: 800;
        margin-top: 0;
        margin-bottom: 1rem;
        color: #1E293B;
    }
    
    .prose h2 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
        color: #1E293B;
    }
    
    .prose h3 {
        font-size: 1.25rem;
        font-weight: 600;
        margin-top: 1.25rem;
        margin-bottom: 0.5rem;
        color: #1E293B;
    }
    
    .prose p {
        margin-top: 0.75rem;
        margin-bottom: 0.75rem;
        color: #1E293B;
    }
    
    .prose ul, .prose ol {
        margin-top: 0.75rem;
        margin-bottom: 0.75rem;
        padding-left: 1.625rem;
    }
    
    .prose ul {
        list-style-type: disc;
    }
    
    .prose ol {
        list-style-type: decimal;
    }
    
    .prose li {
        margin-top: 0.25rem;
        margin-bottom: 0.25rem;
        color: #1E293B;
    }
    
    .prose strong {
        font-weight: 700 !important;
        color: #1E293B !important;
    }
    
    .prose em {
        font-style: italic;
    }
    
    .prose code {
        background-color: #F1F5F9;
        color: #2C74B3;
        padding: 0.125rem 0.375rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
        font-family: 'Courier New', monospace;
    }
    
    .prose pre {
        background-color: #F1F5F9;
        padding: 1rem;
        border-radius: 0.5rem;
        overflow-x: auto;
        margin-top: 1rem;
        margin-bottom: 1rem;
    }
    
    .prose pre code {
        background-color: transparent;
        padding: 0;
    }
    
    .prose blockquote {
        border-left: 4px solid #2C74B3;
        padding-left: 1rem;
        font-style: italic;
        color: #64748b;
        margin: 1rem 0;
    }
    
    .prose a {
        color: #2C74B3;
        text-decoration: underline;
    }
    
    .prose a:hover {
        color: #205295;
    }
    
    .prose hr {
        border: 0;
        border-top: 1px solid #E5E7EB;
        margin: 1.5rem 0;
    }
    
    .prose table {
        width: 100%;
        border-collapse: collapse;
        margin: 1rem 0;
    }
    
    .prose th, .prose td {
        border: 1px solid #E5E7EB;
        padding: 0.5rem;
        text-align: left;
    }
    
    .prose th {
        background-color: #F9FAFB;
        font-weight: 600;
    }
    
    /* Highlight styles for AI-generated content */
    .prose mark {
        padding: 2px 4px;
        border-radius: 3px;
        font-weight: 500;
    }
    
    .prose mark[style*="yellow"] {
        background-color: #fef08a !important;
        color: #854d0e;
    }
    
    .prose mark[style*="#90EE90"], .prose mark[style*="lightgreen"] {
        background-color: #bbf7d0 !important;
        color: #14532d;
    }
    
    .prose mark[style*="#FFB6C1"], .prose mark[style*="pink"] {
        background-color: #fbcfe8 !important;
        color: #831843;
    }
    
    .prose mark[style*="#87CEEB"], .prose mark[style*="skyblue"] {
        background-color: #bfdbfe !important;
        color: #1e3a8a;
    }
    
    @media print {
        .prose {
            max-width: none !important;
        }
        .prose mark {
            background-color: #f3f4f6 !important;
            color: inherit !important;
            border: 1px solid #d1d5db;
        }
    }
</style>
@endpush

<!-- Re-summarize Modal -->
<div 
    x-show="$store.resummary.isOpen" 
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;"
>
    <!-- Backdrop -->
    <div 
        class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"
        @click="$store.resummary.close()"
    ></div>
    
    <!-- Modal -->
    <div class="flex min-h-screen items-center justify-center p-4">
        <div 
            class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-8 transform transition-all"
            @click.stop
        >
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-2xl font-bold text-[#1E293B]">🔄 Re-summarize Catatan</h3>
                    <p class="text-sm text-gray-500 mt-1">Ubah format atau perintah summary</p>
                </div>
                <button 
                    @click="$store.resummary.close()"
                    class="text-gray-400 hover:text-gray-600 transition-colors"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Quick Commands -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-[#1E293B] mb-3">Quick Commands:</label>
                <div class="grid grid-cols-2 gap-2">
                    <button 
                        @click="$store.resummary.setInstruction('Ringkas menjadi poin-poin penting (bullet points)')"
                        class="p-3 text-left border-2 border-gray-200 rounded-xl hover:border-[#2C74B3] hover:bg-blue-50 transition-all"
                    >
                        <div class="font-semibold text-sm text-[#1E293B]">📝 Poin Penting</div>
                        <div class="text-xs text-gray-500">Bullet points ringkas</div>
                    </button>
                    
                    <button 
                        @click="$store.resummary.setInstruction('Ubah menjadi format tabel yang terstruktur')"
                        class="p-3 text-left border-2 border-gray-200 rounded-xl hover:border-[#2C74B3] hover:bg-blue-50 transition-all"
                    >
                        <div class="font-semibold text-sm text-[#1E293B]">📊 Tabel</div>
                        <div class="text-xs text-gray-500">Format tabel terstruktur</div>
                    </button>
                    
                    <button 
                        @click="$store.resummary.setInstruction('Buat ringkasan singkat maksimal 3 paragraf')"
                        class="p-3 text-left border-2 border-gray-200 rounded-xl hover:border-[#2C74B3] hover:bg-blue-50 transition-all"
                    >
                        <div class="font-semibold text-sm text-[#1E293B]">✂️ Ringkas</div>
                        <div class="text-xs text-gray-500">3 paragraf singkat</div>
                    </button>
                    
                    <button 
                        @click="$store.resummary.setInstruction('Buat mind map atau outline hierarki')"
                        class="p-3 text-left border-2 border-gray-200 rounded-xl hover:border-[#2C74B3] hover:bg-blue-50 transition-all"
                    >
                        <div class="font-semibold text-sm text-[#1E293B]">🗺️ Mind Map</div>
                        <div class="text-xs text-gray-500">Outline hierarki</div>
                    </button>
                    
                    <button 
                        @click="$store.resummary.setInstruction('Translate to English and summarize')"
                        class="p-3 text-left border-2 border-gray-200 rounded-xl hover:border-[#2C74B3] hover:bg-blue-50 transition-all"
                    >
                        <div class="font-semibold text-sm text-[#1E293B]">🌐 English</div>
                        <div class="text-xs text-gray-500">Translate & summarize</div>
                    </button>
                    
                    <button 
                        @click="$store.resummary.setInstruction('Tambahkan contoh dan analisis mendalam')"
                        class="p-3 text-left border-2 border-gray-200 rounded-xl hover:border-[#2C74B3] hover:bg-blue-50 transition-all"
                    >
                        <div class="font-semibold text-sm text-[#1E293B]">🔍 Detail</div>
                        <div class="text-xs text-gray-500">Dengan contoh & analisis</div>
                    </button>
                </div>
            </div>

            <!-- Custom Instruction -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-[#1E293B] mb-2">Custom Instruction:</label>
                <textarea 
                    x-model="$store.resummary.instruction"
                    placeholder="Atau tulis perintah custom Anda sendiri..."
                    rows="3"
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#2C74B3] focus:ring-2 focus:ring-[#2C74B3]/20 outline-none transition resize-none"
                ></textarea>
                <p class="text-xs text-gray-500 mt-2">
                    Contoh: "Ubah ke format QnA", "Tambahkan kesimpulan di akhir", "Buat lebih formal"
                </p>
            </div>

            <!-- Loading State -->
            <div x-show="$store.resummary.isLoading" class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-xl">
                <div class="flex items-center gap-3">
                    <svg class="animate-spin h-5 w-5 text-[#2C74B3]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-sm font-medium text-[#2C74B3]">Memproses re-summary...</span>
                </div>
            </div>

            <!-- Error State -->
            <div x-show="$store.resummary.error" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-red-800">Error</p>
                        <p class="text-sm text-red-600" x-text="$store.resummary.error"></p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-3">
                <button 
                    @click="$store.resummary.close()"
                    class="flex-1 px-6 py-3 border border-gray-300 rounded-xl hover:bg-gray-50 font-medium text-gray-700 transition-colors"
                >
                    Batal
                </button>
                <button 
                    @click="$store.resummary.submit()"
                    :disabled="$store.resummary.isLoading || !$store.resummary.instruction"
                    :class="$store.resummary.isLoading || !$store.resummary.instruction ? 'opacity-50 cursor-not-allowed' : 'hover:bg-[#205295]'"
                    class="flex-1 px-6 py-3 bg-[#2C74B3] text-white rounded-xl font-medium transition-colors"
                >
                    <span x-show="!$store.resummary.isLoading">🔄 Re-summarize</span>
                    <span x-show="$store.resummary.isLoading">Processing...</span>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Alpine.js store for re-summarize
    document.addEventListener('alpine:init', () => {
        Alpine.store('resummary', {
            isOpen: false,
            isLoading: false,
            error: null,
            noteId: null,
            content: '',
            instruction: '',
            
            open(noteId, content) {
                this.isOpen = true;
                this.noteId = noteId;
                this.content = content;
                this.instruction = '';
                this.error = null;
                this.isLoading = false;
            },
            
            close() {
                this.isOpen = false;
                this.noteId = null;
                this.content = '';
                this.instruction = '';
                this.error = null;
                this.isLoading = false;
            },
            
            setInstruction(text) {
                this.instruction = text;
            },
            
            async submit() {
                if (!this.instruction.trim()) {
                    this.error = 'Silakan pilih quick command atau tulis instruction custom';
                    return;
                }
                
                this.isLoading = true;
                this.error = null;
                
                try {
                    const formData = new FormData();
                    formData.append('content', this.content);
                    formData.append('instructions', JSON.stringify([this.instruction]));
                    formData.append('note_id', this.noteId);
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                    
                    const response = await fetch('/summarize/regenerate', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (!response.ok) {
                        throw new Error(data.error || 'Terjadi kesalahan saat memproses');
                    }
                    
                    if (data.success) {
                        // Show success message and reload page
                        this.close();
                        
                        // Create success toast
                        const toast = document.createElement('div');
                        toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-xl shadow-lg z-50 flex items-center gap-2';
                        toast.innerHTML = `
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Re-summarize berhasil! Memuat ulang...</span>
                        `;
                        document.body.appendChild(toast);
                        
                        // Reload page after 1 second
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        throw new Error(data.error || 'Gagal memproses re-summary');
                    }
                } catch (error) {
                    console.error('Re-summary error:', error);
                    alert('Gagal memproses re-summary: ' + error.message);
                }
            }
        });
    });

    // Copy share link function
    function copyShareLink() {
        const noteUrl = window.location.href;
        navigator.clipboard.writeText(noteUrl).then(() => {
            // Show toast notification
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-xl shadow-lg flex items-center gap-3 z-50 animate-slide-up';
            toast.innerHTML = `
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Link berhasil disalin!</span>
            `;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 3000);
        });
    }

    // Generate share code
    async function generateShareCode() {
        const alpine = Alpine.$data(document.querySelector('[x-data]'));
        alpine.isGenerating = true;

        try {
            const response = await fetch('{{ route('notes.generate-share', $note) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    expires_in: alpine.expiresIn
                })
            });

            const data = await response.json();

            if (data.success) {
                alpine.shareData = data;
            } else {
                alert('Gagal generate kode share');
            }
        } catch (error) {
            console.error(error);
            alert('Terjadi kesalahan');
        } finally {
            alpine.isGenerating = false;
        }
    }

    // Copy share code
    function copyCode() {
        const alpine = Alpine.$data(document.querySelector('[x-data]'));
        navigator.clipboard.writeText(alpine.shareData.share_code).then(() => {
            showToast('Kode berhasil disalin!');
        });
    }

    // Copy share link
    function copyLink() {
        const alpine = Alpine.$data(document.querySelector('[x-data]'));
        navigator.clipboard.writeText(alpine.shareData.share_url).then(() => {
            showToast('Link berhasil disalin!');
        });
    }

    // Show toast helper
    function showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-xl shadow-lg flex items-center gap-3 z-50 animate-slide-up';
        toast.innerHTML = `
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span>${message}</span>
        `;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }
</script>
@endpush

@push('styles')
<style>
    [x-cloak] { display: none !important; }
    
    @keyframes slide-up {
        from {
            transform: translateY(100%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    
    .animate-slide-up {
        animation: slide-up 0.3s ease-out;
    }
</style>
@endpush

@endsection
