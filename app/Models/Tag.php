<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Tag extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = ['name', 'slug'];
    public function getActivitylogOptions(): LogOptions
    {
        $name = $this->name ?? $this->slug ?? "ID: {$this->id}";
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => "Tag '{$name}' was created",
                'updated' => "Tag '{$name}' was updated",
                'deleted' => "Tag '{$name}' was deleted",
                default   => "Tag '{$name}' was {$eventName}",
            })
            ->useLogName('tag');
    }
    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }
}
