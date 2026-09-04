<?php

namespace App\Models;

use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use LogsActivity;

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
                'created' => "Role '{$name}' was created",
                'updated' => "Role '{$name}' was updated",
                'deleted' => "Role '{$name}' was deleted",
                default   => "Role '{$name}' was {$eventName}",
            })
            ->useLogName('role');
    }
}
