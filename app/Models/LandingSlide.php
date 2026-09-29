<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LandingSlide extends Model
{
    protected $fillable = ['tag', 'title', 'image', 'sort_order', 'is_active'];
    protected $casts    = ['is_active' => 'boolean'];

    public function getImageUrlAttribute(): string
    {
        return Str::startsWith($this->image, ['http://', 'https://', '//'])
            ? $this->image
            : asset($this->image);
    }

    public function scopeActive($q)  { return $q->where('is_active', true); }
    public function scopeOrdered($q) { return $q->orderBy('sort_order')->orderBy('id'); }
}