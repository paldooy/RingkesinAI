@extends('layouts.app')

@section('title', 'Test Auto-Highlighting')

@section('content')
<div class="flex-1 bg-[#F9FAFB] overflow-auto">
    <div class="max-w-5xl mx-auto p-8">
        <!-- Header -->
        <div class="bg-white rounded-2xl p-6 mb-6 border-2 border-[#A7C7E7] shadow-lg">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-16 h-16 bg-gradient-to-br from-[#2C74B3] to-purple-600 rounded-2xl flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-[#1E293B]">✨ Test Auto-Highlighting</h1>
                    <p class="text-[#1E293B]/60">Demo fitur auto-highlight dengan 4 warna berbeda</p>
                </div>
            </div>
        </div>

        <!-- Legend -->
        <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl p-6 mb-6 border-2 border-blue-100">
            <h3 class="text-lg font-bold text-[#1E293B] mb-4">🎨 Legend Warna Highlight:</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-8 rounded bg-yellow-200 border-l-4 border-yellow-400 flex-shrink-0"></div>
                    <div>
                        <strong class="text-[#1E293B]">Kuning</strong>
                        <p class="text-xs text-[#1E293B]/60">penting, utama, kunci, fundamental, esensial, vital, kritikal, signifikan, prioritas, fokus</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-8 rounded bg-green-200 border-l-4 border-green-400 flex-shrink-0"></div>
                    <div>
                        <strong class="text-[#1E293B]">Hijau</strong>
                        <p class="text-xs text-[#1E293B]/60">definisi, konsep, pengertian, adalah, merupakan, yaitu, artinya, maksudnya, didefinisikan</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-8 rounded bg-pink-200 border-l-4 border-pink-400 flex-shrink-0"></div>
                    <div>
                        <strong class="text-[#1E293B]">Pink</strong>
                        <p class="text-xs text-[#1E293B]/60">perhatian, catatan, penting diingat, note, warning, perlu diperhatikan, hati-hati, awas, bahaya</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-8 rounded bg-blue-200 border-l-4 border-blue-400 flex-shrink-0"></div>
                    <div>
                        <strong class="text-[#1E293B]">Biru</strong>
                        <p class="text-xs text-[#1E293B]/60">contoh, misalnya, misal, seperti, instance, kasus, ilustrasi, example</p>
                    </div>
                </div>
            </div>
            <p class="text-xs text-[#1E293B]/50 mt-4">
                💡 Hover pada kata yang di-highlight untuk melihat efek zoom
            </p>
        </div>

        <!-- Result -->
        <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-200">
            <div class="prose prose-slate max-w-none prose-headings:text-[#1E293B] prose-p:text-[#1E293B] prose-a:text-[#2C74B3] prose-strong:text-[#1E293B] prose-strong:font-bold prose-code:text-[#2C74B3] prose-pre:bg-[#F1F5F9] prose-li:text-[#1E293B] prose-ul:list-disc prose-ol:list-decimal">
                {!! $html !!}
            </div>
        </div>

        <!-- Raw Markdown (Hidden by default) -->
        <div class="mt-6">
            <details class="bg-gray-100 rounded-xl p-4">
                <summary class="cursor-pointer font-bold text-[#1E293B] hover:text-[#2C74B3]">📄 View Raw Markdown</summary>
                <pre class="mt-4 bg-gray-800 text-green-400 p-4 rounded-lg overflow-x-auto text-sm font-mono">{{ $markdown }}</pre>
            </details>
        </div>

        <!-- Back Button -->
        <div class="mt-6 text-center">
            <a href="{{ route('summarize.index') }}" class="inline-flex items-center gap-2 bg-[#2C74B3] hover:bg-[#205295] text-white rounded-xl px-6 py-3 font-medium transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke AI Summarize
            </a>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Highlight styles for test page */
    .prose mark {
        padding: 2px 4px;
        border-radius: 3px;
        font-weight: 500;
        transition: all 0.2s;
    }
    
    .prose mark:hover {
        transform: scale(1.02);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .prose mark[style*="yellow"] {
        background-color: #fef08a !important;
        color: #854d0e;
        border-left: 3px solid #facc15;
    }
    
    .prose mark[style*="#90EE90"] {
        background-color: #bbf7d0 !important;
        color: #14532d;
        border-left: 3px solid #4ade80;
    }
    
    .prose mark[style*="#FFB6C1"] {
        background-color: #fbcfe8 !important;
        color: #831843;
        border-left: 3px solid #f472b6;
    }
    
    .prose mark[style*="#87CEEB"] {
        background-color: #bfdbfe !important;
        color: #1e3a8a;
        border-left: 3px solid #60a5fa;
    }
</style>
@endpush
@endsection
