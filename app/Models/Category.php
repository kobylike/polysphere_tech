<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Category extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = ['name', 'slug', 'parent_id', 'description', 'order'];
    public function getActivitylogOptions(): LogOptions
    {
        $name = $this->name ?? $this->slug ?? "ID: {$this->id}";
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => "Category '{$name}' was created",
                'updated' => "Category '{$name}' was updated",
                'deleted' => "Category '{$name}' was deleted",
                default   => "Category '{$name}' was {$eventName}",
            })
            ->useLogName('category');
    }
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }

    // Scope to order by `order` column
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }
}
