<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Message extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'body',
        'read',
        'attachment_path',
        'attachment_type',
        'attachment_name',
        'attachment_size',
    ];
    public function getActivitylogOptions(): LogOptions
    {
        $sender = $this->sender?->name ?? 'Unknown';
        $receiver = $this->receiver?->name ?? 'Unknown';
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => "Message from {$sender} to {$receiver} was sent",
                'updated' => "Message from {$sender} to {$receiver} was updated",
                'deleted' => "Message from {$sender} to {$receiver} was deleted",
                default   => "Message from {$sender} to {$receiver} was {$eventName}",
            })
            ->useLogName('message');
    }
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
