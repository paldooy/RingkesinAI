@extends('layouts.app')

@section('title', 'Dashboard - Ringkesin')

@section('content')
<div class="flex-1 bg-[#F9FAFB] overflow-auto">
    <!-- Header with gradient background -->
    <div class="bg-gradient-to-r from-[#2C74B3] to-[#5B8EC9] text-white p-8">
        <div class="max-w-7xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Selamat datang kembali,</h1>
                    <h2 class="text-4xl font-bold">{{ Auth::user()->name }} 👋</h2>
                    <p class="text-white/80 mt-2">
                        Lanjutkan perjalanan belajarmu hari ini. Kamu sudah membuat {{ $totalNotes }} catatan!
                    </p>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                    <div class="flex items-start justify-between mb-4">
                        <div class="p-3 rounded-xl bg-white">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-white/80 text-sm mb-1">Total Catatan</p>
                    <h3 class="text-3xl font-bold mb-2">{{ $totalNotes }}</h3>
                </div>

                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                    <div class="flex items-start justify-between mb-4">
                        <div class="p-3 rounded-xl bg-white">
                            <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-white/80 text-sm mb-1">Streak Belajar</p>
                    <h3 class="text-3xl font-bold mb-2">7 Hari</h3>
                </div>

                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                    <div class="flex items-start justify-between mb-4">
                        <div class="p-3 rounded-xl bg-white">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-white/80 text-sm mb-1">Kategori</p>
                    <h3 class="text-3xl font-bold mb-2">{{ $totalCategories }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto p-8">
        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <a href="{{ route('notes.create') }}" class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all hover:scale-105">
                <svg class="w-8 h-8 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <h3 class="text-xl font-bold mb-1">Buat Catatan</h3>
                <p class="text-white/80 text-sm">Buat catatan baru</p>
                <div class="mt-4 flex items-center gap-2">
                    <span class="text-sm">Mulai</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
            </a>

            <a href="{{ route('summarize.index') }}" class="bg-gradient-to-br from-purple-500 to-pink-500 text-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all hover:scale-105">
                <svg class="w-8 h-8 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                </svg>
                <h3 class="text-xl font-bold mb-1">AI Summarize</h3>
                <p class="text-white/80 text-sm">Ringkas dengan AI</p>
                <div class="mt-4 flex items-center gap-2">
                    <span class="text-sm">Mulai</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
            </a>

            <a href="{{ route('notes.index') }}" class="bg-gradient-to-br from-green-500 to-emerald-600 text-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all hover:scale-105">
                <svg class="w-8 h-8 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="text-xl font-bold mb-1">Lihat Catatan</h3>
                <p class="text-white/80 text-sm">Explore semua</p>
                <div class="mt-4 flex items-center gap-2">
                    <span class="text-sm">Mulai</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
            </a>
        </div>

        <!-- Recent Notes -->
        <div>
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-[#1E293B]">Aktivitas Terbaru</h2>
                    <p class="text-sm text-[#1E293B]/60 mt-1">Catatan yang baru saja kamu buat</p>
                </div>
                <a href="{{ route('notes.index') }}" class="text-[#2C74B3] hover:underline text-sm font-medium">
                    Lihat Semua →
                </a>
            </div>

            <div class="space-y-4">
                @forelse($recentNotes as $note)
                    @php
                        // Get category color and convert to light background
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
                    
                    <div class="rounded-2xl p-6 hover:shadow-lg transition-all border-2 cursor-pointer group"
                         style="background-color: {{ $bgColor ?: 'rgba(148, 163, 184, 0.08)' }}; border-color: {{ $borderColor ?: 'rgba(148, 163, 184, 0.25)' }};"
                         onclick="window.location='{{ route('notes.show', $note) }}'">
                        <div class="flex items-start gap-4">
                            @if($note->category)
                                <div class="text-3xl">{{ $note->category->icon }}</div>
                            @else
                                <div class="text-3xl">📝</div>
                            @endif
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-lg font-bold text-[#1E293B] group-hover:text-[#2C74B3] transition-colors">{{ $note->title }}</h3>
                                </div>
                                <p class="text-sm text-[#1E293B]/60 mb-3 line-clamp-2">
                                    {{ $note->excerpt }}
                                </p>
                                <div class="flex items-center gap-4 text-xs text-[#1E293B]/50">
                                    @if($note->category)
                                        @php
                                            $catColor = $note->category->color;
                                            $style = str_starts_with($catColor, '#') ? "background-color: {$catColor};" : '';
                                            $class = str_starts_with($catColor, '#') ? '' : $catColor;
                                        @endphp
                                        <span class="px-3 py-1 rounded-lg text-white {{ $class }}" @if($style) style="{{ $style }}" @endif>
                                            {{ $note->category->icon }} {{ $note->category->name }}
                                        </span>
                                    @else
                                        <span class="px-3 py-1 rounded-lg bg-gray-200 text-gray-600">
                                            📝 Tanpa Kategori
                                        </span>
                                    @endif
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $note->created_at->format('d M Y') }} • {{ $note->created_at->format('H:i') }}
                                    </span>
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
                    <div class="text-center py-12 bg-white rounded-2xl border-2 border-dashed border-[#E5E7EB]">
                        <p class="text-[#1E293B]/60">Belum ada catatan. Yuk buat catatan pertamamu!</p>
                        <a href="{{ route('notes.create') }}" class="inline-block mt-4 text-[#2C74B3] hover:underline font-medium">
                            Buat Catatan →
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
