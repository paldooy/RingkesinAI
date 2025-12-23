@extends('layouts.app')

@section('title', 'AI Summarize - Ringkesin')

@section('content')
<div class="flex-1 bg-[#F9FAFB] overflow-auto">
    <div class="max-w-5xl mx-auto p-4 md:p-8" x-data="aiSummarize()">
        <!-- Header -->
        <div class="my-6 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-[#2C74B3] to-purple-600 rounded-2xl mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-[#1E293B] mb-2">AI Summarize Assistant</h1>
            <p class="text-[#1E293B]/60">
                Upload file PDF atau DOCX, berikan instruksi tambahan, dan biarkan AI meringkas dokumenmu
            </p>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-600 rounded-xl p-4 mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- Upload Section -->
        <div class="bg-white rounded-2xl p-6 mb-6 border-2 border-[#A7C7E7] shadow-lg">
            <div x-show="!uploadedFile">
                <label class="flex flex-col items-center justify-center cursor-pointer group py-4">
                    <input
                        type="file"
                        accept=".pdf,.doc,.docx,.txt"
                        @change="handleFileUpload($event)"
                        class="hidden"
                    />
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-[#2C74B3] to-purple-600 rounded-2xl mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl text-[#1E293B] mb-2">Upload Dokumen</h3>
                    <p class="text-sm text-[#1E293B]/60 mb-4 text-center">
                        Klik untuk memilih file atau drag & drop di sini
                    </p>
                    <div class="flex gap-2">
                        <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-lg text-xs">PDF</span>
                        <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-lg text-xs">DOC</span>
                        <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-lg text-xs">DOCX</span>
                        <span class="px-3 py-1 bg-green-50 text-green-700 rounded-lg text-xs">TXT</span>
                    </div>
                    <p class="text-xs text-[#1E293B]/40 mt-4">
                        Maksimal ukuran file: 10 MB
                    </p>
                </label>
            </div>

            <div x-show="uploadedFile" x-cloak>
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-2 h-2 rounded-full bg-[#2C74B3]"></div>
                    <h2 class="text-lg text-[#1E293B]">File Terupload</h2>
                </div>

                <!-- File Preview -->
                <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl p-4 border-2 border-blue-100 mb-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-[#2C74B3] to-purple-600 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[#1E293B] truncate" x-text="uploadedFile?.name"></p>
                            <p class="text-xs text-[#1E293B]/50" x-text="fileSize"></p>
                        </div>
                        <button
                            @click="removeFile()"
                            class="p-2 hover:bg-red-50 rounded-lg transition-colors text-[#1E293B]/60 hover:text-red-600"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chatbot Section - Only show when file is uploaded -->
        <div x-show="uploadedFile && !summary" x-cloak>
            <div class="bg-white rounded-2xl p-6 mb-6 border-2 border-[#E5E7EB]">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-2 h-2 rounded-full bg-purple-600"></div>
                    <h2 class="text-lg text-[#1E293B]">Instruksi Tambahan (Opsional)</h2>
                </div>
                <p class="text-sm text-[#1E293B]/60 mb-4">
                    Berikan instruksi spesifik untuk hasil ringkasan yang lebih sesuai kebutuhanmu
                </p>

                <!-- Chat Messages -->
                <div x-show="chatMessages.length > 0" class="mb-4 space-y-3 max-h-60 overflow-y-auto">
                    <template x-for="(message, index) in chatMessages" :key="index">
                        <div :class="message.type === 'user' ? 'justify-end' : 'justify-start'" class="flex">
                            <div 
                                :class="message.type === 'user' ? 'bg-[#2C74B3] text-white' : 'bg-[#F9FAFB] text-[#1E293B] border border-[#E5E7EB]'"
                                class="max-w-[80%] p-3 rounded-xl"
                            >
                                <p class="text-sm" x-text="message.text"></p>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Chat Input -->
                <div class="flex gap-2">
                    <textarea
                        x-model="chatInput"
                        @keydown.enter.prevent="if(!$event.shiftKey) sendMessage()"
                        placeholder="Contoh: Fokus pada rumus-rumus penting saja, Buat dalam bentuk bullet points..."
                        class="flex-1 min-h-[60px] max-h-[120px] px-4 py-3 rounded-xl border border-[#E5E7EB] focus:border-[#2C74B3] focus:ring-2 focus:ring-[#2C74B3]/20 outline-none transition resize-none"
                    ></textarea>
                    <button
                        @click="sendMessage()"
                        :disabled="!chatInput.trim()"
                        class="bg-purple-600 hover:bg-purple-700 disabled:bg-gray-300 text-white rounded-xl px-4 transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </button>
                </div>

                <!-- Generate Button -->
                <div class="mt-4 pt-4 border-t border-[#E5E7EB]">
                    <button
                        @click="generateSummary()"
                        :disabled="isLoading"
                        class="w-full bg-gradient-to-r from-[#2C74B3] to-purple-600 border-2 border-[#A7C7E7] hover:from-[#205295] hover:to-purple-700 disabled:from-gray-300 disabled:to-gray-400 text-white rounded-xl py-4 font-medium transition-all"
                    >
                        <span x-show="!isLoading" class="flex items-center justify-center gap-2 ">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                            </svg>
                            Generate Ringkasan AI
                        </span>
                        <span x-show="isLoading" class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Memproses dokumen...
                        </span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Info Cards - Only show when no file uploaded -->
        <div x-show="!uploadedFile && !summary" class="grid grid-cols-2 gap-2 md:gap-6">
            <div class="bg-white rounded-lg md:rounded-2xl p-3 md:p-6 border-2 border-[#E5E7EB]">
                <h3 class="text-sm md:text-lg font-bold text-[#1E293B] mb-3">✨ Fitur AI</h3>
                <ul class="space-y-2 text-xs text-[#1E293B]/70">
                    <li>• Upload PDF atau Word dokumen</li>
                    <li>• Berikan instruksi khusus via chat</li>
                    <li>• Ekstrak poin-poin penting otomatis</li>
                    <li>• Simpan hasil langsung ke kategori</li>
                </ul>
            </div>

            <div class="bg-gradient-to-br from-[#2C74B3] to-purple-600 rounded-lg md:rounded-2xl p-3 md:p-6 text-white">
                <h3 class="text-sm md:text-lg font-bold mb-3">💡 Tips Penggunaan</h3>
                <ul class="space-y-2 text-xs text-white/90">
                    <li>• Upload file catatan pelajaran kamu</li>
                    <li>• Berikan instruksi spesifik jika perlu</li>
                    <li>• AI akan membaca dan meringkas otomatis</li>
                    <li>• Pilih kategori sebelum menyimpan</li>
                </ul>
            </div>
        </div>

        <!-- Result Section -->
        <div x-show="summary" x-cloak class="bg-white rounded-2xl border-2 border-[#A7C7E7] shadow-xl overflow-hidden">
            <!-- Result Header -->
            <div class="bg-gradient-to-r from-[#A7C7E7] to-[#2C74B3] p-6 text-white">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <div>
                            <h2 class="text-xl font-bold">Hasil Ringkasan AI</h2>
                            <p class="text-white/80 text-sm">
                                Ringkasan telah dibuat dan siap digunakan
                            </p>
                        </div>
                    </div>
                    <div class="text-xs text-white/70 text-right" x-show="summaryMetadata">
                        <div x-show="summaryMetadata?.model" class="mb-1">
                            🤖 <span x-text="summaryMetadata?.model"></span>
                        </div>
                        <div x-show="summaryMetadata?.cached" class="text-green-300">
                            ⚡ Dari cache
                        </div>
                    </div>
                </div>
            </div>

            <!-- Truncation Warning -->
            <div x-show="summaryMetadata?.truncated" class="px-6 pt-6">
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-yellow-800 mb-1">⚠️ Ringkasan Terpotong</p>
                            <p class="text-xs text-yellow-700">
                                Dokumen terlalu panjang dan ringkasan melebihi batas token AI (8192 tokens). 
                                Pertimbangkan untuk membagi dokumen atau fokuskan pada bagian tertentu.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Result Content -->
            <div class="p-6 bg-white">
                <div class="prose prose-slate max-w-none prose-headings:text-[#1E293B] prose-p:text-[#1E293B] prose-a:text-[#2C74B3] prose-strong:text-[#1E293B] prose-strong:font-bold prose-code:text-[#2C74B3] prose-pre:bg-[#F1F5F9] prose-li:text-[#1E293B] prose-ul:list-disc prose-ol:list-decimal" x-html="formatMarkdown(summary)"></div>
            </div>

            <!-- Revision Chatbot Section -->
            <div class="p-6 bg-gradient-to-br from-purple-50 to-blue-50 border-t border-purple-200" @click.stop>
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                    <h3 class="text-lg font-bold text-[#1E293B]">💬 Revisi Hasil</h3>
                </div>
                <p class="text-sm text-[#1E293B]/60 mb-4">
                    Belum puas dengan hasilnya? Berikan perintah untuk merevisi summary
                </p>

                <!-- Quick Revision Commands -->
                <div class="grid grid-cols-2 gap-2 mb-4">
                    <button 
                        type="button"
                        @click="revisionInstruction = 'Ringkas menjadi poin-poin penting (bullet points)'"
                        class="p-2 text-left border-2 border-purple-200 rounded-lg hover:border-purple-400 hover:bg-white transition-all text-xs"
                    >
                        <div class="font-semibold text-[#1E293B]">📝 Poin Penting</div>
                    </button>
                    <button 
                        type="button"
                        @click="revisionInstruction = 'Ubah menjadi format tabel yang terstruktur'"
                        class="p-2 text-left border-2 border-purple-200 rounded-lg hover:border-purple-400 hover:bg-white transition-all text-xs"
                    >
                        <div class="font-semibold text-[#1E293B]">📊 Format Tabel</div>
                    </button>
                    <button 
                        type="button"
                        @click="revisionInstruction = 'Buat lebih ringkas (maksimal 3 paragraf)'"
                        class="p-2 text-left border-2 border-purple-200 rounded-lg hover:border-purple-400 hover:bg-white transition-all text-xs"
                    >
                        <div class="font-semibold text-[#1E293B]">✂️ Lebih Ringkas</div>
                    </button>
                    <button 
                        type="button"
                        @click="revisionInstruction = 'Tambahkan contoh dan penjelasan detail'"
                        class="p-2 text-left border-2 border-purple-200 rounded-lg hover:border-purple-400 hover:bg-white transition-all text-xs"
                    >
                        <div class="font-semibold text-[#1E293B]">🔍 Lebih Detail</div>
                    </button>
                </div>

                <!-- Custom Revision Input -->
                <div class="flex gap-2">
                    <input 
                        type="text"
                        x-model="revisionInstruction"
                        placeholder="Ketik perintah revisi atau pilih quick button di atas"
                        @keydown.enter.prevent="reviseSummary()"
                        class="flex-1 px-4 py-3 rounded-xl border-2 border-purple-200 focus:border-purple-400 focus:ring-2 focus:ring-purple-400/20 outline-none transition text-sm"
                    />
                    <button
                        type="button"
                        @click.prevent="reviseSummary()"
                        :disabled="revising"
                        class="bg-purple-600 hover:bg-purple-700 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed text-white px-6 py-3 rounded-xl font-medium transition-all flex items-center gap-2 shadow-lg hover:shadow-xl"
                    >
                        <svg x-show="!revising" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <svg x-show="revising" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="revising ? 'Merevisi...' : 'Revisi'"></span>
                    </button>
                </div>

                <!-- Revision Status -->
                <div x-show="revisionError" class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-sm text-red-600" x-text="revisionError"></p>
                </div>
                <div x-show="revisionSuccess" class="mt-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-sm text-green-600">✅ Summary berhasil direvisi!</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="p-6 bg-[#F9FAFB] border-t border-[#E5E7EB] flex flex-wrap items-center gap-3">
                <button
                    @click="showCategoryModal = true"
                    class="bg-[#2C74B3] hover:bg-[#205295] text-white rounded-xl px-6 py-3 font-medium transition-colors flex items-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    Simpan ke Catatan
                </button>
                <button
                    @click="copySummary()"
                    class="border border-[#E5E7EB] hover:bg-white text-[#1E293B] rounded-xl px-6 py-3 font-medium transition-colors flex items-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    <span x-text="copied ? 'Tersalin!' : 'Salin'"></span>
                </button>
                <button
                    @click="resetForm()"
                    class="ml-auto border border-[#E5E7EB] hover:bg-white text-[#1E293B] rounded-xl px-6 py-3 font-medium transition-colors"
                >
                    Upload File Baru
                </button>
            </div>
        </div>

        <!-- Category Selection Modal -->
        <div 
            x-show="showCategoryModal"
            x-cloak
            class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
            @click.self="showCategoryModal = false"
        >
            <div class="bg-white rounded-2xl max-w-md w-full p-6" @click.stop>
                <h2 class="text-2xl font-bold text-[#1E293B] mb-2">Pilih Kategori</h2>
                <p class="text-sm text-[#1E293B]/60 mb-6">
                    Pilih kategori untuk menyimpan ringkasan ini
                </p>

                <form @submit.prevent="saveSummary()">
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-[#1E293B] mb-3">
                            Kategori Catatan
                        </label>
                        
                        <div class="space-y-3">
                            <select 
                                x-model="selectedCategory"
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#2C74B3] focus:ring-2 focus:ring-[#2C74B3]/20 outline-none transition"
                            >
                                <option value="">Pilih kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->icon }} {{ $category->name }}</option>
                                @endforeach
                                <option value="new">➕ Buat Kategori Baru</option>
                            </select>

                            <!-- New Category Form (Show when "Buat Kategori Baru" selected) -->
                            <div x-show="selectedCategory === 'new'" x-cloak class="space-y-3 p-4 bg-blue-50 rounded-xl border border-blue-200">
                                <div>
                                    <label class="block text-xs font-medium text-[#1E293B] mb-2">Nama Kategori Baru</label>
                                    <input 
                                        type="text"
                                        x-model="newCategoryName"
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
                                                x-model="newCategoryEmoji"
                                                @click="showEmojiPicker = !showEmojiPicker"
                                                readonly
                                                placeholder="📚"
                                                class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-[#2C74B3] focus:ring-2 focus:ring-[#2C74B3]/20 outline-none transition text-sm text-center cursor-pointer"
                                            />
                                            
                                            <!-- Emoji Picker Dropdown -->
                                            <div x-show="showEmojiPicker" 
                                                 @click.away="showEmojiPicker = false"
                                                 x-cloak
                                                 class="absolute z-50 mt-2 w-64 bg-white rounded-xl shadow-2xl border-2 border-[#2C74B3] p-3 max-h-48 overflow-y-auto">
                                                <div class="grid grid-cols-8 gap-1">
                                                    <template x-for="emoji in emojis" :key="emoji">
                                                        <button 
                                                            type="button"
                                                            @click="newCategoryEmoji = emoji; showEmojiPicker = false"
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
                                            x-model="newCategoryColor"
                                            class="w-full h-10 rounded-lg border border-gray-300 cursor-pointer"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-[#1E293B] mb-3">
                            Judul Catatan (Opsional)
                        </label>
                        <input 
                            type="text"
                            x-model="noteTitle"
                            :placeholder="'Ringkasan: ' + (uploadedFile?.name || 'Dokumen')"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#2C74B3] focus:ring-2 focus:ring-[#2C74B3]/20 outline-none transition"
                        />
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-[#1E293B] mb-3">
                            Tag (Opsional)
                        </label>
                        
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
                                    <span x-text="tag"></span>
                                    <button type="button" @click="tags.splice(index, 1)" class="hover:text-blue-900">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </span>
                            </template>
                        </div>

                        <p class="text-xs text-[#1E293B]/50 mt-2">
                            Tekan Enter atau klik + untuk menambah tag
                        </p>
                    </div>

                    <div class="flex gap-3">
                        <button
                            type="submit"
                            :disabled="!selectedCategory"
                            class="flex-1 bg-[#2C74B3] hover:bg-[#205295] disabled:bg-gray-300 text-white rounded-xl py-3 font-medium transition-colors"
                        >
                            Simpan
                        </button>
                        <button
                            type="button"
                            @click="showCategoryModal = false"
                            class="flex-1 border border-[#E5E7EB] hover:bg-[#F9FAFB] text-[#1E293B] rounded-xl py-3 font-medium transition-colors"
                        >
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
function aiSummarize() {
    return {
        uploadedFile: null,
        fileSize: '',
        chatMessages: [],
        chatInput: '',
        summary: '',
        isLoading: false,
        copied: false,
        showCategoryModal: false,
        selectedCategory: '',
        noteTitle: '',
        summaryMetadata: null,
        tags: [],
        tagInput: '',
        
        // Re-summarize data
        isResummarize: {{ isset($resummarizeData) ? 'true' : 'false' }},
        resummarizeNoteId: {{ isset($resummarizeData) ? $resummarizeData['note_id'] : 'null' }},
        resummarizeContent: @json(isset($resummarizeData) ? $resummarizeData['content'] : null),
        resummarizeTitle: @json(isset($resummarizeData) ? $resummarizeData['title'] : null),
        
        // New category fields
        newCategoryName: '',
        newCategoryEmoji: '📁',
        newCategoryColor: '#3B82F6',
        showEmojiPicker: false,
        emojis: ['📁', '📚', '📖', '📝', '📊', '💼', '🎓', '🔬', '🧪', '📐', '📏', '🖊️', '✏️', '📌', '📍', '🎨', '🎭', '🎪', '🎬', '🎮', '🎯', '🎲', '🧩', '🎸', '🎹', '🎺', '🎻', '🥁', '💻', '⌨️', '🖥️', '🖨️', '📱', '☎️', '📞', '📟', '📠', '📡', '🔋', '🔌', '💡', '🔦', '🕯️', '🧯', '🛢️', '💰', '💴', '💵', '💶', '💷', '💸', '💳', '🧾', '✉️', '📧', '📨', '📩', '📤', '📥', '📦', '📫', '📪', '📬', '📭', '📮', '🗳️', '✒️', '🖋️', '🖌️', '🖍️', '📂', '🗂️', '📅', '📆', '🗒️', '🗓️', '📇', '📈', '📉', '📋', '📎', '🖇️', '✂️', '🗃️', '🗄️', '🗑️'],
        
        // Revision state
        revisionInstruction: '',
        revising: false,
        revisionError: null,
        revisionSuccess: false,
        originalFileContent: null, // Store original content for revisions

        init() {
            // If this is a re-summarize request, auto-load the content
            if (this.isResummarize && this.resummarizeContent) {
                this.uploadedFile = {
                    name: this.resummarizeTitle + ' (Re-summarize)',
                    size: this.resummarizeContent.length,
                    type: 'text/html'
                };
                this.fileSize = this.formatFileSize(this.resummarizeContent.length);
                this.originalFileContent = this.resummarizeContent;
                
                // Show info message
                this.$nextTick(() => {
                    const infoDiv = document.createElement('div');
                    infoDiv.className = 'bg-blue-50 border border-blue-200 text-blue-700 rounded-xl p-4 mb-6';
                    infoDiv.innerHTML = '📝 Mode Re-summarize: "' + this.resummarizeTitle + '". Berikan instruksi baru untuk mengubah ringkasan.';
                    const container = document.querySelector('.max-w-5xl');
                    if (container && container.children[1]) {
                        container.insertBefore(infoDiv, container.children[1]);
                    }
                });
            }
        },

        formatMarkdown(text) {
            if (!text) return '';
            
            // Configure marked options
            if (typeof marked !== 'undefined') {
                marked.setOptions({
                    breaks: true,
                    gfm: true,
                    headerIds: false,
                    mangle: false
                });
                
                // Simply convert markdown to HTML without any highlighting
                return marked.parse(text);
            }
            
            // Fallback if marked is not loaded
            return text.replace(/\n/g, '<br>');
        },

        handleFileUpload(event) {
            const file = event.target.files[0];
            if (file) {
                const validTypes = [
                    'application/pdf', 
                    'application/msword', 
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'text/plain'
                ];
                const validExtensions = ['.pdf', '.doc', '.docx', '.txt'];
                const fileName = file.name.toLowerCase();
                const hasValidExtension = validExtensions.some(ext => fileName.endsWith(ext));
                
                if (validTypes.includes(file.type) || hasValidExtension) {
                    // Check file size (max 10MB)
                    if (file.size > 10 * 1024 * 1024) {
                        alert('❌ File terlalu besar! Maksimal 10 MB');
                        return;
                    }
                    
                    this.uploadedFile = file;
                    this.fileSize = this.formatFileSize(file.size);
                    this.summary = '';
                    this.chatMessages = [];
                } else {
                    alert('❌ Mohon upload file PDF, DOC, DOCX, atau TXT saja');
                }
            }
        },

        removeFile() {
            this.uploadedFile = null;
            this.summary = '';
            this.chatMessages = [];
            this.chatInput = '';
            this.summaryMetadata = null;
        },

        sendMessage() {
            if (!this.chatInput.trim() || !this.uploadedFile) return;

            this.chatMessages.push({ type: 'user', text: this.chatInput });
            const userMessage = this.chatInput;
            this.chatInput = '';

            setTimeout(() => {
                this.chatMessages.push({
                    type: 'ai',
                    text: 'Saya akan meringkas dokumen dengan fokus pada: ' + userMessage
                });
            }, 1000);
        },

        async generateSummary() {
            if (!this.uploadedFile) return;

            this.isLoading = true;
            const formData = new FormData();
            
            // If re-summarize mode, send content directly instead of file
            if (this.isResummarize && this.resummarizeContent) {
                // Create a blob from the content to simulate file upload
                const blob = new Blob([this.resummarizeContent], { type: 'text/html' });
                const file = new File([blob], this.resummarizeTitle + '.html', { type: 'text/html' });
                formData.append('document', file);
            } else {
                formData.append('document', this.uploadedFile);
            }
            
            // Extract user instructions from chat messages
            const instructions = this.chatMessages
                .filter(msg => msg.type === 'user')
                .map((msg, index) => ({ id: index + 1, text: msg.text }));
            
            formData.append('instructions', JSON.stringify(instructions));

            try {
                const response = await fetch('{{ route("summarize.generate") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });

                if (!response.ok) {
                    const errorText = await response.text();
                    try {
                        const errorData = JSON.parse(errorText);
                        throw new Error(errorData.error || 'Terjadi kesalahan pada server');
                    } catch (e) {
                        throw new Error(`Server error: ${response.status}`);
                    }
                }

                const data = await response.json();
                
                if (data.success) {
                    this.summary = data.summary;
                    
                    // Store original content for revisions
                    if (data.original_content) {
                        this.originalFileContent = data.original_content;
                        console.log('✅ Original content stored for revisions:', this.originalFileContent.substring(0, 100) + '...');
                    } else {
                        console.warn('⚠️ No original_content in response!');
                    }
                    
                    // Store metadata for display
                    this.summaryMetadata = data.metadata || {
                        model: data.model || null,
                        tokens_used: data.tokens_used || 0,
                        cached: data.cached || false,
                        truncated: data.truncated || false,
                        finish_reason: data.finish_reason || 'STOP'
                    };
                    
                    // Show success notification with details
                    const details = [];
                    if (this.summaryMetadata.model) details.push(`Model: ${this.summaryMetadata.model}`);
                    if (this.summaryMetadata.tokens_used) details.push(`Tokens: ${this.summaryMetadata.tokens_used}`);
                    if (this.summaryMetadata.cached) details.push('✓ Dari cache');
                    if (this.summaryMetadata.truncated) details.push('⚠️ Terpotong (melebihi limit)');
                    
                    if (details.length > 0) {
                        console.log('✅ AI Summary Details:', details.join(' | '));
                    }
                } else {
                    throw new Error(data.error || 'Gagal menghasilkan ringkasan');
                }
            } catch (error) {
                console.error('Summary generation error:', error);
                alert('❌ ' + error.message);
            } finally {
                this.isLoading = false;
            }
        },

        async saveSummary() {
            // Validate category selection or new category creation
            if (!this.selectedCategory) {
                alert('❌ Mohon pilih kategori terlebih dahulu!');
                return;
            }

            if (this.selectedCategory === 'new') {
                if (!this.newCategoryName || !this.newCategoryName.trim()) {
                    alert('❌ Mohon isi nama kategori baru!');
                    return;
                }
            }

            try {
                const title = this.noteTitle || (this.isResummarize ? this.resummarizeTitle : ('Ringkasan: ' + this.uploadedFile.name));
                
                // Debug: log content info before saving
                console.log('💾 Saving summary:', {
                    isResummarize: this.isResummarize,
                    noteId: this.resummarizeNoteId,
                    length: this.summary.length,
                    firstChars: this.summary.substring(0, 100),
                    hasHTML: /<[^>]+>/.test(this.summary),
                    linesCount: this.summary.split('\n').length,
                    tagsCount: this.tags.length,
                    categoryMode: this.selectedCategory === 'new' ? 'create new' : 'existing'
                });
                
                const requestBody = {
                    summary: this.summary, // Save raw markdown
                    title: title,
                    tags: this.tags
                };
                
                // If re-summarize mode, include note ID for update
                if (this.isResummarize && this.resummarizeNoteId) {
                    requestBody.note_id = this.resummarizeNoteId;
                }

                // If creating new category, include category data
                if (this.selectedCategory === 'new') {
                    requestBody.create_category = true;
                    requestBody.category_name = this.newCategoryName.trim();
                    requestBody.category_icon = this.newCategoryEmoji || '📁';
                    requestBody.category_color = this.newCategoryColor || '#3B82F6';
                } else {
                    requestBody.category_id = this.selectedCategory;
                }
                
                const response = await fetch('{{ route("summarize.save") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(requestBody)
                });

                // Check if response is JSON or HTML error page
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const errorText = await response.text();
                    console.error('Server returned non-JSON response:', errorText.substring(0, 500));
                    throw new Error('Server error: Response is not JSON. Check browser console for details.');
                }

                const data = await response.json();
                if (data.success) {
                    console.log('✅ Saved successfully, note ID:', data.note_id);
                    alert('✅ Ringkasan berhasil disimpan ke catatan!');
                    window.location.href = '{{ route("notes.index") }}';
                } else {
                    throw new Error(data.message || 'Gagal menyimpan');
                }
            } catch (error) {
                console.error('Save error:', error);
                alert('❌ ' + error.message);
            }
        },

        copySummary() {
            navigator.clipboard.writeText(this.summary);
            this.copied = true;
            setTimeout(() => this.copied = false, 2000);
        },

        resetForm() {
            this.uploadedFile = null;
            this.summary = '';
            this.chatMessages = [];
            this.chatInput = '';
            this.selectedCategory = '';
            this.noteTitle = '';
            this.tags = [];
            this.tagInput = '';
            this.summaryMetadata = null;
            this.revisionInstruction = '';
            this.revisionError = null;
            this.revisionSuccess = false;
            this.originalFileContent = null;
        },

        formatFileSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
        },

        // New method: Revise summary with new instruction
        async reviseSummary() {
            console.log('🔄 reviseSummary called', {
                instruction: this.revisionInstruction,
                hasOriginal: !!this.originalFileContent,
                revising: this.revising
            });

            if (!this.revisionInstruction || !this.revisionInstruction.trim()) {
                this.revisionError = '❌ Silakan masukkan instruksi revisi';
                setTimeout(() => this.revisionError = null, 3000);
                return;
            }

            if (!this.originalFileContent) {
                this.revisionError = '❌ Konten asli tidak ditemukan. Silakan upload ulang file.';
                setTimeout(() => this.revisionError = null, 5000);
                return;
            }

            this.revising = true;
            this.revisionError = null;
            this.revisionSuccess = false;

            // Collect all instructions (previous + new revision)
            const allInstructions = this.chatMessages
                .filter(msg => msg.type === 'user')
                .map(msg => msg.text);
            
            // Add new revision instruction
            allInstructions.push(this.revisionInstruction);

            try {
                const response = await fetch('{{ route("summarize.revise") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        content: this.originalFileContent,
                        instructions: allInstructions
                    })
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.error || 'Terjadi kesalahan pada server');
                }

                const data = await response.json();

                if (data.success) {
                    // Update summary with revised version
                    this.summary = data.summary;
                    this.summaryMetadata = data.metadata || this.summaryMetadata;
                    
                    // Add to chat history
                    this.chatMessages.push({
                        type: 'user',
                        text: this.revisionInstruction
                    });
                    
                    this.revisionSuccess = true;
                    this.revisionInstruction = '';
                    
                    // Auto-hide success message after 3 seconds
                    setTimeout(() => {
                        this.revisionSuccess = false;
                    }, 3000);
                    
                    // Scroll to top to see new result
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } else {
                    throw new Error(data.error || 'Gagal merevisi summary');
                }
            } catch (error) {
                console.error('Revision error:', error);
                this.revisionError = error.message || 'Terjadi kesalahan. Silakan coba lagi.';
            } finally {
                this.revising = false;
            }
        }
    }
}
</script>
@endpush

<style>
    [x-cloak] { display: none !important; }
    
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
    }
    
    .prose h2 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
    }
    
    .prose h3 {
        font-size: 1.25rem;
        font-weight: 600;
        margin-top: 1.25rem;
        margin-bottom: 0.5rem;
    }
    
    .prose p {
        margin-top: 0.75rem;
        margin-bottom: 0.75rem;
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
    }
    
    .prose strong {
        font-weight: 700;
        color: #1E293B;
    }
    
    .prose em {
        font-style: italic;
    }
    
    .prose code {
        background-color: #F1F5F9;
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
</style>
@endsection
