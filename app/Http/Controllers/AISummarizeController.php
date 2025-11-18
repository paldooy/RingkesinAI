<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Category;
use App\Services\FileExtractorService;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Exception;

class AISummarizeController extends Controller
{
    protected FileExtractorService $fileExtractor;
    protected GeminiService $gemini;

    public function __construct(FileExtractorService $fileExtractor, GeminiService $gemini)
    {
        $this->fileExtractor = $fileExtractor;
        $this->gemini = $gemini;
    }

    /**
     * Display the AI Summarize page.
     */
    public function index()
    {
        $categories = Auth::user()->categories;
        
        return view('summarize.index', compact('categories'));
    }

    /**
     * Generate summary from uploaded file using Gemini API.
     */
    public function generate(Request $request)
    {
        // Rate limiting: 5 requests per minute per user
        $key = 'summarize:' . Auth::id();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'success' => false,
                'error' => "Terlalu banyak permintaan. Silakan coba lagi dalam {$seconds} detik.",
            ], 429);
        }

        RateLimiter::hit($key, 60); // 60 seconds window

        // Validation
        $request->validate([
            'document' => 'required|file|mimes:pdf,doc,docx,txt|max:10240', // 10MB max
            'instructions' => 'nullable|string', // Changed from array to string (JSON)
        ]);

        try {
            $file = $request->file('document');
            $fileName = $file->getClientOriginalName();
            
            // Store the uploaded file
            $path = $file->store('documents', 'public');

            // Extract text from file
            $fileContent = $this->fileExtractor->extractText($file);
            
            if (empty($fileContent)) {
                throw new Exception('File kosong atau tidak dapat dibaca. Pastikan file berisi teks yang valid.');
            }

            // Limit content length to prevent token overflow (max ~30K chars ≈ 10K tokens)
            if (strlen($fileContent) > 30000) {
                $fileContent = substr($fileContent, 0, 30000) . "\n\n[... Teks dipotong karena terlalu panjang ...]";
            }

            // Parse instructions from JSON string to array
            $instructions = [];
            if ($request->has('instructions') && !empty($request->input('instructions'))) {
                $instructionsInput = $request->input('instructions');
                
                // If it's a JSON string, decode it
                if (is_string($instructionsInput)) {
                    $decoded = json_decode($instructionsInput, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $instructions = $decoded;
                    }
                } elseif (is_array($instructionsInput)) {
                    $instructions = $instructionsInput;
                }
            }

            // Generate cache key based on file hash and instructions
            $cacheKey = 'summary:' . md5($fileContent . json_encode($instructions));
            
            // Check cache first (valid for 1 hour)
            $cachedSummary = Cache::get($cacheKey);
            if ($cachedSummary) {
                session([
                    'ai_summary' => $cachedSummary['summary'],
                    'ai_file_name' => $fileName,
                    'ai_file_path' => $path,
                    'ai_original_content' => $fileContent, // Store for revisions
                ]);

                return response()->json([
                    'success' => true,
                    'summary' => $cachedSummary['summary'], // Send raw markdown
                    'fileName' => $fileName,
                    'original_content' => $fileContent, // Send to frontend for revisions
                    'metadata' => [
                        'model' => $cachedSummary['model'] ?? 'gemini-2.0-flash-lite',
                        'tokens_used' => $cachedSummary['tokens_used'] ?? 0,
                        'cached' => true,
                        'truncated' => $cachedSummary['truncated'] ?? false,
                        'finish_reason' => $cachedSummary['finish_reason'] ?? 'STOP',
                    ],
                ]);
            }

            // Call Gemini API to generate summary
            $result = $this->gemini->generateSummary($fileContent, $instructions);

            if (!$result['success']) {
                throw new Exception($result['error'] ?? 'Gagal menghasilkan ringkasan');
            }

            $summary = $result['summary'];
            
            // Sanitize: Remove any HTML tags/styling that Gemini might have added
            // Keep only plain markdown
            $summary = $this->sanitizeAIOutput($summary);

            // Cache the result for 1 hour
            Cache::put($cacheKey, [
                'summary' => $summary,
                'model' => $result['model'],
                'tokens_used' => $result['tokens_used'] ?? 0,
                'truncated' => $result['truncated'] ?? false,
                'finish_reason' => $result['finish_reason'] ?? 'STOP',
            ], 3600);

            // Store summary and original content in session (for revisions)
            session([
                'ai_summary' => $summary,
                'ai_file_name' => $fileName,
                'ai_file_path' => $path,
                'ai_original_content' => $fileContent, // Store for revisions
            ]);

            return response()->json([
                'success' => true,
                'summary' => $summary, // Send raw markdown
                'fileName' => $fileName,
                'model' => $result['model'],
                'tokens_used' => $result['tokens_used'] ?? 0,
                'cached' => false,
                'truncated' => $result['truncated'] ?? false,
                'finish_reason' => $result['finish_reason'] ?? 'STOP',
                'original_content' => $fileContent, // Send to frontend for revisions
                'metadata' => [
                    'model' => $result['model'],
                    'tokens_used' => $result['tokens_used'] ?? 0,
                    'cached' => false,
                    'truncated' => $result['truncated'] ?? false,
                    'finish_reason' => $result['finish_reason'] ?? 'STOP',
                ],
            ]);

        } catch (Exception $e) {
            // Clean up uploaded file on error
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Save the generated summary as a note.
     */
    public function save(Request $request)
    {
        $request->validate([
            'summary' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'title' => 'nullable|string|max:255',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
        ]);

        $fileName = session('ai_file_name', 'Dokumen');
        $title = $request->input('title', "Ringkasan: {$fileName}");

        // Sanitize summary to avoid persisting any <mark> highlights or inline markup
        $rawSummary = $request->input('summary');
        
        // Log content size for debugging
        $contentLength = strlen($rawSummary);
        Log::info('Saving AI summary', [
            'user_id' => Auth::id(),
            'title' => $title,
            'content_length' => $contentLength,
            'has_html' => preg_match('/<[^>]+>/', $rawSummary) ? 'yes' : 'no',
            'tags_count' => count($request->input('tags', [])),
        ]);
        
        // Apply sanitization
        $rawSummary = $this->sanitizeAIOutput($rawSummary);
        
        // Log after sanitization
        $cleanLength = strlen($rawSummary);
        if ($contentLength !== $cleanLength) {
            Log::info('Content sanitized', [
                'before' => $contentLength,
                'after' => $cleanLength,
                'removed' => $contentLength - $cleanLength,
            ]);
        }

        // Create the note without relying on a missing 'notes' relation on the User model.
        $note = new Note([
            'title' => $title,
            'content' => $rawSummary,
            'category_id' => $request->input('category_id'),
        ]);
        $note->user_id = Auth::id();
        $note->save();

        // Attach tags if provided
        if ($request->has('tags') && is_array($request->input('tags'))) {
            $tagIds = [];
            foreach ($request->input('tags') as $tagName) {
                $tag = \App\Models\Tag::firstOrCreate(
                    ['name' => trim($tagName)],
                    ['user_id' => Auth::id()]
                );
                $tagIds[] = $tag->id;
            }
            $note->tags()->sync($tagIds);
        }

        // Clear session
        session()->forget(['ai_summary', 'ai_file_name', 'ai_file_path']);

        return response()->json([
            'success' => true,
            'message' => 'Ringkasan berhasil disimpan ke catatan!',
            'note_id' => $note->id,
        ]);
    }

    /**
     * Revise existing summary with new instructions (before saving).
     */
    public function revise(Request $request)
    {
        // Rate limiting
        $key = 'summarize:' . Auth::id();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'success' => false,
                'error' => "Terlalu banyak permintaan. Silakan coba lagi dalam {$seconds} detik.",
            ], 429);
        }

        $request->validate([
            'content' => 'required|string',
            'instructions' => 'required|array',
            'instructions.*' => 'string',
        ]);

        RateLimiter::hit($key, 60);

        try {
            $content = $request->input('content');
            $instructions = $request->input('instructions');

            // Limit content length
            if (strlen($content) > 30000) {
                $content = substr($content, 0, 30000);
            }

            // Check cache
            $cacheKey = 'summary:' . md5($content . json_encode($instructions));
            
            if (Cache::has($cacheKey)) {
                $cachedData = Cache::get($cacheKey);
                
                return response()->json([
                    'success' => true,
                    'summary' => $cachedData['summary'],
                    'metadata' => [
                        'model' => $cachedData['model'] ?? 'gemini-2.0-flash-lite',
                        'tokens_used' => $cachedData['tokens_used'] ?? 0,
                        'cached' => true,
                        'truncated' => $cachedData['truncated'] ?? false,
                        'finish_reason' => $cachedData['finish_reason'] ?? 'STOP',
                    ],
                ]);
            }

            // Generate new summary
            $result = $this->gemini->generateSummary($content, $instructions);

            if (!$result['success']) {
                throw new Exception($result['error'] ?? 'Gagal menghasilkan ringkasan');
            }

            // Sanitize AI output to remove any HTML/styling
            $result['summary'] = $this->sanitizeAIOutput($result['summary']);

            // Cache the result
            Cache::put($cacheKey, [
                'summary' => $result['summary'],
                'model' => $result['model'],
                'tokens_used' => $result['tokens_used'] ?? 0,
                'truncated' => $result['truncated'] ?? false,
                'finish_reason' => $result['finish_reason'] ?? 'STOP',
            ], 3600);

            return response()->json([
                'success' => true,
                'summary' => $result['summary'],
                'metadata' => [
                    'model' => $result['model'],
                    'tokens_used' => $result['tokens_used'] ?? 0,
                    'cached' => false,
                    'truncated' => $result['truncated'] ?? false,
                    'finish_reason' => $result['finish_reason'] ?? 'STOP',
                ],
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Regenerate summary with new instructions for existing note.
     */
    public function regenerate(Request $request)
    {
        // Rate limiting: 5 requests per minute per user
        $key = 'summarize:' . Auth::id();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'success' => false,
                'error' => "Terlalu banyak permintaan. Silakan coba lagi dalam {$seconds} detik.",
            ], 429);
        }

        $request->validate([
            'content' => 'required|string',
            'instructions' => 'required|string',
            'note_id' => 'required|exists:notes,id',
        ]);

        RateLimiter::hit($key, 60); // 60 seconds decay

        try {
            $content = $request->input('content');
            $instructionsJson = $request->input('instructions');
            $noteId = $request->input('note_id');
            
            // Verify note ownership
            $note = Note::where('id', $noteId)
                ->where('user_id', Auth::id())
                ->firstOrFail();
            
            // Parse instructions
            $instructions = json_decode($instructionsJson, true);
            if (!is_array($instructions)) {
                $instructions = [$instructionsJson];
            }

            // Check cache first
            $cacheKey = 'gemini:' . md5($content . json_encode($instructions));
            
            if (Cache::has($cacheKey)) {
                $cachedData = Cache::get($cacheKey);

                // Sanitize cached summary before persisting to note
                $toSave = $cachedData['summary'];
                if (function_exists('strip_mark_tags')) {
                    $toSave = strip_mark_tags($toSave);
                }

                // Update note with cached summary
                $note->content = $toSave;
                $note->save();

                return response()->json([
                    'success' => true,
                    'summary' => $cachedData['summary'],
                    'metadata' => array_merge($cachedData['metadata'], [
                        'cached' => true,
                    ]),
                ]);
            }

            // Limit content length
            if (strlen($content) > 30000) {
                $content = substr($content, 0, 30000);
            }

            // Generate new summary with Gemini
            $result = $this->gemini->generateSummary($content, $instructions);

            // Sanitize AI output to remove any HTML/styling
            $result['summary'] = $this->sanitizeAIOutput($result['summary']);

            // Cache the result for 1 hour
            Cache::put($cacheKey, [
                'summary' => $result['summary'],
                'metadata' => [
                    'model' => $result['model'],
                    'tokens_used' => $result['tokens_used'] ?? 0,
                    'truncated' => $result['truncated'] ?? false,
                    'finish_reason' => $result['finish_reason'] ?? 'STOP',
                ],
            ], 3600);

            // Use sanitized summary for saving
            $toSave = $result['summary'];

            // Update note with new summary (raw markdown, sanitized)
            $note->content = $toSave;
            $note->save();

            return response()->json([
                'success' => true,
                'summary' => $result['summary'],
                'metadata' => [
                    'model' => $result['model'],
                    'tokens_used' => $result['tokens_used'] ?? 0,
                    'cached' => false,
                    'truncated' => $result['truncated'] ?? false,
                    'finish_reason' => $result['finish_reason'] ?? 'STOP',
                ],
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Sanitize AI output to ensure clean markdown without HTML styling.
     * Removes inline HTML tags, style attributes, and other non-markdown elements.
     * Optimized for large content (handles long summaries without timeout).
     *
     * @param string $text
     * @return string
     */
    private function sanitizeAIOutput(string $text): string
    {
        if (empty($text)) {
            return $text;
        }

        // Increase PCRE limits for large content
        ini_set('pcre.backtrack_limit', '5000000');
        ini_set('pcre.recursion_limit', '5000000');

        try {
            // Step 1: Remove HTML comments first (simple, fast)
            $text = preg_replace('/<!--.*?-->/s', '', $text);
            
            // Step 2: Remove <mark> tags (can appear frequently)
            if (function_exists('strip_mark_tags')) {
                $text = strip_mark_tags($text);
            } else {
                $text = preg_replace('#</?mark[^>]*>#i', '', $text);
            }
            
            // Step 3: Remove inline styles in opening tags (most problematic for display)
            // Use non-greedy match and limit lookbehind
            $text = preg_replace('/<([a-z][a-z0-9]*)\b[^>]*\bstyle\s*=\s*["\'][^"\']*["\'][^>]*>(.*?)<\/\1>/is', '$2', $text);
            
            // Step 4: Remove remaining style/class/id attributes on any remaining tags
            $text = preg_replace('/\s+(style|class|id)\s*=\s*["\'][^"\']*["\']/i', '', $text);
            $text = preg_replace('/\s+(style|class|id)\s*=\s*[^\s>]+/i', '', $text);
            
            // Step 5: Strip all HTML tags except markdown-safe ones
            // Allow: br, hr, code, pre (these won't break markdown rendering)
            $text = strip_tags($text, '<br><hr><code><pre>');
            
            // Step 6: Normalize whitespace
            // Remove excessive blank lines (more than 3 consecutive)
            $text = preg_replace('/\n{4,}/', "\n\n\n", $text);
            
            // Remove trailing spaces on each line
            $text = preg_replace('/[ \t]+$/m', '', $text);
            
            return trim($text);
            
        } catch (\Exception $e) {
            // If regex fails (e.g., PCRE limit exceeded), fall back to basic strip_tags
            Log::warning('Sanitize AI output failed with regex, using fallback', [
                'error' => $e->getMessage(),
                'text_length' => strlen($text)
            ]);
            
            // Fallback: aggressive strip all tags
            $cleaned = strip_tags($text);
            return trim($cleaned);
        }
    }
}
