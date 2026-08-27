<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'featured_image',
        'status',
        'visibility',
        'allow_comments',
        'published_at',
        'custom_fields',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'author_id'
    ];

    protected $casts = [
        'custom_fields' => 'array',
        'allow_comments' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    // Helper: get status badge class
    // app/Models/Post.php
    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'published' => 'bg-success',
            'draft'     => 'bg-secondary',
            'pending'   => 'bg-warning',
            'private'   => 'bg-info',
            'trash'     => 'bg-danger',
            default     => 'bg-secondary',
        };
    }
}
