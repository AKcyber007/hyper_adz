<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Blog extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'featured_image',
        'short_description',
        'content',
        'author_name',
        'status',
        'publish_date',
        'is_featured',
        'seo_title',
        'seo_description'
    ];

    protected $casts = [
        'publish_date' => 'datetime',
        'is_featured' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($blog) {
            if (empty($blog->slug)) {
                $blog->slug = Str::slug($blog->title);
            }
        });

        static::updating(function ($blog) {
            if ($blog->isDirty('title') && !$blog->isDirty('slug')) {
                $blog->slug = Str::slug($blog->title);
            }
        });

        // Enforce single featured blog rule
        static::saving(function ($blog) {
            if ($blog->is_featured) {
                // Set is_featured to false for all other blogs
                static::where('id', '!=', $blog->id)
                    ->where('is_featured', true)
                    ->update(['is_featured' => false]);
            }
        });
    }

    /**
     * Estimated Reading Time Accessor (assuming 200 words per minute).
     */
    public function getReadingTimeAttribute(): string
    {
        $wordCount = str_word_count(strip_tags($this->content));
        $minutes = ceil($wordCount / 200);
        return $minutes . ' min read';
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('publish_date')
                  ->orWhere('publish_date', '<=', now());
            });
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeLatestFirst($query)
    {
        return $query->orderByRaw('COALESCE(publish_date, created_at) DESC');
    }
}
