@extends('layouts.app')

@section('title', 'Catatan - Ringkesin')

@section('content')
<div class="flex-1 bg-[#F9FAFB] overflow-auto" x-data="{ 
    viewMode: 'grid',
    selectedNotes: [],
    selectAll: false,
    showDeleteModal: false,
    showSingleDeleteModal: false,
    deleteNoteId: null,
    showShareModal: false,
    shareData: null,
    shareNoteId: null,
    isGenerating: false,
    expiresIn: '1day',
    favoritesCount: {{ $favoritesCount }},
    
    toggleSelectAll() {
        if (this.selectAll) {
            this.selectedNotes = Array.from(document.querySelectorAll('.note-checkbox')).map(cb => parseInt(cb.value));
        } else {
            this.selectedNotes = [];
        }
    },
    
    toggleNote(noteId) {
        const index = this.selectedNotes.indexOf(noteId);
        if (index > -1) {
            this.selectedNotes.splice(index, 1);
        } else {
            this.selectedNotes.push(noteId);
        }
        const totalCheckboxes = document.querySelectorAll('.note-checkbox').length;
        this.selectAll = totalCheckboxes > 0 && this.selectedNotes.length === totalCheckboxes;
    },
    
    async bulkDelete() {
        if (this.selectedNotes.length === 0) return;
        
        try {
            const response = await fetch('{{ route('notes.bulk-delete') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({ note_ids: this.selectedNotes })
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Remove deleted notes from DOM
                this.selectedNotes.forEach(id => {
                    document.querySelector(`[data-note-id='${id}']`)?.remove();
                });
                
                // Reset selection
                this.selectedNotes = [];
                this.selectAll = false;
                this.showDeleteModal = false;
                
                // Show success message
                alert(data.message);
                
                // Reload if no notes left
                if (document.querySelectorAll('[data-note-id]').length === 0) {
                    window.location.reload();
                }
            }
        } catch (error) {
            alert('Terjadi kesalahan saat menghapus catatan');
            console.error(error);
        }
    },
    
    async toggleFavorite(noteId, event) {
        event.stopPropagation();
        
        try {
            const response = await fetch(`/notes/${noteId}/toggle-favorite`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Update icon
                const icon = event.target.closest('button').querySelector('svg');
                if (data.is_favorite) {
                    icon.classList.remove('text-gray-400');
                    icon.classList.add('text-yellow-500', 'fill-yellow-500');
                    icon.setAttribute('fill', 'currentColor');
                    this.favoritesCount++;
                } else {
                    icon.classList.add('text-gray-400');
                    icon.classList.remove('text-yellow-500', 'fill-yellow-500');
                    icon.setAttribute('fill', 'none');
                    this.favoritesCount--;
                    
                    // If on favorite filter, remove the note from view
                    if (new URLSearchParams(window.location.search).get('favorite') === '1') {
                        document.querySelector(`[data-note-id='${noteId}']`)?.remove();
                        
                        // Check if this was the last favorite note
                        const remainingNotes = document.querySelectorAll('[data-note-id]').length;
                        
                        if (remainingNotes === 0 || this.favoritesCount === 0) {
                            // Redirect to All category
                            window.location.href = '{{ route('notes.index') }}';
                            return;
                        }
                    }
                }
                
                // Show toast
                showToast(data.message);
            }
        } catch (error) {
            console.error(error);
            showToast('Terjadi kesalahan', 'error');
        }
    },

    async generateShareCode() {
        this.isGenerating = true;

        try {
            const response = await fetch(`/notes/${this.shareNoteId}/generate-share`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({
                    expires_in: this.expiresIn
                })
            });

            const data = await response.json();

            if (data.success) {
                this.shareData = {
                    share_code: data.share_code,
                    share_url: data.share_url,
                    expires_at: data.expires_at
                };
                showToast('Kode share berhasil dibuat!');
            } else {
                showToast(data.message || 'Gagal membuat kode share', 'error');
            }
        } catch (error) {
            console.error(error);
            showToast('Terjadi kesalahan saat membuat kode share', 'error');
        } finally {
            this.isGenerating = false;
        }
    },

    async deleteSingleNote() {
        if (!this.deleteNoteId) return;
        
        try {
            const form = document.querySelector(`form[data-note-id='${this.deleteNoteId}']`);
            if (form) {
                form.submit();
            }
        } catch (error) {
            console.error(error);
            showToast('Terjadi kesalahan saat menghapus catatan', 'error');
        }
    }
}">
    <div class="max-w-7xl mx-auto p-4 md:p-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 my-6">
            <div>
                <h1 class="text-3xl font-bold text-[#1E293B] mb-2">Catatan Saya</h1>
                <p class="text-sm text-[#1E293B]/60">
                    Kelola dan cari catatanmu dengan mudah
                </p>
            </div>

            <a href="{{ route('notes.create') }}" class="bg-[#2C74B3] hover:bg-[#205295] text-white font-medium px-6 py-3 rounded-xl transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Buat Catatan
            </a>
        </div>

        <!-- Bulk Actions Bar -->
        <div x-show="selectedNotes.length > 0" 
             x-transition
             class="bg-purple-50 border border-purple-200 rounded-xl p-4 mb-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="font-medium text-purple-900">
                    <span x-text="selectedNotes.length"></span> catatan dipilih
                </span>
            </div>
            <div class="flex gap-2">
                <button @click="selectedNotes = []; selectAll = false" 
                        class="px-4 py-2 text-purple-700 hover:bg-purple-100 rounded-lg transition-colors">
                    Batal
                </button>
                <button @click="showDeleteModal = true" 
                        class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus Terpilih
                </button>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="bg-white rounded-2xl shadow-sm border border-[#E5E7EB] p-6 mb-6">
            <form method="GET" action="{{ route('notes.index') }}" class="flex flex-col md:flex-row gap-4">
                <div class="flex-1 relative">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-[#1E293B]/40 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input 
                        type="text" 
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari catatan..."
                        class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-300 focus:border-[#2C74B3] focus:ring-2 focus:ring-[#2C74B3]/20 outline-none transition"
                    />
                </div>
                
                <div class="flex gap-2">
                    <button type="submit" class="bg-[#2C74B3] hover:bg-[#205295] text-white px-6 py-3 rounded-xl transition-colors">
                        Cari
                    </button>
                    
                    <div class="flex border border-[#E5E7EB] rounded-xl overflow-hidden">
                        <button
                            type="button"
                            @click="viewMode = 'grid'"
                            :class="viewMode === 'grid' ? 'bg-[#2C74B3] text-white' : 'bg-white text-[#1E293B] hover:bg-[#F9FAFB]'"
                            class="px-4 py-2 transition-colors"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                        </button>
                        <button
                            type="button"
                            @click="viewMode = 'list'"
                            :class="viewMode === 'list' ? 'bg-[#2C74B3] text-white' : 'bg-white text-[#1E293B] hover:bg-[#F9FAFB]'"
                            class="px-4 py-2 border-l border-[#E5E7EB] transition-colors"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </form>

            <!-- Category Filter Pills -->
            <div class="flex flex-wrap gap-2 mt-4">
                <a href="{{ route('notes.index') }}" 
                   class="px-4 py-2 {{ !request('category_id') && !request('favorite') ? 'bg-[#2C74B3] text-white' : 'bg-blue-50 text-blue-700 hover:bg-blue-100' }} rounded-full text-sm font-medium transition-colors">
                    Semua
                </a>
                
                @if($favoritesCount > 0)
                    <a href="{{ route('notes.index', ['favorite' => '1']) }}" 
                       x-show="favoritesCount > 0"
                       class="px-4 py-2 {{ request('favorite') == '1' ? 'bg-yellow-500 text-white' : 'bg-yellow-50 text-yellow-700 hover:bg-yellow-100' }} rounded-full text-sm font-medium transition-colors flex items-center gap-1">
                        <svg class="w-4 h-4 {{ request('favorite') == '1' ? 'fill-white' : 'fill-yellow-500' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        Favorit (<span x-text="favoritesCount"></span>)
                    </a>
                @else
                    <a href="{{ route('notes.index', ['favorite' => '1']) }}" 
                       x-show="favoritesCount > 0"
                       x-cloak
                       class="px-4 py-2 {{ request('favorite') == '1' ? 'bg-yellow-500 text-white' : 'bg-yellow-50 text-yellow-700 hover:bg-yellow-100' }} rounded-full text-sm font-medium transition-colors flex items-center gap-1">
                        <svg class="w-4 h-4 {{ request('favorite') == '1' ? 'fill-white' : 'fill-yellow-500' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        Favorit (<span x-text="favoritesCount"></span>)
                    </a>
                @endif
                @foreach($categories as $cat)
                    @php
                        $color = $cat->color;
                        // Convert hex to RGB and create light version
                        if (str_starts_with($color, '#')) {
                            $hex = ltrim($color, '#');
                            $r = hexdec(substr($hex, 0, 2));
                            $g = hexdec(substr($hex, 2, 2));
                            $b = hexdec(substr($hex, 4, 2));
                            $bgColor = "rgba($r, $g, $b, 0.15)";
                            $textColor = "rgba(0, 0, 0, 0.6)";
                        } else {
                            $bgColor = '';
                            $textColor = '';
                        }
                        $isActive = request('category_id') == $cat->id;
                    @endphp
                    
                    <a href="{{ route('notes.index', ['category_id' => $cat->id]) }}" 
                       class="px-4 py-2 rounded-full text-sm font-medium transition-all hover:shadow-md {{ $isActive ? 'ring-2 ring-offset-2' : '' }}"
                       style="{{ $isActive ? 'background-color: ' . $color . '; color: "rgba(0, 0, 0, 0.6)"; ring-color: ' . $color : 'background-color: ' . ($bgColor ?: 'rgb(243 244 246)') . '; color: ' . ($textColor ?: 'rgb(55 65 81)') }}">
                        {{ $cat->icon }} {{ $cat->name }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Grid View -->
        <div x-show="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Select All Checkbox -->
            <div class="col-span-full mb-2">
                <label class="flex items-center gap-2 cursor-pointer w-fit">
                    <input 
                        type="checkbox" 
                        x-model="selectAll"
                        @change="toggleSelectAll()"
                        class="w-4 h-4 text-purple-600 rounded border-gray-300 focus:ring-purple-500"
                    />
                    <span class="text-sm text-gray-600 font-medium">Pilih Semua</span>
                </label>
            </div>

            @forelse($notes as $note)
                @php
                    // Get category color and convert to light background
                    $color = $note->category ? $note->category->color : '#94A3B8';
                    
                    // Convert hex to RGB and create light version
                    if (str_starts_with($color, '#')) {
                        $hex = ltrim($color, '#');
                        $r = hexdec(substr($hex, 0, 2));
                        $g = hexdec(substr($hex, 2, 2));
                        $b = hexdec(substr($hex, 4, 2));
                        // Create very light version (90% white mixed)
                        $bgColor = "rgba($r, $g, $b, 0.1)";
                        $borderColor = "rgba($r, $g, $b, 0.3)";
                    } else {
                        // Fallback for Tailwind classes
                        $bgColor = '';
                        $borderColor = '';
                    }
                @endphp
                
                <div 
                    data-note-id="{{ $note->id }}"
                    class="rounded-xl p-4 border border-gray-200 group relative hover:shadow-lg transition-all"
                    :class="selectedNotes.includes({{ $note->id }}) ? 'ring-2 ring-blue-500 border-blue-500' : ''"
                    :style="selectedNotes.includes({{ $note->id }}) ? '' : 'background-color: {{ $bgColor ?: 'rgba(148, 163, 184, 0.08)' }};'"
                >
                    <!-- Checkbox & Star -->
                    <div class="absolute top-3 right-3 z-20 flex items-center gap-2">
                        <button 
                            @click="toggleFavorite({{ $note->id }}, $event)"
                            class="p-1 hover:scale-110 transition-transform"
                            title="{{ $note->is_favorite ? 'Hapus dari favorit' : 'Tambah ke favorit' }}"
                        >
                            <svg class="w-5 h-5 {{ $note->is_favorite ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300' }}" 
                                 fill="{{ $note->is_favorite ? 'currentColor' : 'none' }}" 
                                 stroke="currentColor" 
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                        </button>
                        
                        <input 
                            type="checkbox" 
                            class="note-checkbox w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 cursor-pointer"
                            :checked="selectedNotes.includes({{ $note->id }})"
                            @click.stop="toggleNote({{ $note->id }})"
                            value="{{ $note->id }}"
                        />
                    </div>

                    <!-- Clickable card -->
                    <div class="cursor-pointer pr-16" @click="if (!$event.target.classList.contains('note-checkbox')) { window.location='{{ route('notes.show', $note) }}' }">
                        
                        <!-- Title -->
                        <h3 class="text-sm sm:text-base font-bold text-[#1E293B] mb-2 line-clamp-2 group-hover:text-[#2C74B3] transition-colors">
                            {{ $note->title }}
                        </h3>

                        <!-- Preview -->
                        <p class="text-xs sm:text-sm text-gray-600 mb-3 line-clamp-2">
                            {{ $note->excerpt }}
                        </p>

                        <!-- Tags -->
                        @if($note->tags->count() > 0)
                            <div class="flex flex-wrap gap-1.5 mb-3">
                                @foreach($note->tags as $tag)
                                    <span class="text-xs px-2 py-0.5 bg-white/60 text-gray-700 rounded-md">
                                        {{ $tag->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        <!-- Footer: Category & Date & Actions -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pt-3 border-t border-gray-200">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                                @if($note->category)
                                    @php
                                        $catColor = $note->category->color;
                                        $style = str_starts_with($catColor, '#') ? "background-color: {$catColor};" : '';
                                        $class = str_starts_with($catColor, '#') ? '' : $catColor;
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 text-white text-xs font-medium rounded-md {{ $class }}" @if($style) style="{{ $style }}" @endif>
                                        {{ $note->category->name }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-md">
                                        Tanpa Kategori
                                    </span>
                                @endif
                                
                                <span class="text-xs text-gray-500">
                                    {{ $note->created_at->format('d M Y') }}
                                </span>
                            </div>
                            
                            <div class="flex gap-1">
                                <button 
                                   @click.stop="shareNoteId = {{ $note->id }}; showShareModal = true; shareData = null"
                                   class="p-2 hover:bg-white/80 rounded-lg transition-colors text-[#1E293B]/60 hover:text-green-600"
                                   title="Share">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                                    </svg>
                                </button>
                                <a href="{{ route('notes.edit', $note) }}" 
                                   @click.stop
                                   class="p-2 hover:bg-white/80 rounded-lg transition-colors text-[#1E293B]/60 hover:text-[#2C74B3]"
                                   title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('notes.destroy', $note) }}" method="POST" @click.stop class="inline" data-note-id="{{ $note->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" 
                                            @click.stop="deleteNoteId = {{ $note->id }}; showSingleDeleteModal = true"
                                            class="p-2 hover:bg-red-50 rounded-lg transition-colors text-[#1E293B]/60 hover:text-red-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-20">
                    <div class="w-24 h-24 bg-[#F9FAFB] rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-[#1E293B]/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl text-[#1E293B] mb-2">Belum ada catatan</h3>
                    <p class="text-[#1E293B]/60 mb-6">
                        Buat catatan pertamamu sekarang!
                    </p>
                    <a href="{{ route('notes.create') }}" class="inline-block bg-[#2C74B3] hover:bg-[#205295] text-white px-6 py-3 rounded-xl transition-colors">
                        Buat Catatan Baru
                    </a>
                </div>
            @endforelse
        </div>

        <!-- List View -->
        <div x-show="viewMode === 'list'" x-cloak class="space-y-3">
            <!-- Select All Checkbox -->
            <div class="mb-3">
                <label class="flex items-center gap-2 cursor-pointer w-fit">
                    <input 
                        type="checkbox" 
                        x-model="selectAll"
                        @change="toggleSelectAll()"
                        class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500"
                    />
                    <span class="text-sm text-gray-600 font-medium">Pilih Semua</span>
                </label>
            </div>

            @forelse($notes as $note)
                @php
                    $color = $note->category ? $note->category->color : '#94A3B8';
                    
                    if (str_starts_with($color, '#')) {
                        $hex = ltrim($color, '#');
                        $r = hexdec(substr($hex, 0, 2));
                        $g = hexdec(substr($hex, 2, 2));
                        $b = hexdec(substr($hex, 4, 2));
                        $bgColor = "rgba($r, $g, $b, 0.08)";
                        $borderColor = "rgba($r, $g, $b, 0.25)";
                    } else {
                        $bgColor = '';
                        $borderColor = '';
                    }
                @endphp
                
                <div 
                    data-note-id="{{ $note->id }}"
                    class="rounded-xl p-4 border border-gray-200 group relative hover:shadow-lg transition-all"
                    :class="selectedNotes.includes({{ $note->id }}) ? 'ring-2 ring-blue-500 border-blue-500' : ''"
                    :style="selectedNotes.includes({{ $note->id }}) ? '' : 'background-color: {{ $bgColor ?: 'rgba(148, 163, 184, 0.08)' }};'"
                >
                    <!-- Checkbox & Favorite (Left) -->
                    <div class="absolute top-3 left-3 z-20 flex items-center gap-1.5">
                        <input 
                            type="checkbox" 
                            class="note-checkbox w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 cursor-pointer"
                            :checked="selectedNotes.includes({{ $note->id }})"
                            @click.stop="toggleNote({{ $note->id }})"
                            value="{{ $note->id }}"
                        />
                        
                        <button 
                            @click="toggleFavorite({{ $note->id }}, $event)"
                            class="p-0.5 hover:scale-110 transition-transform"
                            title="{{ $note->is_favorite ? 'Hapus dari favorit' : 'Tambah ke favorit' }}"
                        >
                            <svg class="w-4 h-4 {{ $note->is_favorite ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300' }}" 
                                 fill="{{ $note->is_favorite ? 'currentColor' : 'none' }}" 
                                 stroke="currentColor" 
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Content (Clickable) -->
                    <div class="cursor-pointer pl-12 pr-6 md:pr-12" @click="if (!$event.target.classList.contains('note-checkbox')) { window.location='{{ route('notes.show', $note) }}' }">
                        <!-- Title -->
                        <h3 class="text-sm sm:text-base font-bold text-[#1E293B] mb-1 line-clamp-2 group-hover:text-[#2C74B3] transition-colors">
                            {{ $note->title }}
                        </h3>
                        
                        <!-- Preview -->
                        <p class="text-xs sm:text-sm text-gray-600 mb-2 line-clamp-1">
                            {{ $note->excerpt }}
                        </p>

                        <!-- Tags (if any) -->
                        @if($note->tags->count() > 0)
                            <div class="flex flex-wrap gap-1 mb-2">
                                @foreach($note->tags as $tag)
                                    <span class="text-xs px-1.5 py-0.5 bg-white/60 text-gray-700 rounded">
                                        {{ $tag->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        <!-- Footer: Category (left) and Actions + Date (right) -->
                        <div class="flex items-center justify-between gap-2 text-xs">
                            <div class="flex items-center gap-2">
                                @if($note->category)
                                    @php
                                        $catColor = $note->category->color;
                                        $style = str_starts_with($catColor, '#') ? "background-color: {$catColor};" : '';
                                        $class = str_starts_with($catColor, '#') ? '' : $catColor;
                                    @endphp
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-white font-medium {{ $class }}" @if($style) style="{{ $style }}" @endif>
                                        {{ $note->category->name }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-gray-200 text-gray-600 font-medium">
                                        Tanpa Kategori
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-center gap-2">
                                <button @click.stop="shareNoteId = {{ $note->id }}; showShareModal = true; shareData = null" title="Share" class="p-2 rounded-lg hover:bg-white/80 transition-colors text-[#1E293B]/60">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                                    </svg>
                                </button>

                                <a href="{{ route('notes.edit', $note) }}" @click.stop title="Edit" class="p-2 rounded-lg hover:bg-white/80 transition-colors text-[#1E293B]/60">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>

                                <form action="{{ route('notes.destroy', $note) }}" method="POST" @click.stop class="inline" data-note-id="{{ $note->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" @click.stop="deleteNoteId = {{ $note->id }}; showSingleDeleteModal = true" title="Hapus" class="p-2 rounded-lg hover:bg-red-50 transition-colors text-[#1E293B]/60">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>

                                <span class="text-gray-500 text-xs ml-2">{{ $note->created_at->format('d M Y • H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-20">
                    <div class="w-24 h-24 bg-[#F9FAFB] rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-[#1E293B]/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl text-[#1E293B] mb-2">Belum ada catatan</h3>
                    <p class="text-[#1E293B]/60 mb-6">
                        Buat catatan pertamamu sekarang!
                    </p>
                    <a href="{{ route('notes.create') }}" class="inline-block bg-[#2C74B3] hover:bg-[#205295] text-white px-6 py-3 rounded-xl transition-colors">
                        Buat Catatan Baru
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $notes->links() }}
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
                    class="w-full bg-[#2C74B3] hover:bg-[#205295] text-white font-medium px-6 py-3 rounded-xl transition-colors disabled:opacity-50">
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
                            <button @click.stop="navigator.clipboard.writeText(shareData.share_code).then(() => showToast('Kode berhasil disalin!')).catch(() => showToast('Gagal menyalin kode', 'error'))" class="p-2 hover:bg-gray-100 rounded-lg" title="Salin kode">
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
                            <button @click.stop="navigator.clipboard.writeText(shareData.share_url).then(() => showToast('Link berhasil disalin!')).catch(() => showToast('Gagal menyalin link', 'error'))" class="p-2 hover:bg-gray-100 rounded-lg flex-shrink-0" title="Salin link">
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

                <button @click="shareData = null" class="w-full px-4 py-3 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-xl transition-colors">
                    Generate Kode Baru
                </button>
            </div>
        </div>
    </div>

    <!-- Bulk Delete Confirmation Modal -->
    <div x-show="showDeleteModal" 
         x-cloak
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
         @click.self="showDeleteModal = false">
        <div class="bg-white rounded-2xl p-6 max-w-md w-full" @click.stop>
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Konfirmasi Hapus</h3>
                    <p class="text-sm text-gray-600">Tindakan ini tidak dapat dibatalkan</p>
                </div>
            </div>
            
            <p class="text-gray-700 mb-6">
                Anda akan menghapus <span class="font-bold text-red-600" x-text="selectedNotes.length"></span> catatan. 
                Apakah Anda yakin?
            </p>
            
            <div class="flex gap-3 justify-end">
                <button @click="showDeleteModal = false" 
                        class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                    Batal
                </button>
                <button @click="bulkDelete()" 
                        class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors">
                    Ya, Hapus Semua
                </button>
            </div>
        </div>
    </div>

    <!-- Single Delete Confirmation Modal -->
    <div x-show="showSingleDeleteModal" 
         x-cloak
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
         @click.self="showSingleDeleteModal = false">
        <div class="bg-white rounded-2xl p-6 max-w-md w-full" @click.stop>
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Konfirmasi Hapus</h3>
                    <p class="text-sm text-gray-600">Tindakan ini tidak dapat dibatalkan</p>
                </div>
            </div>
            
            <p class="text-gray-700 mb-6">
                Apakah Anda yakin ingin menghapus catatan ini? Data yang sudah dihapus tidak dapat dikembalikan.
            </p>
            
            <div class="flex gap-3 justify-end">
                <button @click="showSingleDeleteModal = false; deleteNoteId = null" 
                        class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                    Batal
                </button>
                <button @click="deleteSingleNote()" 
                        class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function showToast(message, type = 'success') {
        const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
        const toast = document.createElement('div');
        toast.className = `fixed bottom-4 right-4 ${bgColor} text-white px-6 py-3 rounded-xl shadow-lg flex items-center gap-3 z-50 animate-slide-up`;
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
@endsection
