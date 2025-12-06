<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotesController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AISummarizeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register/send-otp', [AuthController::class, 'sendRegisterOtp'])->name('register.send-otp');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Notes
    Route::resource('notes', NotesController::class);
    Route::get('/notes/{note}/export-pdf', [NotesController::class, 'exportPdf'])->name('notes.export-pdf');
    Route::get('/notes/{note}/resummarize', [NotesController::class, 'resimmarize'])->name('notes.resummarize');
    Route::post('/notes/bulk-delete', [NotesController::class, 'bulkDelete'])->name('notes.bulk-delete');
    Route::post('/notes/{note}/toggle-favorite', [NotesController::class, 'toggleFavorite'])->name('notes.toggle-favorite');
    
    // Note Sharing
    Route::post('/notes/{note}/generate-share', [NotesController::class, 'generateShareCode'])->name('notes.generate-share');
    Route::get('/import', [NotesController::class, 'importForm'])->name('notes.import.form');
    Route::post('/import', [NotesController::class, 'processImportCode'])->name('notes.import.process');
    Route::get('/import/{code}', [NotesController::class, 'showImport'])->name('notes.import.show');
    Route::post('/import/{code}', [NotesController::class, 'importNote'])->name('notes.import.save');
    
    // Categories
    Route::resource('categories', CategoriesController::class)->only(['index', 'store', 'update', 'destroy']);
    
    // AI Summarize
    Route::get('/summarize', [AISummarizeController::class, 'index'])->name('summarize.index');
    Route::post('/summarize', [AISummarizeController::class, 'generate'])->name('summarize.generate');
    Route::post('/summarize/save', [AISummarizeController::class, 'save'])->name('summarize.save');
    Route::post('/summarize/revise', [AISummarizeController::class, 'revise'])->name('summarize.revise');
    Route::post('/summarize/regenerate', [AISummarizeController::class, 'regenerate'])->name('summarize.regenerate');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/email/request', [ProfileController::class, 'requestEmailChange'])->name('profile.email.request');
    Route::post('/profile/email/verify', [ProfileController::class, 'verifyEmailChange'])->name('profile.email.verify');
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// //AI Test Routes
// Route::get('/test-gemini', function () {
//     try {
//         $gemini = app(\App\Services\GeminiService::class);
//         $result = $gemini->testConnection();
        
//         return response()->json($result);
//     } catch (Exception $e) {
//         return response()->json([
//             'success' => false,
//             'error' => $e->getMessage(),
//         ], 500);
//     }
// });

// // Test route for highlighting feature
// Route::get('/test-highlight', function () {
//     $markdown = <<<'MD'
// # Ringkasan Materi Kalkulus Dasar

// ## Pendahuluan

// * **Mata Kuliah:** Kalkulus Dasar (3 SKS)
// * **Tujuan:** Memahami konsep dasar kalkulus yang penting.
// * **Materi:** Terdiri dari 6 bab fundamental.

// ## Bab 1: Sistem Bilangan

// ### A. Sejarah Perkembangan Bilangan Real

// 1. **Bilangan Asli (N):**
//    * **Definisi:** Bilangan yang dimulai dari 1, 2, 3, dan seterusnya
//    * **Sifat:** Tertutup terhadap operasi penjumlahan dan perkalian
//    * **Contoh:** 1, 2, 3, 4, 5, ...

// 2. **Bilangan Bulat (Z):**
//    * **Pengertian:** Bilangan yang mencakup bilangan asli, nol, dan negatifnya
//    * **Sifat Penting:** Memiliki elemen identitas (0 untuk +, 1 untuk ×)
//    * **Contoh:** ..., -2, -1, 0, 1, 2, ...

// 3. **Bilangan Rasional (Q):**
//    * **Definisi:** Bilangan yang dapat dinyatakan sebagai perbandingan bilangan bulat (a/b, b ≠ 0)
//    * **Catatan:** Setiap bilangan bulat adalah bilangan rasional
//    * **Contoh:** 1/2, -3/4, 2.5

// ### B. Tabel Sifat Bilangan

// | Jenis Bilangan | Symbol | Contoh | Sifat Utama |
// |----------------|--------|--------|-------------|
// | Bilangan Asli | N | 1, 2, 3 | Tertutup terhadap + dan × |
// | Bilangan Bulat | Z | -1, 0, 1 | Memiliki invers aditif |
// | Bilangan Rasional | Q | 1/2, 0.5 | Dapat dinyatakan sebagai pecahan |

// **Perhatian:** Tabel di atas merupakan ringkasan yang penting untuk dipahami.

// ## Kesimpulan

// Materi ini mencakup konsep-konsep fundamental yang penting. Perhatian khusus harus diberikan pada pemahaman definisi dan penerapannya dalam contoh-contoh soal. Misalnya, memahami perbedaan antara jenis bilangan adalah hal yang esensial.
// MD;

//     $html = format_note_content_with_highlights($markdown, true);
    
//     return view('test-highlight', ['html' => $html, 'markdown' => $markdown]);
// });

// Route::get('/test-file-extraction', function () {
//     return view('test-file-extraction');
// });

// // Test route untuk highlight feature
// Route::get('/test-highlight', function () {
//     $markdown = <<<'MARKDOWN'
// # Ringkasan Materi Kalkulus Dasar

// **Mata kuliah Kalkulus Dasar** membahas konsep-konsep **fundamental** yang **penting** untuk dipahami.

// ## 1. Sistem Bilangan

// ### A. Definisi

// **Definisi**: Bilangan rasional **adalah** bilangan yang dapat dinyatakan sebagai pecahan.

// ### B. Contoh

// **Contoh**: Bilangan 1/2, -3/4, dan 2.5 **merupakan** bilangan rasional.

// **Perhatian**: Jangan lupa bahwa 0 tidak boleh menjadi penyebut.

// **Catatan**: Pemahaman **konsep** ini sangat **penting** untuk materi selanjutnya.

// ## 2. Tabel Sifat Bilangan

// | Jenis | Symbol | Contoh | Sifat |
// |-------|--------|--------|-------|
// | Asli | N | 1, 2, 3 | Tertutup |
// | Bulat | Z | -1, 0, 1 | Memiliki invers |

// **Misalnya**, kita bisa lihat perbedaan **fundamental** antara bilangan asli dan bulat.
// MARKDOWN;

//     $html = format_note_content_with_highlights($markdown, true);
    
//     return response()->json([
//         'markdown' => $markdown,
//         'html' => $html,
//         'success' => true
//     ]);
// });
