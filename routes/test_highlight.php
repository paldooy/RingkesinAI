<?php

use Illuminate\Support\Facades\Route;

/**
 * Test route untuk demonstrate auto-highlighting feature
 * Akses: /test-highlight
 */
Route::get('/test-highlight', function () {
    // Sample markdown content
    $markdown = <<<'MARKDOWN'
# Ringkasan Materi Kalkulus Dasar

**Mata kuliah Kalkulus Dasar** ini disusun oleh Dr. Andri Suryana, membahas konsep-konsep fundamental dalam kalkulus yang penting untuk mahasiswa Program Studi Pendidikan Biologi FMIPA-UNINDRA.

## 1. Sistem Bilangan

Bab ini membahas dasar-dasar sistem bilangan yang menjadi fondasi dalam kalkulus.

### A. Sejarah Perkembangan Bilangan Real

1. **Bilangan Asli (N):**
   * Sifat: Tertutup, komutatif, dan asosiatif terhadap penjumlahan (+) dan perkalian (×).
   * Contoh: 1, 2, 3, ...

2. **Bilangan Bulat (Z):**
   * Sifat: Tertutup, komutatif, dan asosiatif terhadap operasi + dan ×.
   * Contoh: ..., -2, -1, 0, 1, 2, ...

3. **Bilangan Rasional (Q):**
   * Definisi: Bilangan yang dapat dinyatakan sebagai pecahan p/q dimana p,q ∈ Z dan q ≠ 0
   * Contoh: 1/2, -3/4, 2.5

### B. Konsep Penting dalam Sistem Bilangan

**Perhatian**: Memahami perbedaan antara berbagai jenis bilangan adalah hal yang fundamental dalam mempelajari kalkulus.

## 2. Limit dan Kontinuitas

### A. Definisi Limit

**Definisi**: Limit dari fungsi f(x) saat x mendekati a adalah nilai yang didekati oleh f(x) ketika x semakin dekat ke a.

**Catatan**: Nilai limit tidak selalu sama dengan nilai fungsi di titik tersebut.

### B. Contoh Penerapan

Misalnya, untuk mencari limit fungsi f(x) = (x² - 4)/(x - 2) saat x → 2:

* Langkah penting: Faktorkan pembilang
* f(x) = (x - 2)(x + 2)/(x - 2)
* Sederhanakan: f(x) = x + 2
* Hasil: lim(x→2) f(x) = 4

## 3. Tabel Sifat Bilangan

| Jenis Bilangan | Symbol | Contoh | Sifat Utama |
|----------------|--------|--------|-------------|
| Bilangan Asli | N | 1, 2, 3 | Tertutup terhadap + dan × |
| Bilangan Bulat | Z | -1, 0, 1 | Memiliki invers aditif |
| Bilangan Rasional | Q | 1/2, 0.5 | Dapat dinyatakan sebagai pecahan |

**Catatan Penting**: Tabel di atas merupakan ringkasan sifat-sifat fundamental dari masing-masing jenis bilangan.

## 4. Kesimpulan

Materi kalkulus dasar ini mencakup konsep-konsep fundamental yang penting untuk dipahami. Perhatian khusus harus diberikan pada pemahaman definisi dan penerapannya dalam contoh-contoh soal.
MARKDOWN;

    // Convert to HTML with highlights
    $html = format_note_content_with_highlights($markdown, true);
    
    // Display result
    return view('test-highlight', [
        'markdown' => $markdown,
        'html' => $html
    ]);
});
