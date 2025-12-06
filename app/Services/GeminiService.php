<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;

class GeminiService
{
    private array $apiKeys = [];
    private string $model;
    private string $baseUrl = 'https://generativelanguage.googleapis.com';

    public function __construct()
    {
        $this->loadApiKeys();
        $this->model = config('services.gemini.model', 'gemini-2.0-flash-lite');
        
        if (empty($this->apiKeys)) {
            throw new Exception('GOOGLE_API_KEY atau GEMINI_API_KEYS belum dikonfigurasi di file .env');
        }
    }

    /**
     * Load API keys from config (supports single key or multiple keys)
     */
    private function loadApiKeys(): void
    {
        // Check for multiple keys first (comma-separated)
        $multipleKeys = config('services.gemini.api_keys');
        
        if (!empty($multipleKeys)) {
            $keys = array_map('trim', explode(',', $multipleKeys));
            $this->apiKeys = array_filter($keys, fn($key) => !empty($key));
        }
        
        // Fallback to single key
        if (empty($this->apiKeys)) {
            $singleKey = config('services.gemini.api_key');
            if (!empty($singleKey)) {
                $this->apiKeys = [$singleKey];
            }
        }
    }

    /**
     * Get next available API key (rotation with rate limit tracking)
     */
    private function getAvailableApiKey(): ?string
    {
        $now = time();
        
        foreach ($this->apiKeys as $index => $key) {
            $cacheKey = 'gemini_rate_limit_' . md5($key);
            $rateLimitedUntil = Cache::get($cacheKey, 0);
            
            // Key is available if not rate limited or rate limit expired
            if ($rateLimitedUntil < $now) {
                // Track usage for load balancing (optional)
                $usageKey = 'gemini_usage_' . md5($key);
                Cache::increment($usageKey);
                
                return $key;
            }
        }
        
        // All keys are rate limited, return first key anyway (will show error to user)
        return $this->apiKeys[0] ?? null;
    }

    /**
     * Mark API key as rate limited
     */
    private function markKeyAsRateLimited(string $apiKey, int $cooldownSeconds = 60): void
    {
        $cacheKey = 'gemini_rate_limit_' . md5($apiKey);
        Cache::put($cacheKey, time() + $cooldownSeconds, $cooldownSeconds);
        
        Log::warning('Gemini API key rate limited', [
            'key_prefix' => substr($apiKey, 0, 10) . '...',
            'cooldown_seconds' => $cooldownSeconds,
        ]);
    }

    /**
     * Generate summary from text using Gemini API with key rotation.
     *
     * @param string $content
     * @param array $instructions
     * @return array
     * @throws Exception
     */
    public function generateSummary(string $content, array $instructions = []): array
    {
        // Build prompt with instructions
        $prompt = $this->buildPrompt($content, $instructions);
        
        // Try each API key until success or all fail
        $lastError = null;
        $triedKeys = [];
        
        foreach ($this->apiKeys as $apiKey) {
            // Skip if this key is currently rate limited
            $cacheKey = 'gemini_rate_limit_' . md5($apiKey);
            if (Cache::get($cacheKey, 0) > time()) {
                continue;
            }
            
            $triedKeys[] = substr($apiKey, 0, 10) . '...';
            
            $result = $this->makeApiRequest($apiKey, $prompt);
            
            if ($result['success']) {
                return $result;
            }
            
            // If rate limited, mark this key and try next
            if (isset($result['error_code']) && $result['error_code'] === 429) {
                $this->markKeyAsRateLimited($apiKey, 60);
                $lastError = $result;
                continue;
            }
            
            // For other errors, return immediately
            return $result;
        }
        
        // All keys failed
        if ($lastError) {
            $lastError['error'] = '⏱️ Semua API key sedang dalam rate limit. Silakan tunggu 1-2 menit lalu coba lagi. (Tried ' . count($triedKeys) . ' keys)';
            return $lastError;
        }
        
        return [
            'success' => false,
            'error' => 'Tidak ada API key yang tersedia.',
        ];
    }

    /**
     * Make actual API request with specific key
     */
    private function makeApiRequest(string $apiKey, string $prompt): array
    {
        // Prepare API URL
        $url = "{$this->baseUrl}/v1beta/models/{$this->model}:generateContent?key={$apiKey}";
        
        try {
            // Increased timeout for large documents (120 seconds for response, 30 seconds for connection)
            $response = Http::connectTimeout(30)
                ->timeout(120)
                ->post($url, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'topK' => 40,
                        'topP' => 0.95,
                        'maxOutputTokens' => 12000,
                    ]
                ]);

            if (!$response->successful()) {
                return $this->handleError($response);
            }

            $result = $response->json();
            
            // Extract summary text
            $summary = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;
            
            if (empty($summary)) {
                throw new Exception('Gemini API tidak mengembalikan hasil ringkasan');
            }

            // Check if response was truncated due to token limit
            $finishReason = $result['candidates'][0]['finishReason'] ?? 'STOP';
            $isTruncated = in_array($finishReason, ['MAX_TOKENS', 'LENGTH']);
            
            // Add truncation notice if needed
            if ($isTruncated) {
                $summary .= "\n\n---\n\n";
                $summary .= "⚠️ **Catatan:** Ringkasan ini terpotong karena melebihi batas maksimal token output (8192 tokens / ~6000 kata). ";
                $summary .= "Materi di atas sudah mencakup poin-poin utama yang berhasil diproses. ";
                $summary .= "Untuk mendapatkan ringkasan lengkap, pertimbangkan untuk:\n";
                $summary .= "- Membagi dokumen menjadi beberapa bagian\n";
                $summary .= "- Menggunakan instruksi yang lebih ringkas\n";
                $summary .= "- Memfokuskan pada topik tertentu saja";
            }

            return [
                'success' => true,
                'summary' => $summary,
                'model' => $this->model,
                'tokens_used' => $this->estimateTokens($prompt, $summary),
                'truncated' => $isTruncated,
                'finish_reason' => $finishReason,
            ];

        } catch (Exception $e) {
            Log::error('Gemini API Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Build prompt with HYBRID approach + STRONG user priority enforcement.
     * Even for long documents, user instructions must be followed exactly.
     */
    private function buildPrompt(string $content, array $instructions): string
    {
        $hasCustomInstructions = !empty($instructions) && 
                                 !empty(array_filter($instructions, fn($i) => !empty($i['text'] ?? '')));
        
        $prompt = "";
        
        // ========================================
        // CRITICAL: USER INSTRUCTIONS FIRST (Top Priority)
        // ========================================
        if ($hasCustomInstructions) {
            $prompt .= "⚠️ INSTRUKSI UTAMA DARI USER (PRIORITAS ABSOLUT - WAJIB DIIKUTI):\n\n";
            
            foreach ($instructions as $instruction) {
                if (!empty($instruction['text'])) {
                    $prompt .= $instruction['text'] . "\n\n";
                }
            }
            
            // EXPLICIT enforcement for common conflicts
            $userText = implode(' ', array_column($instructions, 'text'));
            $wantsParagraph = (stripos($userText, 'paragraf') !== false || stripos($userText, 'paragraph') !== false);
            $wantsNoBullets = (stripos($userText, 'tanpa poin') !== false || stripos($userText, 'tanpa bullet') !== false || stripos($userText, 'no bullet') !== false);
            $wantsTable = (stripos($userText, 'tabel') !== false || stripos($userText, 'table') !== false);
            
            if ($wantsTable) {
                $prompt .= "🚨 PENTING: User meminta FORMAT TABEL.\n";
                $prompt .= "   → WAJIB gunakan format Markdown table dengan | (pipe)\n";
                $prompt .= "   → Struktur: | Header 1 | Header 2 | Header 3 |\n";
                $prompt .= "   →          |----------|----------|----------|\n";
                $prompt .= "   →          | Data 1   | Data 2   | Data 3   |\n";
                $prompt .= "   → JANGAN gunakan bullet points atau paragraf untuk data yang diminta dalam tabel\n";
                $prompt .= "   → Setiap baris tabel harus memiliki informasi yang diminta user\n\n";
            } elseif ($wantsParagraph || $wantsNoBullets) {
                $prompt .= "🚨 PENTING: User meminta FORMAT PARAGRAF / TANPA POIN-POIN.\n";
                $prompt .= "   → WAJIB menulis dalam bentuk PARAGRAF penuh\n";
                $prompt .= "   → DILARANG KERAS menggunakan bullet points (-, *, •) atau numbering (1., 2., 3.)\n";
                $prompt .= "   → TIDAK PEDULI seberapa panjang dokumen, tetap gunakan paragraf\n";
                $prompt .= "   → Struktur: Heading (#) → Paragraf lengkap → Sub-heading (##) → Paragraf lengkap\n\n";
            }
            
            $prompt .= "💡 ATURAN PRIORITAS:\n";
            $prompt .= "   1. Instruksi user di atas adalah HUKUM yang tidak boleh dilanggar\n";
            $prompt .= "   2. Jika dokumen panjang, TETAP ikuti format yang diminta user\n";
            $prompt .= "   3. Jika ada konflik antara efisiensi vs instruksi user → pilih instruksi user\n";
            $prompt .= "   4. JANGAN gunakan format default AI jika user sudah spesifikasi format\n";
            $prompt .= "   5. KHUSUS untuk tabel: Abaikan semua aturan tentang bullet points, gunakan Markdown table\n\n";
            
        } else {
            // AUTO MODE: Default summarization
            $prompt .= "TUGAS: Buatkan ringkuman komprehensif dari dokumen berikut.\n\n";
        }
        
        // ========================================
        // DOCUMENT CONTENT
        // ========================================
        $prompt .= "DOKUMEN:\n```\n";
        $prompt .= $content;
        $prompt .= "\n```\n\n";
        
        // ========================================
        // MINIMAL GUARDRAILS (Always apply)
        // ========================================
        $prompt .= "ATURAN OUTPUT:\n";
        $prompt .= "1. Format HANYA Markdown standar: # ## ** * - ` | >\n";
        $prompt .= "2. DILARANG: HTML tags, inline CSS, external links\n";
        $prompt .= "3. Panjang maksimal: ~6000 kata atau 8000 tokens\n";
        $prompt .= "4. Jika terpotong: selesaikan paragraf/section terakhir dengan sempurna\n";
        
        if ($hasCustomInstructions) {
            $prompt .= "5. PRIORITAS TERTINGGI: Ikuti format yang diminta user di bagian INSTRUKSI UTAMA\n";
        }
        
        // ========================================
        // ANTI-BULLET-SPAM RULES (For long documents)
        // ========================================
        $prompt .= "\n🎯 ATURAN KHUSUS UNTUK DOKUMEN PANJANG:\n";
        $prompt .= "   → JANGAN buat poin-poin singkat/superfisial\n";
        $prompt .= "   → FOKUS pada materi yang benar-benar penting dan perlu dirangkum\n";
        $prompt .= "   → Setiap poin harus dijelaskan dengan SUBSTANSI (min 2-3 kalimat)\n";
        $prompt .= "   → Lebih baik: 5 poin mendalam daripada 20 poin dangkal\n";
        $prompt .= "   → Gunakan paragraf untuk menjelaskan konsep kompleks\n";
        $prompt .= "   → Skip detail minor jika tidak critical\n\n";
        
        $prompt .= "🚫 SKIP BAGIAN TIDAK PENTING:\n";
        $prompt .= "   → ABAIKAN: Cover, judul, daftar isi, kata pengantar, pendahuluan generik\n";
        $prompt .= "   → ABAIKAN: Profil penulis, daftar gambar/tabel, bibliografi di awal\n";
        $prompt .= "   → ABAIKAN: Tujuan pembelajaran, kompetensi dasar, atau metadata kurikulum\n";
        $prompt .= "   → ABAIKAN: Pertanyaan pemahaman konsep/latihan yang hanya berisi soal tanpa materi\n";
        $prompt .= "   → LANGSUNG mulai dari INTI MATERI (bab pertama yang berisi substansi)\n";
        $prompt .= "   → Identifikasi dimana materi substansial dimulai, lalu mulai rangkum dari sana\n\n";
        
        $prompt .= "📊 STRATEGI TOKEN LIMIT:\n";
        $prompt .= "   → Jika dokumen memiliki 10+ materi/bab dan mendekati batas token:\n";
        $prompt .= "   → JANGAN rangkum semua materi dengan poin singkat\n";
        $prompt .= "   → LEBIH BAIK: Rangkum hanya sampai materi ke-5 atau ke-6 dengan DETAIL LENGKAP\n";
        $prompt .= "   → Setiap materi harus mencakup SEMUA informasi penting dalam bentuk paragraf\n";
        $prompt .= "   → Prioritas: KUALITAS (detail lengkap) > KUANTITAS (merangkum semua)\n";
        $prompt .= "   → Jika hanya mampu cover 50% dokumen dengan detail → itu lebih baik daripada 100% tapi superfisial\n";
        $prompt .= "   → Akhiri dengan kalimat: '(Rangkuman mencakup materi 1-X dari Y materi total karena keterbatasan token)'\n";
        
        $prompt .= "\nOUTPUT:\n";
        
        return $prompt;
    }

    /**
     * Handle API error responses.
     *
     * @param \Illuminate\Http\Client\Response $response
     * @return array
     */
    private function handleError($response): array
    {
        $error = $response->json();
        $errorCode = $error['error']['code'] ?? 500;
        $errorMessage = $error['error']['message'] ?? 'Terjadi kesalahan pada API Gemini';

        // Log error for debugging
        Log::error('Gemini API Error', [
            'status' => $errorCode,
            'message' => $errorMessage,
            'model' => $this->model,
        ]);

        // Handle specific error codes
        if ($errorCode === 429) {
            // Check if it's quota exceeded or rate limit
            $isQuotaExceeded = stripos($errorMessage, 'quota') !== false || stripos($errorMessage, 'billing') !== false;
            
            if ($isQuotaExceeded) {
                return [
                    'success' => false,
                    'error' => '⚠️ API Quota Habis! API key Gemini sudah melebihi batas penggunaan. Solusi: 1) Tunggu reset quota (biasanya setiap hari), 2) Ganti API key baru, atau 3) Upgrade plan. Cek: https://ai.dev/usage',
                    'error_code' => 429,
                ];
            }
            
            return [
                'success' => false,
                'error' => '⏱️ Rate limit terlampaui. Terlalu banyak request dalam waktu singkat. Silakan tunggu 1-2 menit lalu coba lagi.',
                'error_code' => 429,
            ];
        }

        if ($errorCode === 404) {
            return [
                'success' => false,
                'error' => 'Model AI tidak tersedia. Silakan hubungi administrator.',
                'error_code' => 404,
            ];
        }

        if ($errorCode === 400) {
            return [
                'success' => false,
                'error' => 'Format permintaan tidak valid. Silakan coba lagi dengan dokumen lain.',
                'error_code' => 400,
            ];
        }

        return [
            'success' => false,
            'error' => $errorMessage,
            'error_code' => $errorCode,
        ];
    }

    /**
     * Estimate token usage (rough approximation).
     *
     * @param string $prompt
     * @param string $response
     * @return int
     */
    private function estimateTokens(string $prompt, string $response): int
    {
        // Rough estimation: 1 token ≈ 4 characters for English, ~2 for Indonesian
        $totalChars = strlen($prompt) + strlen($response);
        return (int) ceil($totalChars / 3);
    }

    /**
     * Test API connection.
     *
     * @return array
     */
    public function testConnection(): array
    {
        try {
            $result = $this->generateSummary('Ini adalah tes koneksi ke Gemini API.', []);
            
            if ($result['success']) {
                return [
                    'success' => true,
                    'message' => 'Koneksi ke Gemini API berhasil!',
                    'model' => $this->model,
                ];
            }
            
            return $result;
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
