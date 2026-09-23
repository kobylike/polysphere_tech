@php
    /** @var \App\Models\Vacancy $vacancy */
    /** @var bool $featured */

    $workplaceClass = match ($vacancy->workplace_type->value) {
        'remote' => 'vchip--remote',
        'onsite' => 'vchip--onsite',
        'hybrid' => 'vchip--hybrid',
        default => '',
    };

    $workplaceIcon = match ($vacancy->workplace_type->value) {
        'remote' => 'fa-solid fa-house-laptop',
        'onsite' => 'fa-solid fa-building',
        'hybrid' => 'fa-solid fa-shuffle',
        default => 'fa-solid fa-location-dot',
    };

    // ─── Department avatar ─────────────────────────────
    $deptName = $vacancy->department?->name ?? 'General';
    $deptInitials = collect(explode(' ', $deptName))
        ->filter()
        ->map(fn($p) => mb_substr($p, 0, 1))
        ->take(2)
        ->implode('');

    // Deterministic accent color per department
    $palette = [
        ['from' => '#a5b4fc', 'to' => '#6366f1', 'text' => '#312e81'],  // indigo
        ['from' => '#6ee7b7', 'to' => '#10b981', 'text' => '#064e3b'],  // emerald
        ['from' => '#fcd34d', 'to' => '#f59e0b', 'text' => '#78350f'],  // amber
        ['from' => '#f0abfc', 'to' => '#c026d3', 'text' => '#701a75'],  // fuchsia
        ['from' => '#7dd3fc', 'to' => '#0ea5e9', 'text' => '#0c4a6e'],  // sky
        ['from' => '#fda4af', 'to' => '#e11d48', 'text' => '#881337'],  // rose
    ];
    $accent = $palette[crc32($deptName) % count($palette)];

    // ─── Time signals ──────────────────────────────────
    $isNew = $vacancy->published_at
        && $vacancy->published_at->isPast()
        && $vacancy->published_at->gt(now()->subDays(7));

    $closingDate = $vacancy->closing_date;
    $daysLeft = $closingDate ? (int) now()->startOfDay()->diffInDays($closingDate->startOfDay(), false) : null;

    $isClosingToday = $daysLeft !== null && $daysLeft === 0;
    $isClosingTomorrow = $daysLeft !== null && $daysLeft === 1;
    $isClosingSoon = $daysLeft !== null && $daysLeft > 1 && $daysLeft <= 7;

    // Progress bar for the closing window
    $progressPercent = null;
    if ($closingDate && $vacancy->published_at) {
        $start = $vacancy->published_at->timestamp;
        $end = $closingDate->timestamp;
        if ($end > $start) {
            $elapsed = max(0, min(1, (now()->timestamp - $start) / ($end - $start)));
            $progressPercent = round($elapsed * 100);
        }
    }

    $postedLabel = $vacancy->published_at
        ? $vacancy->published_at->diffForHumans(short: true)
        : $vacancy->created_at->diffForHumans(short: true);

    $bookingKey = 'vacancy-saved-' . $vacancy->id;
@endphp

<article class="vcard {{ $featured ? 'vcard--featured' : '' }}" x-data="{
        saved: localStorage.getItem('{{ $bookingKey }}') === '1',
        shared: false,
        toggleSave() {
            this.saved = !this.saved;
            localStorage.setItem('{{ $bookingKey }}', this.saved ? '1' : '0');
        },
        copyLink() {
            const url = '{{ route('vacancy.details', $vacancy->slug) }}';
            navigator.clipboard.writeText(url).then(() => {
                this.shared = true;
                setTimeout(() => this.shared = false, 2000);
            });
        }
    }">

    {{-- Decorative gradient (hover reveal) --}}
    <div class="vcard__glow" aria-hidden="true"></div>

    {{-- ─── Header ─── --}}
    <header class="vcard__top">
        <div class="vcard__dept"
            style="background: linear-gradient(135deg, {{ $accent['from'] }}, {{ $accent['to'] }});">
            <span>{{ strtoupper($deptInitials ?: '·') }}</span>
        </div>

        <div class="vcard__head">
            <div class="vcard__badges">
                @if($featured)
                    <span class="vbadge vbadge--featured">
                        <i class="fa-solid fa-star"></i> Featured
                    </span>
                @endif
                @if($isNew && !$featured)
                    <span class="vbadge vbadge--new">
                        <i class="fa-solid fa-bolt"></i> New
                    </span>
                @endif
                @if($isClosingToday)
                    <span class="vbadge vbadge--urgent">
                        <i class="fa-solid fa-fire"></i> Closing today
                    </span>
                @elseif($isClosingTomorrow)
                    <span class="vbadge vbadge--urgent">
                        <i class="fa-regular fa-hourglass-half"></i> Closing tomorrow
                    </span>
                @elseif($isClosingSoon)
                    <span class="vbadge vbadge--soon">
                        <i class="fa-regular fa-hourglass-half"></i> Closing soon
                    </span>
                @endif
            </div>

            <h3 class="vcard__title">
                <a wire:navigate.hover href="{{ route('vacancy.details', $vacancy->slug) }}">
                    {{ $vacancy->title }}
                </a>
            </h3>

            <div class="vcard__subtitle">
                <span>{{ $deptName }}</span>
                @if($vacancy->location)
                    <span class="vcard__dot" aria-hidden="true">·</span>
                    <span>
                        <i class="fa-solid fa-location-dot"></i>
                        {{ $vacancy->location }}@if($vacancy->country), {{ $vacancy->country }}@endif
                    </span>
                @endif
            </div>
        </div>
    </header>

    {{-- ─── Chips ─── --}}
    <div class="vcard__meta">
        <span class="vchip">
            <i class="fa-solid fa-clock"></i>
            {{ $vacancy->employment_type->label() }}
        </span>
        <span class="vchip {{ $workplaceClass }}">
            <i class="{{ $workplaceIcon }}"></i>
            {{ $vacancy->workplace_type->label() }}
        </span>
        <span class="vchip">
            <i class="fa-solid fa-gauge-high"></i>
            {{ $vacancy->experience_level->label() }}
        </span>

        @if($vacancy->positions_available > 1)
            <span class="vchip">
                <i class="fa-solid fa-user-group"></i>
                {{ $vacancy->positions_available }} openings
            </span>
        @endif

        @if($vacancy->is_salary_visible && $vacancy->salary_range)
            <span class="vchip vchip--salary">
                <i class="fa-solid fa-coins"></i>
                {{ $vacancy->salary_range }}
            </span>
        @endif
    </div>

    {{-- ─── Summary ─── --}}
    @if($vacancy->summary)
        <p class="vcard__summary">{{ $vacancy->summary }}</p>
    @endif

    {{-- ─── Timeline (only when we know the closing window) ─── --}}
    @if($progressPercent !== null && $closingDate)
        <div class="vcard__timeline">
            <div class="vcard__timeline-bar" role="progressbar" aria-valuenow="{{ $progressPercent }}" aria-valuemin="0"
                aria-valuemax="100">
                <div class="vcard__timeline-fill
                        {{ $progressPercent >= 85 ? 'vcard__timeline-fill--danger' : ($progressPercent >= 60 ? 'vcard__timeline-fill--warn' : '') }}"
                    style="width: {{ $progressPercent }}%;"></div>
            </div>
            <div class="vcard__timeline-meta">
                <span class="vcard__date">
                    <i class="fa-regular fa-clock"></i> Posted {{ $postedLabel }}
                </span>
                <span class="vcard__deadline">
                    @if($isClosingToday)
                        Closes <strong>today</strong>
                    @elseif($isClosingTomorrow)
                        Closes <strong>tomorrow</strong>
                    @elseif($daysLeft !== null && $daysLeft > 0)
                        <strong>{{ $daysLeft }}</strong> days left
                    @else
                        Closes {{ $closingDate->format('M j') }}
                    @endif
                </span>
            </div>
        </div>
    @else
        <div class="vcard__timeline vcard__timeline--plain">
            <span class="vcard__date">
                <i class="fa-regular fa-clock"></i> Posted {{ $postedLabel }}
            </span>
        </div>
    @endif

    {{-- ─── Footer ─── --}}
    <footer class="vcard__foot">
        <div class="vcard__actions-left">
            <button type="button" class="vicon-btn" :class="{ 'is-active': saved }" x-on:click="toggleSave()"
                x-bind:title="saved ? 'Remove from saved' : 'Save this role'" aria-label="Save vacancy">
                <i class="fa-solid fa-bookmark" x-show="saved"></i>
                <i class="fa-regular fa-bookmark" x-show="!saved"></i>
            </button>

            <button type="button" class="vicon-btn" :class="{ 'is-active': shared }" x-on:click="copyLink()"
                x-bind:title="shared ? 'Link copied!' : 'Copy link to this role'" aria-label="Copy link">
                <i class="fa-solid fa-check" x-show="shared"></i>
                <i class="fa-solid fa-link" x-show="!shared"></i>
            </button>
        </div>

        <a wire:navigate.hover href="{{ route('vacancy.details', $vacancy->slug) }}" class="vcard__apply">
            <span>View role</span>
            <i class="fa-solid fa-arrow-right"></i>
        </a>
    </footer>
</article>