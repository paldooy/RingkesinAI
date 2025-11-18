# 📝 Setup Guide: Gemini API Integration

## ✅ Instalasi Berhasil!

Integrasi Gemini API telah berhasil ditambahkan ke project Laravel Anda dengan fitur lengkap:

### 🎯 Fitur yang Telah Diimplementasikan

1. ✅ **File Extraction Service**
   - Support PDF, DOCX, DOC, dan TXT
   - Ekstraksi teks otomatis dari berbagai format dokumen
   - Error handling untuk file yang tidak valid

2. ✅ **Gemini API Integration**
   - Menggunakan model `gemini-2.0-flash-lite` (hemat quota)
   - Custom instructions support
   - Error handling lengkap (quota, timeout, dll)
   - Token usage tracking

3. ✅ **Caching System**
   - Hasil summary di-cache selama 1 jam
   - Menghindari API calls berulang untuk dokumen yang sama
   - Hemat quota API

4. ✅ **Rate Limiting**
   - 5 requests per menit per user
   - Mencegah abuse dan quota habis cepat
   - User-friendly error messages

5. ✅ **Updated Controller**
   - Full integration dengan services
   - JSON instructions parsing
   - Session management untuk save note

---

## 🔧 Cara Setup

### 1. Install Dependencies (Sudah Selesai ✅)
```bash
composer require smalot/pdfparser phpoffice/phpword
```

### 2. Konfigurasi Environment

Edit file `.env` Anda dan tambahkan:

```bash
# Google Gemini API Configuration
GOOGLE_API_KEY=AIzaSyBxn82onmnjp3nXgwFKHWjxA9Xam02JqQ8
GOOGLE_MODEL=gemini-2.0-flash-lite
```

**Cara mendapatkan API Key:**
1. Kunjungi: https://aistudio.google.com/apikey
2. Login dengan akun Google
3. Klik "Create API Key"
4. Copy key dan paste ke `.env`

### 3. Clear Config Cache

```bash
php artisan config:clear
php artisan cache:clear
```

---

## 🧪 Testing

### Test Route (Sudah Ada)
Akses: `http://localhost/test-gemini`

### Test dengan Frontend
1. Login ke aplikasi
2. Pergi ke menu "AI Summarize"
3. Upload file PDF/DOCX/TXT
4. (Opsional) Tambahkan instruksi khusus
5. Klik "Generate Summary"
6. Simpan ke Notes jika diinginkan

---

## 📊 Model Options

### Recommended: `gemini-2.0-flash-lite`
- ✅ Paling murah (hemat quota)
- ✅ Cepat (low latency)
- ✅ Cocok untuk summary sederhana
- ⚠️ Kualitas sedikit di bawah model Pro

### Alternative: `gemini-2.0-flash`
- ✅ Seimbang antara harga dan kualitas
- ✅ Kualitas lebih baik dari lite
- ⚠️ Quota habis lebih cepat

### Premium: `gemini-2.5-pro`
- ✅ Kualitas terbaik
- ✅ Context window besar
- ❌ Sangat mahal
- ❌ Quota cepat habis

---

## 🔍 File Structure

```
app/
├── Http/
│   └── Controllers/
│       └── AISummarizeController.php   ← Updated dengan Gemini integration
├── Services/
│   ├── FileExtractorService.php        ← NEW: Extract text dari file
│   └── GeminiService.php                ← NEW: Gemini API wrapper

config/
└── services.php                         ← Updated: Gemini config

.env.example                             ← Updated: Added GOOGLE_* vars
```

---

## 🚀 API Endpoints

### POST `/summarize`
Generate summary dari uploaded file.

**Request:**
```javascript
{
    "document": File (PDF/DOCX/TXT),
    "instructions": JSON string atau array (optional)
}
```

**Response (Success):**
```json
{
    "success": true,
    "summary": "Ringkasan lengkap...",
    "fileName": "document.pdf",
    "model": "gemini-2.0-flash-lite",
    "tokens_used": 1234,
    "cached": false
}
```

**Response (Error):**
```json
{
    "success": false,
    "error": "Error message here"
}
```

### POST `/summarize/save`
Save summary as note.

**Request:**
```javascript
{
    "summary": "string",
    "category_id": integer,
    "title": "string (optional)"
}
```

---

## ⚠️ Troubleshooting

### Error: "GOOGLE_API_KEY belum dikonfigurasi"
**Solusi:** Pastikan `.env` sudah berisi `GOOGLE_API_KEY=your-key-here` dan jalankan `php artisan config:clear`

### Error: "Kuota API Gemini sudah habis"
**Solusi:**
1. Tunggu sampai besok (free tier reset setiap hari)
2. Buat API key baru
3. Upgrade ke paid plan

### Error: "File kosong atau tidak dapat dibaca"
**Solusi:**
1. Pastikan file berisi teks (bukan gambar atau scan)
2. Coba convert PDF to text-based PDF
3. Cek format file (DOCX, bukan DOC lama)

### Error: "Terlalu banyak permintaan"
**Solusi:** Rate limit aktif - tunggu 1 menit dan coba lagi

---

## 💡 Tips Optimasi

### 1. Hemat Quota
- Gunakan `gemini-2.0-flash-lite` sebagai default
- Aktifkan caching (sudah diimplementasikan)
- Batasi ukuran file yang di-upload

### 2. Improve Quality
- Berikan instruksi yang spesifik
- Gunakan file text-based (bukan scan)
- Limit panjang dokumen (~10-20 halaman optimal)

### 3. Monitoring
- Log ada di `storage/logs/laravel.log`
- Check error patterns untuk debugging
- Monitor token usage

---

## 📚 Resources

- **Gemini API Docs:** https://ai.google.dev/gemini-api/docs
- **Get API Key:** https://aistudio.google.com/apikey
- **Rate Limits:** https://ai.google.dev/gemini-api/docs/rate-limits
- **Model List:** https://ai.google.dev/gemini-api/docs/models

---

## 🎉 Next Steps

1. ✅ Test dengan file PDF
2. ✅ Test dengan file DOCX
3. ✅ Test dengan custom instructions
4. ✅ Verify caching works
5. ✅ Test rate limiting
6. ⚠️ Deploy to production (update `.env` di server)

---

**Questions?** Check the logs or test the endpoints manually! 🚀
