<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;


class Project extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

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
    public function getActivitylogOptions(): LogOptions
    {
        $title = $this->title ?? $this->slug ?? "ID: {$this->id}";
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => "Project '{$title}' was created",
                'updated' => "Project '{$title}' was updated",
                'deleted' => "Project '{$title}' was deleted",
                default   => "Project '{$title}' was {$eventName}",
            })
            ->useLogName('project');
    }
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
