<?php

namespace App\Models;

use App\Services\ChatKnowledgeBase;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Service extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'featured_image',
        'additional_images',
        'icon',
        'order',
        'status',
    ];

    protected $casts = [
        'additional_images' => 'array',
    ];

    // ─── Lifecycle ──────────────────────────────────────────

    protected static function booted(): void
    {
        // Chat bot knowledge base: bust cache on any change
        $bust = fn() => ChatKnowledgeBase::bust();

        static::saved($bust);
        static::deleted($bust);
    }

    // ─── Activity log ───────────────────────────────────────

    public function getActivitylogOptions(): LogOptions
    {
        $name = $this->name ?? $this->slug ?? "ID: {$this->id}";

        return LogOptions::defaults()
            ->logOnly([
                'name',
                'slug',
                'icon',
                'order',
                'status',
            ])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => "Service '{$name}' was created",
                'updated' => "Service '{$name}' was updated",
                'deleted' => "Service '{$name}' was deleted",
                default   => "Service '{$name}' was {$eventName}",
            })
            ->useLogName('service');
    }

    // ─── Relationships ──────────────────────────────────────

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    // ─── Accessors ──────────────────────────────────────────

    public function getFeaturedImageUrlAttribute()
    {
        return $this->featured_image ? asset('storage/' . $this->featured_image) : null;
    }
}
