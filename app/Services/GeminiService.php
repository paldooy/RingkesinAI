<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class GeminiService
{
    private string $apiKey;
    private string $model;
    private string $baseUrl = 'https://generativelanguage.googleapis.com';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->model = config('services.gemini.model', 'gemini-2.0-flash-lite');
        
        if (empty($this->apiKey)) {
            throw new Exception('GOOGLE_API_KEY belum dikonfigurasi di file .env');
        }
    }

    /**
     * Generate summary from text using Gemini API.
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
        
        // Prepare API URL
        $url = "{$this->baseUrl}/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";
        
        try {
            $response = Http::timeout(60)->post($url, [
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
                    'maxOutputTokens' => 8192, // Increased from 2048 to 8192 (gemini-2.0-flash-lite max)
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
     * Build prompt with content and instructions.
     *
     * @param string $content
     * @param array $instructions
     * @return string
     */
    private function buildPrompt(string $content, array $instructions): string
    {
        $prompt = "Buatkan ringkasan dari teks berikut:\n\n";
        
        // Add instructions if provided
        if (!empty($instructions)) {
            $prompt .= "Instruksi khusus:\n";
            foreach ($instructions as $instruction) {
                if (isset($instruction['text']) && !empty($instruction['text'])) {
                    $prompt .= "- " . $instruction['text'] . "\n";
                }
            }
            $prompt .= "\n";
        }
        
        $prompt .= "===== ISI DOKUMEN =====\n\n";
        $prompt .= $content;
        $prompt .= "\n\n===== AKHIR DOKUMEN =====\n\n";
        
        $prompt .= "Buatkan ringkasan yang jelas, terstruktur, dan mudah dipahami.\n";
        $prompt .= "Gunakan format MARKDOWN MURNI untuk struktur:\n";
        $prompt .= "- Heading (# H1, ## H2, ### H3)\n";
        $prompt .= "- Bold (**teks tebal**) untuk penekanan penting\n";
        $prompt .= "- List (bullet: -, numbered: 1. 2. 3.)\n";
        $prompt .= "- Tabel markdown (| Header 1 | Header 2 | dengan separator |---|---|)\n";
        $prompt .= "- Code blocks dengan ```language untuk kode\n";
        $prompt .= "- Blockquote (> teks) untuk kutipan/catatan\n\n";
        $prompt .= "⚠️ ATURAN PENTING FORMAT:\n";
        $prompt .= "- HANYA gunakan syntax MARKDOWN standar\n";
        $prompt .= "- JANGAN gunakan HTML tags (<span>, <div>, <mark>, dll)\n";
        $prompt .= "- JANGAN gunakan inline CSS/styling (style=\"...\", background-color, dll)\n";
        $prompt .= "- JANGAN gunakan HTML attributes (class, id, style)\n";
        $prompt .= "- Output harus pure Markdown yang bisa di-parse oleh CommonMark\n\n";
        
        $prompt .= "ATURAN PANJANG:\n";
        $prompt .= "- Maksimal ~6000 kata (8000 tokens)\n";
        $prompt .= "- Jika dokumen terlalu panjang:\n";
        $prompt .= "  * Selesaikan bagian yang sedang dijelaskan sampai LENGKAP\n";
        $prompt .= "  * JANGAN potong di tengah kalimat/paragraf\n";
        $prompt .= "  * Prioritaskan poin terpenting di awal\n\n";
        
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
            return [
                'success' => false,
                'error' => 'Kuota API Gemini sudah habis. Silakan coba lagi nanti atau hubungi administrator.',
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
