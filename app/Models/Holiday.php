<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Holiday extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'date',
        'recurring',
    ];

    protected $casts = [
        'date'      => 'date',
        'recurring' => 'boolean',
    ];
    public function getActivitylogOptions(): LogOptions
    {
        $name = $this->name ?? $this->date ?? "ID: {$this->id}";
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => "Holiday '{$name}' was created",
                'updated' => "Holiday '{$name}' was updated",
                'deleted' => "Holiday '{$name}' was deleted",
                default   => "Holiday '{$name}' was {$eventName}",
            })
            ->useLogName('holiday');
    }
    /**
     * The date this holiday next falls on, relative to $from.
     * For non-recurring holidays this is just the stored date.
     * For recurring holidays, it's the same month/day, rolled
     * forward to the next occurrence >= $from.
     */
    public function nextOccurrenceFrom(Carbon $from): Carbon
    {
        if (!$this->recurring) {
            return $this->date->copy();
        }

        $next = Carbon::create($from->year, $this->date->month, $this->date->day);

        if ($next->lt($from->copy()->startOfDay())) {
            $next->addYear();
        }

        return $next;
    }

    /**
     * The concrete date (Y-m-d) this holiday falls on within a given
     * year/month, honoring recurrence. Returns null if it doesn't
     * apply to that year/month at all.
     */
    public function occursOn(int $year, int $month): ?string
    {
        if (!$this->recurring) {
            return ((int) $this->date->year === $year && (int) $this->date->month === $month)
                ? $this->date->toDateString()
                : null;
        }

        return ((int) $this->date->month === $month)
            ? Carbon::create($year, $month, $this->date->day)->toDateString()
            : null;
    }
}
