<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Holiday extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'date',
        'recurring',
    ];

    protected $casts = [
        'date'      => 'date',
        'recurring' => 'boolean',
    ];

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
