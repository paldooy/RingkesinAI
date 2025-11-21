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

        <form method="POST" action="{{ route('notes.store') }}" class="max-w-7xl mx-auto" id="noteForm">
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
                </div>

                <!-- Metadata & Actions Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pb-16">
                    <!-- Actions -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB]">
                        <h3 class="text-lg font-bold text-[#1E293B] mb-4">Aksi</h3>
                        <div class="space-y-3">
                            <button 
                                type="button"
                                onclick="submitNoteForm()"
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
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#E5E7EB]"
                         x-data="categoryForm"
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
                                            <p class="text-xs text-gray-500 mb-2" x-text="'Emojis loaded: ' + emojis.length"></p>
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
                                            <p x-show="emojis.length === 0" class="text-xs text-red-500 mt-2">⚠️ No emojis loaded</p>
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
    // Alpine.js component for category form
    document.addEventListener('alpine:init', () => {
        Alpine.data('categoryForm', () => ({
            selectedCat: '{{ old("category_id") }}',
            newCatName: '',
            newCatEmoji: '📁',
            newCatColor: '#3B82F6',
            showEmojiPicker: false,
            emojis: [],
            
            init() {
                // Initialize emoji list
                this.emojis = ['📁', '📚', '📖', '📝', '📊', '💼', '🎓', '🔬', '🧪', '📐', '📏', '🖊️', '✏️', '📌', '📍', '🎨', '🎭', '🎪', '🎬', '🎮', '🎯', '🎲', '🧩', '🎸', '🎹', '🎺', '🎻', '🥁', '💻', '⌨️', '🖥️', '🖨️', '📱', '☎️', '📞', '📟', '📠', '📡', '🔋', '🔌', '💡', '🔦', '🕯️', '🧯', '🛢️', '💰', '💴', '💵', '💶', '💷', '💸', '💳', '🧾', '✉️', '📧', '📨', '📩', '📤', '📥', '📦', '📫', '📪', '📬', '📭', '📮', '🗳️', '✏️', '✒️', '🖋️', '🖊️', '🖌️', '🖍️', '📝', '💼', '📁', '📂', '🗂️', '📅', '📆', '🗒️', '🗓️', '📇', '📈', '📉', '📊', '📋', '📌', '📍', '📎', '🖇️', '📏', '📐', '✂️', '🗃️', '🗄️', '🗑️'];
                console.log('✅ Category form initialized');
                console.log('📊 Total emojis:', this.emojis.length);
                console.log('🎯 showEmojiPicker state:', this.showEmojiPicker);
                console.log('📁 First 5 emojis:', this.emojis.slice(0, 5));
            },
            
            selectEmoji(emoji) {
                this.newCatEmoji = emoji;
                this.showEmojiPicker = false;
                console.log('✅ Emoji selected:', emoji);
                console.log('📊 Current state - showEmojiPicker:', this.showEmojiPicker, 'newCatEmoji:', this.newCatEmoji);
            },
            
            toggleEmojiPicker() {
                this.showEmojiPicker = !this.showEmojiPicker;
                console.log('🔄 Emoji picker toggled:', this.showEmojiPicker);
                console.log('📋 Available emojis:', this.emojis.length);
            }
        }));
    });

    // Function untuk submit form dari button
    window.submitNoteForm = function() {
        console.log('🚀 submitNoteForm called');
        const form = document.querySelector('#noteForm');
        
        if (!form) {
            console.error('❌ Form not found');
            return;
        }
        
        // Sync TinyMCE
        if (typeof tinymce !== 'undefined' && tinymce.get('content')) {
            tinymce.get('content').save();
            console.log('✅ TinyMCE synced');
        }
        
        // Validate
        const title = form.querySelector('#title');
        const content = form.querySelector('#content');
        
        if (!title?.value?.trim()) {
            alert('❌ Judul harus diisi!');
            title.focus();
            return;
        }
        
        if (!content?.value?.trim()) {
            alert('❌ Konten harus diisi!');
            return;
        }
        
        console.log('✅ Validation passed, submitting...');
        form.submit();
    };
    
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

    // Ensure TinyMCE content is synced before form submission
    const noteForm = document.querySelector('#noteForm');
    
    if (noteForm) {
        noteForm.addEventListener('submit', function(e) {
            console.log('✅ Form submit event triggered');
            
            // Trigger TinyMCE to save content to textarea
            if (typeof tinymce !== 'undefined' && tinymce.get('content')) {
                tinymce.get('content').save();
                console.log('✅ TinyMCE content saved to textarea');
            }
            
            // Log form data for debugging
            const formData = new FormData(this);
            console.log('📋 Form data:', {
                title: formData.get('title'),
                content_length: formData.get('content')?.length || 0,
                category_id: formData.get('category_id'),
                new_category_name: formData.get('new_category_name'),
                tags: formData.getAll('tags[]')
            });
            
            // Check if form is valid
            if (!this.checkValidity()) {
                console.error('❌ Form validation failed');
                e.preventDefault();
                return false;
            }
            
            console.log('✅ Form submitting...');
            return true; // Allow form to submit
        });
    }
    
    // Test function untuk debug
    window.testFormSubmit = function() {
        console.log('🔍 Testing form submit...');
        
        const form = document.querySelector('#noteForm');
        if (!form) {
            console.error('❌ Form not found!');
            return;
        }
        
        console.log('✅ Form found');
        console.log('Form action:', form.action);
        console.log('Form method:', form.method);
        
        // Check required fields
        const title = form.querySelector('#title');
        const content = form.querySelector('#content');
        
        console.log('Title value:', title?.value || '(empty)');
        console.log('Content value length:', content?.value?.length || 0);
        
        if (!title?.value) {
            alert('❌ Judul harus diisi!');
            title.focus();
            return;
        }
        
        if (typeof tinymce !== 'undefined' && tinymce.get('content')) {
            tinymce.get('content').save();
            console.log('✅ TinyMCE saved');
        }
        
        if (!content?.value) {
            alert('❌ Konten harus diisi!');
            return;
        }
        
        console.log('✅ All validations passed, submitting form...');
        form.submit();
    };
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
