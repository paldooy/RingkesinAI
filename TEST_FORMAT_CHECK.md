# Testing AI Summarize - Format Check

## Test Case: Markdown Formatting

### Before:
- Upload file dengan markdown format
- Generate summary dengan AI
- Save ke catatan

### Expected Result:
Catatan tersimpan dengan format markdown yang rapi, bukan berantakan dengan tag HTML atau markup yang rusak.

### Test Data:

#### Sample Markdown Input:
```
# Ringkasan Kalkulus Dasar

## Bab 1: Sistem Bilangan

### Bilangan Asli (N)
- Bilangan bulat positif (1, 2, 3, ...)
- **Sifat:** Tertutup, Komutatif, Asosiatif

### Rumus Penting
1. f(x) = 2x + 1
2. Area = π × r²
3. Limit: lim(x→0) sin(x)/x = 1

## Contoh Soal
**Soal 1:** Hitung turunan dari f(x) = x² + 2x - 1

**Penyelesaian:**
f'(x) = 2x + 2
```

### Verification Points:
✅ Headers (# ## ###) tetap sebagai headers  
✅ Bold (**text**) tetap bold  
✅ Lists (- dan 1.) tetap sebagai lists  
✅ Code blocks (```) tetap sebagai code blocks  
✅ Rumus matematika tidak dipecah atau di-highlight  
✅ TIDAK ADA tag <mark> tersimpan di database  
✅ Format Markdown murni tersimpan, HTML hanya di render saat ditampilkan
