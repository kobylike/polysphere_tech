<?php

namespace App\Models;

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
        'status'
    ];

    protected $casts = [
        'additional_images' => 'array',
    ];
    public function getActivitylogOptions(): LogOptions
    {
        $name = $this->name ?? $this->slug ?? "ID: {$this->id}";
        return LogOptions::defaults()
            ->logAll()
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
    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function getFeaturedImageUrlAttribute()
    {
        return $this->featured_image ? asset('storage/' . $this->featured_image) : null;
    }
}
