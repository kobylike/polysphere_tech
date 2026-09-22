NEW APPLICATION — POLYSPHERE TECH
===========================================

Role : {{ $vacancy->title }}
Department : {{ $department }}
Candidate : {{ $application->name }}
Email : {{ $application->email }}
Phone : {{ $application->phone ?: '—' }}
Location :
{{ trim(($application->location ?? '') . ($application->country ? ', ' . $application->country : '')) ?: '—' }}

Experience : {{ $application->years_experience_label }}@if($application->current_role) ·
{{ $application->current_role }}@if($application->current_company), {{ $application->current_company }}@endif @endif
@if($application->availability)
    Availability: {{ $application->availability }}
@endif
@if($application->salary_expectation)
    Salary : {{ $application->salary_expectation }}
@endif
@if($application->timezone)
    Timezone : {{ $application->timezone }}
@endif
@if($application->work_authorization)
    Work auth : {{ $application->work_authorization }}
@endif
@if($application->source)
    Source : {{ $application->source }}@if($application->referrer_name) — {{ $application->referrer_name }}@endif
@endif

Received : {{ $sentAt }}

-------------------------------------------
LINKS
-------------------------------------------
@foreach(array_filter([
        'LinkedIn' => $application->linkedin_url,
        'Portfolio' => $application->portfolio_url,
        'GitHub' => $application->github_url,
        'Site' => $application->personal_site_url,
        'Behance' => $application->behance_url,
        'Dribbble' => $application->dribbble_url,
        'Writing' => $application->writing_samples_url,
    ]) as $label => $url)
    {{ $label }}: {{ $url }}
@endforeach

@if($application->cover_letter)
    -------------------------------------------
    COVER NOTE
    -------------------------------------------
    {{ $application->cover_letter }}

@endif
-------------------------------------------
CV attached: {{ $application->cv_original_name }}
-------------------------------------------

Review in admin: {{ $reviewUrl }}
Reply directly to: {{ $application->email }}

© {{ date('Y') }} Polysphere Tech · Accra, Ghana