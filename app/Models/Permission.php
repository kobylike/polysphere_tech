<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    use HasFactory, LogsActivity;
    protected $fillable = [
        'name',
        'guard_name',
        'description',
        'color',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        $name = $this->name ?? "ID: {$this->id}";
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => "Permission '{$name}' was created",
                'updated' => "Permission '{$name}' was updated",
                'deleted' => "Permission '{$name}' was deleted",
                default   => "Permission '{$name}' was {$eventName}",
            })
            ->useLogName('permission');
    }
}
