<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class FaqCategory extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'display_order', 'status'];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
        static::updating(function ($category) {
            if ($category->isDirty('name') && !$category->isDirty('slug')) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(Faq::class)->orderBy('display_order');
    }

    public function activeFaqs(): HasMany
    {
        return $this->hasMany(Faq::class)->where('status', 'active')->orderBy('display_order');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('name');
    }
}
