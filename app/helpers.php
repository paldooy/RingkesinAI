<?php

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;

if (!function_exists('markdown_to_html')) {
    /**
     * Convert Markdown to HTML.
     *
     * @param string $markdown
     * @return string
     */
    function markdown_to_html(string $markdown): string
    {
        // Check if content is already HTML (from TinyMCE)
        if (preg_match('/<[^>]+>/', $markdown)) {
            return $markdown; // Already HTML, return as-is
        }
        
        // Configure environment with GitHub Flavored Markdown support
        $environment = new Environment([
            'html_input' => 'allow', // Allow HTML tags (for TinyMCE content)
            'allow_unsafe_links' => false,
        ]);
        
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());
        
        $converter = new MarkdownConverter($environment);
        
        return $converter->convert($markdown)->getContent();
    }
}

if (!function_exists('format_note_content')) {
    /**
     * Format note content for display (convert markdown OR preserve HTML from TinyMCE).
     *
     * @param string $content
     * @param bool $isMarkdown
     * @return string
     */
    function format_note_content(string $content, bool $isMarkdown = true): string
    {
        if (empty($content)) {
            return '';
        }
        
        // Check if content is already HTML (from TinyMCE)
        if (preg_match('/<(p|div|table|h[1-6]|ul|ol|pre|blockquote)[^>]*>/', $content)) {
            return $content; // Already formatted HTML, return as-is
        }
        
        // Check if content likely contains markdown
        $hasMarkdown = preg_match('/(\*\*|__|##|\*|\-|\d+\.|```|\[.+\]\(.+\))/', $content);
        
        if ($hasMarkdown || $isMarkdown) {
            return markdown_to_html($content);
        }
        
        // Fallback: just preserve line breaks
        return nl2br(e($content));
    }
}

if (!function_exists('strip_mark_tags')) {
    /**
     * Remove <mark> tags (and their attributes) from a string while preserving inner text.
     * Useful to sanitize content before saving so highlights applied at render-time
     * don't end up persisted into the database.
     *
     * @param string $text
     * @return string
     */
    function strip_mark_tags(string $text): string
    {
        if ($text === '') return $text;

        // Remove opening <mark ...> tags
        $text = preg_replace('#<mark[^>]*>#i', '', $text);

        // Remove closing </mark> tags
        $text = preg_replace('#</mark>#i', '', $text);

        return $text;
    }
}

if (!function_exists('strip_markdown_syntax')) {
    /**
     * Remove markdown syntax from text to create plain text excerpt.
     * Useful for generating clean excerpts from markdown content.
     *
     * @param string $markdown
     * @param int $limit Character limit (0 = no limit)
     * @return string
     */
    function strip_markdown_syntax(string $markdown, int $limit = 0): string
    {
        if (empty($markdown)) {
            return '';
        }
        
        // First, check if content is already HTML
        if (preg_match('/<(p|div|table|h[1-6]|ul|ol|pre|blockquote)[^>]*>/', $markdown)) {
            // If HTML, just strip all tags
            $plainText = strip_tags($markdown);
        } else {
            // Process as markdown
            $plainText = $markdown;
            
            // Remove fenced code blocks (``` or ~~~)
            $plainText = preg_replace('/^```[\s\S]*?^```/m', '', $plainText);
            $plainText = preg_replace('/^~~~[\s\S]*?^~~~/m', '', $plainText);
            
            // Remove headers (#)
            $plainText = preg_replace('/^#{1,6}\s+(.*)$/m', '$1', $plainText);
            
            // Remove bold/italic (must do in specific order)
            $plainText = preg_replace('/\*\*\*(.+?)\*\*\*/s', '$1', $plainText); // Bold+italic
            $plainText = preg_replace('/___(.+?)___/s', '$1', $plainText); // Bold+italic
            $plainText = preg_replace('/\*\*(.+?)\*\*/s', '$1', $plainText); // Bold
            $plainText = preg_replace('/__(.+?)__/s', '$1', $plainText); // Bold
            $plainText = preg_replace('/\*(.+?)\*/s', '$1', $plainText); // Italic
            $plainText = preg_replace('/_(.+?)_/s', '$1', $plainText); // Italic
            
            // Remove inline code
            $plainText = preg_replace('/`([^`]+)`/', '$1', $plainText);
            
            // Remove links [text](url) -> text
            $plainText = preg_replace('/\[([^\]]+)\]\([^\)]+\)/', '$1', $plainText);
            
            // Remove images ![alt](url)
            $plainText = preg_replace('/!\[[^\]]*\]\([^\)]+\)/', '', $plainText);
            
            // Remove list markers
            $plainText = preg_replace('/^\s*[\*\-\+]\s+/m', '', $plainText);
            $plainText = preg_replace('/^\s*\d+\.\s+/m', '', $plainText);
            
            // Remove blockquotes
            $plainText = preg_replace('/^\s*>\s*/m', '', $plainText);
            
            // Remove horizontal rules
            $plainText = preg_replace('/^[\*\-_]{3,}\s*$/m', '', $plainText);
            
            // Remove any remaining HTML tags
            $plainText = strip_tags($plainText);
        }
        
        // Normalize whitespace
        $plainText = preg_replace('/\r\n|\r/', "\n", $plainText); // Normalize line endings
        $plainText = preg_replace('/\n{3,}/', "\n\n", $plainText); // Max 2 consecutive newlines
        $plainText = preg_replace('/[ \t]+/', ' ', $plainText); // Normalize spaces
        $plainText = preg_replace('/^\s+/m', '', $plainText); // Remove leading spaces on lines
        $plainText = trim($plainText);
        
        // Apply limit if specified
        if ($limit > 0 && mb_strlen($plainText) > $limit) {
            // Find a good break point (space or punctuation)
            $truncated = mb_substr($plainText, 0, $limit);
            $lastSpace = mb_strrpos($truncated, ' ');
            if ($lastSpace !== false && $lastSpace > $limit * 0.8) {
                $truncated = mb_substr($truncated, 0, $lastSpace);
            }
            return $truncated . '...';
        }
        
        return $plainText;
    }
}
