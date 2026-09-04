<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Post extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

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

    public function getActivitylogOptions(): LogOptions
    {
        $title = $this->title ?? $this->slug ?? "ID: {$this->id}";
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => "Post '{$title}' was created",
                'updated' => "Post '{$title}' was updated",
                'deleted' => "Post '{$title}' was deleted",
                default   => "Post '{$title}' was {$eventName}",
            })
            ->useLogName('post');
    }
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



    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id')->orderBy('created_at', 'desc');
    }


    public function approvedComments()
    {
        return $this->comments()->where('approved', true);
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
}
