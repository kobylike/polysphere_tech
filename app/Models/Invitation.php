<?php
// app/Models/Invitation.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Permission\Models\Role;

class Invitation extends Model
{
    use HasFactory, LogsActivity;
    protected $fillable = [
        'email',
        'token',
        'role_id',
        'position',
        'expires_at',
        'accepted_at',
        'invited_by'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];
    public function getActivitylogOptions(): LogOptions
    {
        $email = $this->email ?? 'unknown email';
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => "Invitation sent to {$email}",
                'updated' => "Invitation for {$email} was updated",
                'deleted' => "Invitation for {$email} was deleted",
                default   => "Invitation for {$email} was {$eventName}",
            })
            ->useLogName('invitation');
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isAccepted(): bool
    {
        return $this->accepted_at !== null;
    }
}
