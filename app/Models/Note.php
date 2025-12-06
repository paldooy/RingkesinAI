<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'content',
        'excerpt',
        'is_favorite',
    ];

    /**
     * Get the user that owns the note.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category that owns the note.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * The tags that belong to the note.
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Automatically generate excerpt from content.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($note) {
            if (empty($note->excerpt)) {
                // Use helper to clean markdown and create excerpt
                $note->excerpt = strip_markdown_syntax($note->content, 200);
            }
        });
    }

    /**
     * Get the excerpt attribute.
     * Generate excerpt on the fly if not stored in database.
     */
    public function getExcerptAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }
        
        // Use helper to clean markdown and create excerpt
        return strip_markdown_syntax($this->content, 200);
    }
}
