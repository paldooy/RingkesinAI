@extends('layouts.app')

@section('title', 'Buat Catatan - Ringkesin')

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
                <h1 class="text-3xl font-bold text-[#1E293B]">Buat Catatan Baru</h1>
            </div>
            <p class="text-sm text-[#1E293B]/60">
                Tulis dan organisir catatanmu dengan mudah
            </p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-600 rounded-xl p-4 mb-6">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('notes.store') }}" class="max-w-7xl mx-auto">
            @csrf

            <!-- Main Content Area -->
            <div class="space-y-6">
                <!-- Title -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB]">
                    <label for="title" class="block text-sm font-medium text-[#1E293B] mb-2">
                        Judul Catatan
                    </label>
                    <input 
                        type="text" 
                        id="title" 
                        name="title" 
                        value="{{ old('title') }}"
                        placeholder="Contoh: Persamaan Kuadrat - Rumus ABC"
                        required
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#2C74B3] focus:ring-2 focus:ring-[#2C74B3]/20 outline-none transition"
                    />
                </div>

                <!-- Content Editor -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB]">
                    <label for="content" class="block text-sm font-medium text-[#1E293B] mb-2">
                        Isi Catatan
                    </label>
                    <textarea 
                        id="content" 
                        name="content"
                        required
                        class="w-full"
                    >{{ old('content') }}</textarea>
                    
                    <div class="flex items-start gap-2 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-3 mt-3">
                        <span class="text-blue-600 text-lg">💡</span>
                        <div class="text-xs text-blue-800 space-y-1">
                            <p class="font-semibold">Fitur Editor:</p>
                            <ul class="list-disc list-inside text-[11px] space-y-0.5">
                                <li><strong>Table:</strong> Insert → Table (bisa resize & merge cells)</li>
                                <li><strong>Code Block:</strong> Format → Code atau Insert → Code Sample</li>
                                <li><strong>Highlight:</strong> Format → Background Color atau pilih teks → Background</li>
                                <li><strong>Bold/Italic:</strong> Toolbar atau Ctrl+B / Ctrl+I</li>
                                <li><strong>Lists:</strong> Bullet & Numbered list di toolbar</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Metadata & Actions Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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
                                Simpan Catatan
                            </button>
                            <a 
                                href="{{ route('notes.index') }}"
                                class="w-full border border-[#E5E7EB] hover:bg-[#F9FAFB] text-[#1E293B] font-medium py-3 rounded-xl transition-colors flex items-center justify-center gap-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Batal
                            </a>
                        </div>
                    </div>

                    <!-- Category Selection -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB]">
                        <h3 class="text-lg font-bold text-[#1E293B] mb-4">Kategori</h3>
                        <select 
                            name="category_id" 
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#2C74B3] focus:ring-2 focus:ring-[#2C74B3]/20 outline-none transition"
                        >
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->icon }} {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tags -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB]" 
                         x-data="{ tags: [], tagInput: '' }">
                        <h3 class="text-lg font-bold text-[#1E293B] mb-4">Tag</h3>
                        
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
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.tiny.cloud/1/kxr7knwfldryrpt79dmjl3iu43ggy14brhjff2t4hblvd6y1/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
    // Function to convert markdown tables to HTML
    function convertMarkdownTables(content) {
        // Detect markdown tables and convert to HTML
        const tableRegex = /(\|[^\n]+\|\r?\n)((?:\|[-:| ]+\|\r?\n))((?:\|[^\n]+\|\r?\n?)+)/gm;
        
        return content.replace(tableRegex, function(match, header, separator, rows) {
            // Parse header
            const headers = header.split('|').filter(h => h.trim()).map(h => h.trim());
            
            // Parse rows
            const rowLines = rows.trim().split('\n');
            const parsedRows = rowLines.map(row => 
                row.split('|').filter(cell => cell.trim()).map(cell => cell.trim())
            );
            
            // Build HTML table
            let html = '<table border="1" style="border-collapse: collapse; width: 100%;">\n';
            html += '  <thead>\n    <tr>\n';
            headers.forEach(h => {
                html += `      <th style="padding: 8px; background-color: #2C74B3; color: white; border: 1px solid #1e5a8e;">${h}</th>\n`;
            });
            html += '    </tr>\n  </thead>\n  <tbody>\n';
            
            parsedRows.forEach(row => {
                html += '    <tr>\n';
                row.forEach(cell => {
                    html += `      <td style="padding: 8px; border: 1px solid #e2e8f0;">${cell}</td>\n`;
                });
                html += '    </tr>\n';
            });
            
            html += '  </tbody>\n</table>\n';
            return html;
        });
    }

    // Initialize TinyMCE
    tinymce.init({
        selector: 'textarea#content',
        height: 700,
        menubar: 'file edit view insert format tools table',
        
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'codesample', 'help', 'wordcount'
        ],
        
        toolbar: 'undo redo | blocks | ' +
            'bold italic underline strikethrough | forecolor backcolor | ' +
            'alignleft aligncenter alignright alignjustify | ' +
            'bullist numlist outdent indent | ' +
            'table tabledelete | tableprops tablerowprops tablecellprops | ' +
            'tableinsertrowbefore tableinsertrowafter tabledeleterow | ' +
            'tableinsertcolbefore tableinsertcolafter tabledeletecol | ' +
            'codesample code | removeformat | help',
        
        // Table options
        table_toolbar: 'tableprops tabledelete | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol',
        table_appearance_options: true,
        table_grid: true,
        table_resize_bars: true,
        table_default_attributes: {
            border: '1'
        },
        table_default_styles: {
            'border-collapse': 'collapse',
            'width': '100%'
        },
        table_class_list: [
            {title: 'Default', value: ''},
            {title: 'Blue Header', value: 'table-blue'},
            {title: 'Striped', value: 'table-striped'}
        ],
        
        // Code sample settings
        codesample_languages: [
            {text: 'HTML/XML', value: 'markup'},
            {text: 'JavaScript', value: 'javascript'},
            {text: 'CSS', value: 'css'},
            {text: 'PHP', value: 'php'},
            {text: 'Python', value: 'python'},
            {text: 'Java', value: 'java'},
            {text: 'C', value: 'c'},
            {text: 'C++', value: 'cpp'},
            {text: 'SQL', value: 'sql'},
            {text: 'Bash', value: 'bash'}
        ],
        
        // Content style
        content_style: `
            body { 
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif; 
                font-size: 16px;
                line-height: 1.6;
                color: #1e293b;
                padding: 20px;
            }
            h1 { 
                color: #0f172a; 
                border-bottom: 3px solid #2C74B3; 
                padding-bottom: 0.3em;
                margin-top: 0;
            }
            h2 { 
                color: #1e293b; 
                border-bottom: 2px solid #e2e8f0; 
                padding-bottom: 0.3em;
            }
            h3 { color: #334155; }
            table { 
                border-collapse: collapse; 
                width: 100%; 
                margin: 1em 0;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            }
            table.table-blue thead { 
                background: linear-gradient(135deg, #2C74B3 0%, #205295 100%);
                color: white;
            }
            table.table-striped tbody tr:nth-child(even) {
                background-color: #f8fafc;
            }
            th { 
                padding: 12px; 
                text-align: left;
                font-weight: 700;
                background-color: #2C74B3;
                color: white;
                border: 1px solid #1e5a8e;
            }
            td { 
                padding: 10px; 
                border: 1px solid #e2e8f0;
            }
            tbody tr:hover {
                background-color: #e0f2fe;
            }
            code {
                background-color: #f1f5f9;
                padding: 2px 6px;
                border-radius: 3px;
                color: #dc2626;
                font-size: 0.9em;
            }
            pre {
                background-color: #1e293b;
                color: #e2e8f0;
                padding: 1em;
                border-radius: 8px;
                overflow-x: auto;
            }
            pre code {
                background-color: transparent;
                color: inherit;
                padding: 0;
            }
            blockquote {
                border-left: 4px solid #2C74B3;
                padding-left: 1em;
                margin: 1em 0;
                font-style: italic;
                color: #475569;
                background-color: #f8fafc;
                padding: 1em;
            }
        `,
        
        // Before setting content, convert markdown tables
        setup: function(editor) {
            editor.on('BeforeSetContent', function(e) {
                if (e.content) {
                    e.content = convertMarkdownTables(e.content);
                }
            });
            
            // Add custom button for markdown table
            editor.ui.registry.addButton('markdowntable', {
                text: '📋 MD Table',
                tooltip: 'Insert Markdown Table',
                onAction: function() {
                    const markdownTable = '| Header 1 | Header 2 | Header 3 |\n|----------|----------|----------|\n| Cell 1   | Cell 2   | Cell 3   |\n| Cell 4   | Cell 5   | Cell 6   |';
                    const htmlTable = convertMarkdownTables(markdownTable);
                    editor.insertContent(htmlTable);
                }
            });
            
            // Update toolbar to include markdown table button
            editor.on('init', function() {
                console.log('TinyMCE initialized successfully');
            });
        }
    });
</script>
@endpush

@push('styles')
<style>
    /* TinyMCE Container Styling */
    .tox-tinymce {
        border-radius: 12px !important;
        border: 1px solid #E5E7EB !important;
    }
    
    /* Custom table classes for TinyMCE */
    .tox .tox-edit-area {
        border-radius: 0 0 12px 12px !important;
    }
</style>
@endpush

@endsection
