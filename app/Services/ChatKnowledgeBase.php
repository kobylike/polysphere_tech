<?php

namespace App\Services;

use App\Enums\VacancyStatus;
use App\Models\Project;
use App\Models\Service;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ChatKnowledgeBase
{
    public const CACHE_KEY = 'chat_widget.knowledge_base';

    public const CACHE_TTL_MINUTES = 5;

    protected const MAX_VACANCIES = 25;
    protected const MAX_TEAM      = 25;
    protected const MAX_PROJECTS  = 25;
    protected const MAX_SERVICES  = 30;

    protected function baseUrl(): string
    {
        return rtrim(config('app.url'), '/');
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Public API                                                 */
    /* ──────────────────────────────────────────────────────────── */

    public function build(): string
    {
        return Cache::remember(
            self::CACHE_KEY,
            now()->addMinutes(self::CACHE_TTL_MINUTES),
            fn() => $this->buildFresh()
        );
    }

    public static function bust(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    protected function buildFresh(): string
    {
        return implode("\n\n", array_filter([
            $this->sectionCompanyFacts(),
            $this->sectionServices(),
            $this->sectionProjects(),
            $this->sectionTeam(),
            $this->sectionVacancies(),
        ]));
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Sections                                                   */
    /* ──────────────────────────────────────────────────────────── */

    protected function sectionCompanyFacts(): string
    {
        $base = $this->baseUrl();

        return <<<TXT
        ═══════════════════════════
        COMPANY FACTS (static)
        ═══════════════════════════
        - Name: Polysphere Tech
        - Base: Accra, Ghana
        - Phone: +233 (59) 756-3427
        - General email: contact@polyspheretech.com
        - Careers email: careers@polyspheretech.com
        - Homepage: {$base}
        - Focus: custom software development, SaaS engineering, digital
          transformation, IT consulting.
        TXT;
    }

    protected function sectionServices(): string
    {
        $services = Service::query()
            ->where('status', 'active')
            ->orderBy('order')
            ->orderBy('name')
            ->limit(self::MAX_SERVICES)
            ->get(['id', 'name', 'slug', 'description']);

        if ($services->isEmpty()) {
            return '';
        }

        $base  = $this->baseUrl();
        $lines = $services->map(function (Service $s) use ($base) {
            $bits = ['Name: ' . $this->str($s->name)];

            if ($slug = $this->str($s->slug)) {
                $bits[] = "URL: {$base}/services/{$slug}";
            }

            if ($desc = $this->str($s->description)) {
                $bits[] = 'Summary: ' . $this->truncate($desc, 200);
            }

            return '- ' . implode(' | ', $bits);
        })->implode("\n");

        return sprintf(
            "═══ LIVE SERVICES OFFERED (%d) ═══\n%s",
            $services->count(),
            $lines
        );
    }

    protected function sectionProjects(): string
    {
        $projects = Project::query()
            ->with('service:id,name')
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->orderByDesc('published_at')
            ->limit(self::MAX_PROJECTS)
            ->get([
                'id',
                'title',
                'slug',
                'excerpt',
                'content',
                'service_id',
                'client',
                'company',
                'location',
                'start_year',
                'end_year',
                'published_at',
            ]);

        if ($projects->isEmpty()) {
            return "═══ LIVE PROJECTS ═══\nNo published case studies yet.";
        }

        $base  = $this->baseUrl();
        $lines = $projects->map(function (Project $p) use ($base) {
            $bits = ['Title: ' . $this->str($p->title)];

            if ($p->service?->name) {
                $bits[] = 'Service: ' . $this->str($p->service->name);
            }

            if ($client = $this->str($p->client)) {
                $bits[] = "Client: {$client}";
            } elseif ($company = $this->str($p->company)) {
                $bits[] = "Company: {$company}";
            }

            if ($loc = $this->str($p->location)) {
                $bits[] = "Location: {$loc}";
            }

            if ($year = $this->yearRange($p->start_year ?? null, $p->end_year ?? null)) {
                $bits[] = "Year: {$year}";
            }

            if ($slug = $this->str($p->slug)) {
                $bits[] = "URL: {$base}/projects/{$slug}";
            }

            $summary = $this->str($p->excerpt)
                ?: $this->truncate($this->str($p->content), 200);

            if ($summary) {
                $bits[] = 'Summary: ' . $summary;
            }

            return '- ' . implode(' | ', $bits);
        })->implode("\n");

        return sprintf(
            "═══ LIVE PROJECTS (%d case studies) ═══\n%s",
            $projects->count(),
            $lines
        );
    }

    protected function sectionTeam(): string
    {
        $members = User::query()
            ->whereHas('profile', fn($q) => $q->where('is_featured_team', true))
            ->with([
                'profile' => function ($q) {
                    $q->select([
                        'id',
                        'user_id',
                        'position',
                        'about_me',
                        'skills',
                        'display_order',
                        'is_featured_team',
                        'department_id',
                    ]);
                },
                'profile.department:id,name',
            ])
            ->join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
            ->orderBy('user_profiles.display_order')
            ->limit(self::MAX_TEAM)
            ->select('users.id', 'users.name', 'users.username', 'users.avatar')
            ->get();

        if ($members->isEmpty()) {
            return '';
        }

        $base  = $this->baseUrl();
        $lines = $members->map(function (User $u) use ($base) {
            $bits = ['Name: ' . $this->str($u->name)];

            if ($pos = $this->str($u->profile?->position)) {
                $bits[] = "Role: {$pos}";
            }

            if ($dept = $this->str($u->profile?->department?->name)) {
                $bits[] = "Dept: {$dept}";
            }

            if ($skills = $this->skillsToString($u->profile?->skills ?? null)) {
                $bits[] = "Skills: {$skills}";
            }

            if ($bio = $this->str($u->profile?->about_me)) {
                $bits[] = 'Bio: ' . $this->truncate($bio, 180);
            }

            if ($username = $this->str($u->username)) {
                $bits[] = "Profile: {$base}/team/{$username}";
            }

            return '- ' . implode(' | ', $bits);
        })->implode("\n");

        return sprintf(
            "═══ LIVE TEAM (%d featured members) ═══\n%s",
            $members->count(),
            $lines
        );
    }

    protected function sectionVacancies(): string
    {
        $vacancies = Vacancy::query()
            ->with('department:id,name')
            ->where('status', VacancyStatus::Published->value)
            ->where(function ($q) {
                $q->whereNull('closing_date')
                    ->orWhere('closing_date', '>=', now());
            })
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->limit(self::MAX_VACANCIES)
            ->get([
                'id',
                'title',
                'slug',
                'department_id',
                'summary',
                'location',
                'country',
                'employment_type',
                'experience_level',
                'workplace_type',
                'salary_min',
                'salary_max',
                'salary_currency',
                'is_salary_visible',
                'closing_date',
                'is_featured',
                'published_at',
            ]);

        if ($vacancies->isEmpty()) {
            return "═══ LIVE JOB VACANCIES ═══\nThere are currently NO open positions at Polysphere Tech.";
        }

        $base  = $this->baseUrl();
        $lines = $vacancies->map(function (Vacancy $v) use ($base) {
            $bits = ['Title: ' . $this->str($v->title)];

            if ($dept = $this->str($v->department?->name)) {
                $bits[] = "Dept: {$dept}";
            }

            if ($loc = $this->vacancyLocation($v)) {
                $bits[] = "Location: {$loc}";
            }

            if ($et = $this->enumLabel($v->employment_type)) {
                $bits[] = "Type: {$et}";
            }

            if ($lvl = $this->enumLabel($v->experience_level)) {
                $bits[] = "Level: {$lvl}";
            }

            if ($wt = $this->enumLabel($v->workplace_type)) {
                $bits[] = "Workplace: {$wt}";
            }

            if ($salary = $this->salaryText($v)) {
                $bits[] = "Salary: {$salary}";
            }

            if ($v->closing_date) {
                $bits[] = 'Closes: ' . Carbon::parse($v->closing_date)->format('M j, Y');
            }

            if (! empty($v->is_featured)) {
                $bits[] = 'FEATURED';
            }

            $slug = $this->str($v->slug);
            $bits[] = 'URL: ' . ($slug ? "{$base}/careers/{$slug}" : "{$base}/careers");

            return '- ' . implode(' | ', $bits);
        })->implode("\n");

        return sprintf(
            "═══ LIVE JOB VACANCIES (%d open) ═══\n%s",
            $vacancies->count(),
            $lines
        );
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Normalisers                                                */
    /* ──────────────────────────────────────────────────────────── */

    /**
     * Coerce anything (string, number, array, object, null, enum) to a
     * clean string. Arrays are flattened to space-joined text. Never
     * throws on non-strings.
     */
    protected function str(mixed $v): string
    {
        if ($v === null) {
            return '';
        }

        if (is_string($v)) {
            return trim($v);
        }

        if (is_numeric($v) || is_bool($v)) {
            return (string) $v;
        }

        if (is_array($v)) {
            $flat = [];
            array_walk_recursive($v, function ($item) use (&$flat) {
                if (is_scalar($item)) {
                    $s = trim((string) $item);
                    if ($s !== '') $flat[] = $s;
                }
            });
            return implode(' ', $flat);
        }

        if (is_object($v)) {
            if (method_exists($v, '__toString')) {
                return trim((string) $v);
            }
            if ($v instanceof \BackedEnum) {
                return (string) $v->value;
            }
            if ($v instanceof \UnitEnum) {
                return $v->name;
            }
            return '';
        }

        return '';
    }

    /**
     * Collapse all whitespace runs into single spaces and trim.
     */
    protected function oneLine(string $text): string
    {
        $text = preg_replace('/\s+/', ' ', $text) ?? '';
        return trim($text);
    }

    /**
     * Word-safe truncation. Cuts at the last complete word within
     * $chars, strips tags, collapses whitespace, appends "…".
     */
    protected function truncate(string $text, int $chars = 200): string
    {
        $text = strip_tags($text);
        $text = preg_replace('/\s+/', ' ', $text) ?? '';
        $text = trim($text);

        if (mb_strlen($text) <= $chars) {
            return $text;
        }

        $cut       = mb_substr($text, 0, $chars);
        $lastSpace = mb_strrpos($cut, ' ');

        if ($lastSpace !== false && $lastSpace > $chars * 0.6) {
            $cut = mb_substr($cut, 0, $lastSpace);
        }

        return rtrim($cut, " ,.;:!?-") . '…';
    }

    /**
     * Human label for a BackedEnum. Prefers ->label() if defined,
     * otherwise returns the enum value, otherwise string-casts.
     */
    protected function enumLabel(mixed $enum): string
    {
        if ($enum === null || $enum === '') {
            return '';
        }
        if (is_object($enum) && method_exists($enum, 'label')) {
            return $this->str($enum->label());
        }
        if ($enum instanceof \BackedEnum) {
            return (string) $enum->value;
        }
        if (is_object($enum) && property_exists($enum, 'value')) {
            return $this->str($enum->value);
        }
        return $this->str($enum);
    }

    protected function yearRange($start, $end): ?string
    {
        $start = $start ? (int) $start : null;
        $end   = $end ? (int) $end : null;

        if ($start && $end && $start !== $end) return "{$start}–{$end}";
        if ($start && $end)                  return (string) $start;
        if ($start)                          return "{$start}–present";
        if ($end)                            return (string) $end;
        return null;
    }

    /**
     * Extract skill names from whatever shape the column is in.
     * Handles:
     *   - ["PHP", "Laravel"]
     *   - [{"name": "PHP", "level": 90}, ...]
     *   - [{"skill": "PHP"}, {"skill": "Laravel"}]
     *   - JSON strings of any of the above
     *   - Comma-separated strings
     * Never returns numeric proficiency levels as skills.
     */
    protected function skillsToString(mixed $skills): ?string
    {
        if (empty($skills)) {
            return null;
        }

        if (is_string($skills)) {
            $decoded = json_decode($skills, true);
            if (is_array($decoded)) {
                return $this->skillsToString($decoded);
            }
            $s = trim($skills);
            return $s !== '' ? $s : null;
        }

        if (! is_array($skills)) {
            return null;
        }

        $names = [];

        foreach ($skills as $item) {
            // Flat string / numeric entry
            if (is_string($item) || is_numeric($item)) {
                $s = trim((string) $item);
                if ($s !== '') $names[] = $s;
                continue;
            }

            // Object/assoc array — pull only the name-like key.
            if (is_array($item) || is_object($item)) {
                $item = (array) $item;

                foreach (['name', 'label', 'skill', 'title', 'value'] as $key) {
                    if (! empty($item[$key]) && is_string($item[$key])) {
                        $s = trim($item[$key]);
                        if ($s !== '') $names[] = $s;
                        break; // only take the first matching key
                    }
                }
            }
        }

        $names = array_values(array_unique($names));

        return $names ? implode(', ', array_slice($names, 0, 10)) : null;
    }

    protected function vacancyLocation(Vacancy $v): ?string
    {
        $loc     = $this->str($v->location);
        $country = $this->str($v->country);

        if ($loc === '' && $country === '') return null;
        if ($loc === '')     return $country;
        if ($country === '') return $loc;

        if (! Str::contains(Str::lower($loc), Str::lower($country))) {
            return "{$loc}, {$country}";
        }

        return $loc;
    }

    protected function salaryText(Vacancy $v): ?string
    {
        if (isset($v->is_salary_visible) && ! $v->is_salary_visible) {
            return null;
        }

        $min = $v->salary_min ?? null;
        $max = $v->salary_max ?? null;
        $cur = $this->str($v->salary_currency);

        if (! $min && ! $max) return null;

        $fmt = fn($n) => number_format((float) $n, 0, '.', ',');

        if ($min && $max && $min != $max) {
            return trim("{$cur} " . $fmt($min) . ' – ' . $fmt($max));
        }
        if ($min) return trim("{$cur} " . $fmt($min) . '+');
        return trim("{$cur} up to " . $fmt($max));
    }
}
