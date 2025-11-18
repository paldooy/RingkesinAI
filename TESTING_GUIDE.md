# 🧪 Quick Test Commands

## Test Gemini API Connection

### Option 1: Browser
Open in your browser:
```
http://localhost:8000/test-gemini
```

### Option 2: Command Line (PowerShell)
```powershell
# Start Laravel server (if not running)
php artisan serve

# In another terminal:
Invoke-WebRequest -Uri "http://localhost:8000/test-gemini" -Method GET | Select-Object -ExpandProperty Content
```

### Expected Success Response:
```json
{
  "success": true,
  "message": "Koneksi ke Gemini API berhasil!",
  "model": "gemini-2.0-flash-lite"
}
```

### Expected Error (if API key not set):
```json
{
  "success": false,
  "error": "GOOGLE_API_KEY belum dikonfigurasi di file .env"
}
```

---

## Test File Upload & Summarize

### Via Browser UI:
```
http://localhost:8000/test-file-extraction
```

Steps:
1. Click "Choose File" and select a PDF/DOCX/TXT
2. (Optional) Add custom instructions
3. Click "Generate Summary"
4. Wait for result

### Via API (PowerShell):
```powershell
# Create test file
"Ini adalah contoh teks untuk testing. Gemini akan meringkas teks ini." | Out-File -FilePath "test.txt" -Encoding UTF8

# Test the API
$uri = "http://localhost:8000/summarize"
$file = "test.txt"
$instructions = '[{"id":1,"text":"Buat ringkasan singkat"}]'

# Note: File upload via PowerShell is complex, use the browser UI instead
# Or use Postman/Insomnia for API testing
```

---

## Test Existing Summarize Feature

1. Login to your app: `http://localhost:8000/login`
2. Go to "AI Summarize" menu
3. Upload a document
4. Add instructions (optional)
5. Click generate
6. Save to Notes

---

## Verify Installation

### Check Dependencies:
```powershell
composer show smalot/pdfparser
composer show phpoffice/phpword
```

### Check Config:
```powershell
php artisan config:show services.gemini
```

Expected output:
```
[
  'api_key' => 'AIzaSy...',
  'model' => 'gemini-2.0-flash-lite'
]
```

### Check Routes:
```powershell
php artisan route:list --path=summarize
```

Expected routes:
- GET /summarize
- POST /summarize
- POST /summarize/save
- GET /test-gemini
- GET /test-file-extraction

---

## Common Issues & Solutions

### Issue: "Class 'App\Services\GeminiService' not found"
**Solution:**
```powershell
composer dump-autoload
php artisan config:clear
```

### Issue: API returns 404
**Solution:**
```powershell
# Make sure server is running
php artisan serve

# Check if routes are registered
php artisan route:list
```

### Issue: "GOOGLE_API_KEY belum dikonfigurasi"
**Solution:**
1. Edit `.env` file
2. Add: `GOOGLE_API_KEY=your-key-here`
3. Run: `php artisan config:clear`

### Issue: Quota exceeded
**Solution:**
- Wait 24 hours (free tier resets daily)
- Or get new API key from https://aistudio.google.com/apikey

---

## Production Deployment Checklist

Before deploying to production:

1. ✅ Set `GOOGLE_API_KEY` in production `.env`
2. ✅ Set `GOOGLE_MODEL` (keep `gemini-2.0-flash-lite`)
3. ✅ Run migrations (if any)
4. ✅ Clear all caches:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan route:clear
   php artisan view:clear
   ```
5. ✅ Set up proper storage permissions:
   ```bash
   php artisan storage:link
   chmod -R 775 storage
   chmod -R 775 bootstrap/cache
   ```
6. ✅ Test with real documents
7. ✅ Monitor logs: `tail -f storage/logs/laravel.log`
8. ✅ Set up log rotation
9. ✅ Configure rate limiting based on expected traffic
10. ✅ Set up monitoring/alerts for API quota

---

## Performance Tuning

### Increase Cache Duration
Edit `AISummarizeController.php`:
```php
// Change from 3600 (1 hour) to 86400 (24 hours)
Cache::put($cacheKey, [...], 86400);
```

### Adjust Rate Limiting
Edit `AISummarizeController.php`:
```php
// Change from 5 requests/min to 10
if (RateLimiter::tooManyAttempts($key, 10)) {
```

### Switch to Faster Model
Edit `.env`:
```bash
# For better quality (uses more quota):
GOOGLE_MODEL=gemini-2.0-flash

# For best quality (premium):
GOOGLE_MODEL=gemini-2.5-pro
```

---

**Happy Testing! 🚀**
