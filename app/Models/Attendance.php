<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendance';

    protected $fillable = [
        'user_id',
        'date',
        'status',
        'check_in',
        'check_out',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ─── Scopes ────────────────────────────────────────────────────

    public function scopeForMonth($query, $year, $month)
    {
        return $query->whereYear('date', $year)->whereMonth('date', $month);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('date', today());
    }

    // ─── Status source of truth ────────────────────────────────────

    public static function statusOptions(): array
    {
        return [
            'present' => 'Present',
            'absent'  => 'Absent',
            'leave'   => 'Leave',
            'holiday' => 'Holiday',
        ];
    }

    public static function displayFor(?string $status, bool $isHoliday = false): array
    {
        $normalized = $status ? strtolower(trim($status)) : null;

        return match (true) {
            $normalized === 'present' => [
                'icon'  => 'fa-solid fa-check',
                'class' => 'text-success',
                'label' => 'Present',
            ],
            $normalized === 'absent' => [
                'icon'  => 'fa-regular fa-xmark',
                'class' => 'text-danger',
                'label' => 'Absent',
            ],
            $normalized === 'leave' => [
                'icon'  => 'fa-regular fa-clock',
                'class' => 'text-warning',
                'label' => 'Leave',
            ],
            $normalized === 'holiday' => [
                'icon'  => 'fa-regular fa-sun',
                'class' => 'text-warning',
                'label' => 'Holiday',
            ],
            $isHoliday => [
                'icon'  => 'fa-regular fa-star',
                'class' => 'text-info',
                'label' => 'Company Holiday',
            ],
            default => [
                'icon'  => null,
                'class' => 'text-muted',
                'label' => 'Unmarked',
            ],
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return self::displayFor($this->status)['class'] === 'text-success' ? 'success'
            : (self::displayFor($this->status)['class'] === 'text-danger' ? 'danger'
                : (self::displayFor($this->status)['class'] === 'text-warning' ? 'warning' : 'secondary'));
    }

    public function getStatusIconAttribute(): string
    {
        return self::displayFor($this->status)['icon'] ?? 'fa-question';
    }

    public function getMinutesWorkedAttribute(): ?int
    {
        if (! $this->check_in || ! $this->check_out) {
            return null;
        }
        $in  = Carbon::parse($this->check_in);
        $out = Carbon::parse($this->check_out);
        return $out->diffInMinutes($in);
    }
}
