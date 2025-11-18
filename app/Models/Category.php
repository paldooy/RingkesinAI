<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'icon',
        'color',
    ];

    /**
     * Get the user that owns the category.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the notes for the category.
     */
    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    /**
     * Get the notes count for the category.
     */
    public function getNotesCountAttribute()
    {
        return $this->notes()->count();
    }
}
