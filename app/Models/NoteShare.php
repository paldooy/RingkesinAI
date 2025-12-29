<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class NoteShare extends Model
{
    protected $fillable = [
        'note_id',
        'share_code',
        'expires_at',
        'view_count',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Get the note that owns this share.
     */
    public function note(): BelongsTo
    {
        return $this->belongsTo(Note::class);
    }

    /**
     * Check if the share code is expired.
     */
    public function isExpired(): bool
    {
        if ($this->expires_at === null) {
            return false;
        }

        return $this->expires_at->isPast();
    }

    /**
     * Increment the view count.
     */
    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    /**
     * Generate a unique share code.
     */
    public static function generateUniqueCode(): string
    {
        do {
            $code = strtoupper(Str::random(6));
        } while (self::where('share_code', $code)->exists());

        return $code;
    }

    /**
     * Find by share code and check if valid.
     */
    public static function findValidByCode(string $code): ?self
    {
        $share = self::with('note.category', 'note.tags')
            ->where('share_code', $code)
            ->first();

        if (!$share || $share->isExpired()) {
            return null;
        }

        return $share;
    }
}
