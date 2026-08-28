<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'featured_image',
        'additional_images',
        'thumbnail_image',
        'video_url',
        'video_file',
        'service_id',
        'status',
        'visibility',
        'published_at',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'custom_fields',
        'author_id',
        'start_year',
        'end_year',
        'client',
        'company',
    ];

    protected $casts = [
        'additional_images' => 'array',
        'custom_fields' => 'array',
        'published_at' => 'datetime',
    ];

    public function getVideoAttribute()
    {
        if ($this->video_file) {
            return asset('storage/' . $this->video_file);
        }
        return $this->video_url;
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

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

    public function getFeaturedImageUrlAttribute()
    {
        return $this->featured_image ? asset('storage/' . $this->featured_image) : null;
    }

    public function getThumbnailImageUrlAttribute()
    {
        return $this->thumbnail_image ? asset('storage/' . $this->thumbnail_image) : null;
    }
}
