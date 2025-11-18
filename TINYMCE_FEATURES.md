# TinyMCE Editor Enhancement

## ✨ Fitur Baru di Edit Page

### 1. **Rich Text Editor (TinyMCE 7)**
- WYSIWYG editor lengkap dengan toolbar intuitif
- Live preview saat mengetik
- Support copy-paste dari Word/Google Docs

### 2. **Table Features** 📊
#### Insert Table (GUI):
- Klik **Insert → Table** atau icon table di toolbar
- Pilih ukuran tabel (grid selector)
- Resize kolom dengan drag & drop
- Merge/split cells
- Table properties (border, padding, spacing)

#### Table Toolbar:
- `tableinsertrowbefore` - Insert row above
- `tableinsertrowafter` - Insert row below
- `tabledeleterow` - Delete row
- `tableinsertcolbefore` - Insert column before
- `tableinsertcolafter` - Insert column after
- `tabledeletecol` - Delete column
- `tableprops` - Table properties

#### Table Styles:
1. **Default** - Basic table with borders
2. **Blue Header** - Gradient blue header (class: `table-blue`)
3. **Striped** - Alternating row colors (class: `table-striped`)

#### Markdown Table Support:
- Paste markdown table → auto-convert to HTML
- Custom button **📋 MD Table** → insert template
- Format:
```markdown
| Header 1 | Header 2 | Header 3 |
|----------|----------|----------|
| Cell 1   | Cell 2   | Cell 3   |
| Cell 4   | Cell 5   | Cell 6   |
```

### 3. **Code Highlighting** 💻
#### Insert Code Block:
- **Method 1**: Insert → Code Sample
- **Method 2**: Format → Code
- **Method 3**: Keyboard shortcut (if configured)

#### Supported Languages:
- HTML/XML
- JavaScript
- CSS
- PHP
- Python
- Java
- C/C++
- SQL
- Bash

#### Features:
- Syntax highlighting (Prism.js)
- Dark theme code blocks
- Copy code button
- Line numbers (optional)

### 4. **Text Highlighting** 🎨
#### Background Color:
- Select text → Format → Background Color
- Or: Toolbar → Background color picker
- Custom color picker available

#### Foreground Color:
- Select text → Format → Text Color
- Or: Toolbar → Text color picker

### 5. **Advanced Formatting**
- **Bold** (Ctrl+B)
- **Italic** (Ctrl+I)
- **Underline** (Ctrl+U)
- **Strikethrough**
- Headings (H1-H6)
- Bullet lists
- Numbered lists
- Blockquotes
- Horizontal rules
- Links
- Images

## 🎯 Cara Menggunakan

### Insert Table (GUI Method):
1. Klik icon **Table** di toolbar
2. Hover mouse untuk pilih ukuran (e.g., 3x3)
3. Klik untuk insert
4. Isi cells dengan content
5. Right-click untuk opsi tambahan

### Insert Table (Markdown Method):
1. Klik button **📋 MD Table** di toolbar
2. Edit header dan cells sesuai kebutuhan
3. Atau paste markdown table langsung → auto-convert

### Insert Code Block:
1. Klik **Insert → Code Sample**
2. Pilih bahasa (e.g., Python, JavaScript)
3. Paste atau ketik code
4. Klik OK

### Text Highlighting:
1. Select teks yang ingin di-highlight
2. Klik icon **Background Color** (paint bucket)
3. Pilih warna (e.g., yellow untuk highlight)

## 🔧 Konfigurasi TinyMCE

### Plugins Aktif:
```javascript
'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 
'preview', 'anchor', 'searchreplace', 'visualblocks', 'code', 
'fullscreen', 'insertdatetime', 'media', 'table', 'codesample', 
'help', 'wordcount'
```

### Toolbar Layout:
```
[Undo/Redo] | [Blocks] | 
[Bold Italic Underline Strike] | [Text Color Background Color] | 
[Align Left Center Right Justify] | 
[Bullet List Numbered List Outdent Indent] | 
[Table Operations] | 
[Code Sample] [Source Code] | 
[Remove Format] | [Help]
```

## 🎨 Custom Styling

### Table Styles (Auto-Applied):
```css
/* Blue Header Table */
thead {
    background: linear-gradient(135deg, #2C74B3 0%, #205295 100%);
    color: white;
}

/* Hover Effect */
tbody tr:hover {
    background-color: #e0f2fe;
}
```

### Code Block Styles:
```css
pre {
    background-color: #1e293b;
    color: #e2e8f0;
    padding: 1em;
    border-radius: 8px;
}
```

## 🔄 Markdown to HTML Conversion

### Auto-Conversion Features:
1. **On Load**: Markdown tables in existing notes → HTML tables
2. **On Paste**: Paste markdown → auto-detect & convert
3. **Custom Button**: Quick insert markdown table template

### Conversion Function:
```javascript
function convertMarkdownTables(content) {
    // Regex: /(\|[^\n]+\|\r?\n)((?:\|[-:| ]+\|\r?\n))((?:\|[^\n]+\|\r?\n?)+)/gm
    // Converts markdown table syntax to HTML <table>
    // Preserves headers, separators, and data rows
}
```

## 📝 Best Practices

### For Tables:
✅ Use GUI table editor untuk kontrol penuh
✅ Apply "Blue Header" style untuk konsistensi
✅ Merge cells untuk complex layouts
❌ Jangan mix markdown & HTML table di satu note

### For Code:
✅ Selalu pilih bahasa untuk syntax highlighting
✅ Use code sample untuk multi-line code
✅ Use inline `code` untuk single keywords
❌ Jangan paste code tanpa formatting

### For Highlighting:
✅ Yellow/light colors untuk highlights
✅ Consistent color scheme per note
❌ Jangan terlalu banyak warna (max 2-3)

## 🐛 Troubleshooting

### Issue: Table tidak muncul
- **Fix**: Pastikan content memiliki `<table>` tag, bukan markdown
- **Check**: Inspect element → lihat HTML output

### Issue: Code tidak ter-highlight
- **Fix**: Pilih bahasa di dropdown saat insert
- **Check**: `<pre class="language-xxx">` harus ada

### Issue: Markdown table tidak convert
- **Fix**: Paste ulang atau gunakan button **📋 MD Table**
- **Check**: Format markdown harus benar (pipe + dash separator)

## 🚀 Fitur Mendatang (Optional)

- [ ] Auto-save draft (localStorage)
- [ ] Export to PDF
- [ ] Import from Word/Google Docs
- [ ] Collaborative editing
- [ ] Version history
- [ ] Table templates library
- [ ] Custom code themes

## 📚 Resources

- TinyMCE Docs: https://www.tiny.cloud/docs/
- Table Plugin: https://www.tiny.cloud/docs/plugins/opensource/table/
- Code Sample Plugin: https://www.tiny.cloud/docs/plugins/opensource/codesample/
- Markdown Guide: https://www.markdownguide.org/cheat-sheet/
