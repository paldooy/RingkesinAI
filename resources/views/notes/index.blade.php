@extends('layouts.app')

@section('title', 'Catatan - Ringkesin')

@section('content')
<div class="flex-1 bg-[#F9FAFB] overflow-auto" x-data="{ viewMode: 'grid' }">
    <div class="max-w-7xl mx-auto p-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-8">
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
                   class="px-4 py-2 {{ !request('category_id') ? 'bg-[#2C74B3] text-white' : 'bg-blue-50 text-blue-700 hover:bg-blue-100' }} rounded-full text-sm font-medium transition-colors">
                    Semua
                </a>
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
                    class="p-6 rounded-2xl border-2 cursor-pointer group relative overflow-hidden transition-all hover:shadow-xl"
                    style="background-color: {{ $bgColor ?: 'rgba(148, 163, 184, 0.1)' }}; border-color: {{ $borderColor ?: 'rgba(148, 163, 184, 0.3)' }};"
                    onclick="window.location='{{ route('notes.show', $note) }}'"
                >
                    <!-- Gradient overlay on hover -->
                    <div class="absolute inset-0 bg-gradient-to-br from-white/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>

                    <div class="relative z-10">
                        <div class="flex items-start gap-3 mb-4">
                            <div class="text-4xl group-hover:scale-110 transition-transform">
                                {{ $note->category ? $note->category->icon : '📝' }}
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg text-[#1E293B] mb-2 line-clamp-2 group-hover:text-[#2C74B3] transition-colors">
                                    {{ $note->title }}
                                </h3>
                                @if($note->category)
                                    @php
                                        $color = $note->category->color;
                                        // Handle both hex colors and Tailwind classes
                                        $style = str_starts_with($color, '#') ? "background-color: {$color};" : '';
                                        $class = str_starts_with($color, '#') ? '' : $color;
                                    @endphp
                                    <span class="inline-block px-3 py-1 text-white text-xs rounded-lg {{ $class }}" @if($style) style="{{ $style }}" @endif>
                                        {{ $note->category->icon }} {{ $note->category->name }}
                                    </span>
                                @else
                                    <span class="inline-block px-3 py-1 bg-gray-200 text-gray-600 text-xs rounded-lg">
                                        📝 Tanpa Kategori
                                    </span>
                                @endif
                            </div>
                        </div>

                        <p class="text-sm text-[#1E293B]/60 mb-4 line-clamp-3">
                            {{ $note->excerpt }}
                        </p>

                        @if($note->tags->count() > 0)
                            <div class="flex flex-wrap gap-2 mb-4">
                                @foreach($note->tags as $tag)
                                    <span class="text-xs px-2 py-1 bg-white/60 text-[#1E293B]/70 rounded-md border border-[#E5E7EB]">
                                        {{ $tag->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        <div class="flex items-center justify-between pt-4 border-t border-[#E5E7EB]/50">
                            <span class="text-xs text-[#1E293B]/50">{{ $note->created_at->format('d M Y') }}</span>
                            <div class="flex gap-1">
                                <a href="{{ route('notes.show', $note) }}" 
                                   onclick="event.stopPropagation()"
                                   class="p-2 hover:bg-white/80 rounded-lg transition-colors text-[#1E293B]/60 hover:text-[#2C74B3]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('notes.edit', $note) }}" 
                                   onclick="event.stopPropagation()"
                                   class="p-2 hover:bg-white/80 rounded-lg transition-colors text-[#1E293B]/60 hover:text-[#2C74B3]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('notes.destroy', $note) }}" method="POST" onclick="event.stopPropagation()" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            onclick="return confirm('Yakin ingin menghapus catatan ini?')"
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
        <div x-show="viewMode === 'list'" x-cloak class="space-y-4">
            @forelse($notes as $note)
                @php
                    // Get category color and convert to light background for list view
                    $color = $note->category ? $note->category->color : '#94A3B8';
                    
                    if (str_starts_with($color, '#')) {
                        $hex = ltrim($color, '#');
                        $r = hexdec(substr($hex, 0, 2));
                        $g = hexdec(substr($hex, 2, 2));
                        $b = hexdec(substr($hex, 4, 2));
                        $bgColor = "rgba($r, $g, $b, 0.05)";
                        $borderColor = "rgba($r, $g, $b, 0.2)";
                    } else {
                        $bgColor = '';
                        $borderColor = '';
                    }
                @endphp
                
                <div class="rounded-2xl p-6 hover:shadow-lg transition-shadow border-2 cursor-pointer"
                     style="background-color: {{ $bgColor ?: 'rgba(148, 163, 184, 0.05)' }}; border-color: {{ $borderColor ?: 'rgba(148, 163, 184, 0.2)' }};"
                     onclick="window.location='{{ route('notes.show', $note) }}'">
                    <div class="flex items-start gap-4">
                        <div class="text-3xl">{{ $note->category ? $note->category->icon : '📝' }}</div>
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-2 h-2 rounded-full bg-[#2C74B3]"></div>
                                <h3 class="text-lg font-bold text-[#1E293B]">{{ $note->title }}</h3>
                            </div>
                            <p class="text-sm text-[#1E293B]/60 mb-3 line-clamp-2">
                                {{ $note->excerpt }}
                            </p>
                            <div class="flex items-center gap-4 text-xs text-[#1E293B]/50">
                                @if($note->category)
                                    @php
                                        $color = $note->category->color;
                                        // Handle both hex colors and Tailwind classes
                                        $style = str_starts_with($color, '#') ? "background-color: {$color};" : '';
                                        $class = str_starts_with($color, '#') ? '' : $color;
                                    @endphp
                                    <span class="px-3 py-1 rounded-lg text-white {{ $class }}" @if($style) style="{{ $style }}" @endif>
                                        {{ $note->category->icon }} {{ $note->category->name }}
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-lg bg-gray-200 text-gray-600">
                                        📝 Tanpa Kategori
                                    </span>
                                @endif
                                <span>{{ $note->created_at->format('d M Y • H:i') }}</span>
                            </div>
                        </div>
                        <a href="{{ route('notes.show', $note) }}" 
                           onclick="event.stopPropagation()"
                           class="bg-white hover:bg-[#2C74B3] hover:text-white border border-[#E5E7EB] hover:border-[#2C74B3] text-[#1E293B] font-medium px-4 py-2 rounded-xl transition-colors">
                            Lihat
                        </a>
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
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
