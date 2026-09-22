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

    // Department initials – graceful fallback if department is null
    $deptName = $vacancy->department?->name ?? 'General';
    $deptInitials = collect(explode(' ', $deptName))
        ->map(fn($p) => mb_substr($p, 0, 1))
        ->take(2)
        ->implode('');
@endphp

<article class="vcard {{ $featured ? 'vcard--featured' : '' }}">
    <div class="vcard__top">
        <div class="vcard__dept" aria-hidden="true">{{ strtoupper($deptInitials) }}</div>
        <div class="vcard__head">
            <h3 class="vcard__title">
                <a wire:navigate.hover href="{{ route('vacancy.details', $vacancy->slug) }}">{{ $vacancy->title }}</a>
            </h3>
            <div class="vcard__dept-name">
                {{ $deptName }}
                @if($vacancy->location)
                    · {{ $vacancy->location }}@if($vacancy->country), {{ $vacancy->country }}@endif
                @endif
            </div>
        </div>
        @if($featured)
            <span class="vcard__featured-flag"><i class="fa-solid fa-star"></i> Featured</span>
        @endif
    </div>

    <div class="vcard__meta">
        <span class="vchip"><i class="fa-solid fa-clock"></i>{{ $vacancy->employment_type->label() }}</span>
        <span class="vchip {{ $workplaceClass }}"><i
                class="{{ $workplaceIcon }}"></i>{{ $vacancy->workplace_type->label() }}</span>
        <span class="vchip"><i class="fa-solid fa-gauge-high"></i>{{ $vacancy->experience_level->label() }}</span>
        @if($vacancy->is_salary_visible && $vacancy->salary_range)
            <span class="vchip" style="background:rgba(16,185,129,.1); color:#047857;">
                <i class="fa-solid fa-coins" style="color:#10b981;"></i>{{ $vacancy->salary_range }}
            </span>
        @endif
    </div>

    @if($vacancy->summary)
        <p class="vcard__summary">{{ $vacancy->summary }}</p>
    @endif

    <div class="vcard__foot">
        <span class="vcard__date">
            <i class="fa-regular fa-clock"></i>
            @if($vacancy->published_at)
                Posted {{ $vacancy->published_at->diffForHumans() }}
            @else
                Posted {{ $vacancy->created_at->diffForHumans() }}
            @endif
            @if($vacancy->closing_date)
                · Closes {{ $vacancy->closing_date->format('M j') }}
            @endif
        </span>
        <a wire:navigate.hover href="{{ route('vacancy.details', $vacancy->slug) }}" class="vcard__apply">
            View role <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>
</article>