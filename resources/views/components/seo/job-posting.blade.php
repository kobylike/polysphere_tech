@props(['vacancy'])

{{-- JobPosting schema (Google for Jobs). Only emitted while the vacancy is open. --}}
@if ($vacancy->is_open)
    @php
        $typeMap = [
            'full_time' => 'FULL_TIME',
            'part_time' => 'PART_TIME',
            'contract' => 'CONTRACTOR',
            'internship' => 'INTERN',
            'intern' => 'INTERN',
            'temporary' => 'TEMPORARY',
            'freelance' => 'CONTRACTOR',
        ];
        $employmentType = $typeMap[$vacancy->employment_type->value] ?? 'OTHER';
        $isRemote = $vacancy->workplace_type->value === 'remote';

        // Build an HTML description (Google reads this field as HTML): the main
        // description plus the responsibilities / requirements / benefits lists.
        $toLines = fn($text) => collect(preg_split("/\r\n|\n|\r/", (string) $text))
            ->map(fn($line) => trim($line))
            ->filter();

        $descriptionHtml = '<p>' . nl2br(e($vacancy->description)) . '</p>';

        $sections = [
            'responsibilities' => "What you'll do",
            'requirements' => "What we're looking for",
            'benefits' => 'What we offer',
        ];

        foreach ($sections as $field => $heading) {
            $items = $toLines($vacancy->{$field});
            if ($items->isNotEmpty()) {
                $descriptionHtml .= '<h3>' . e($heading) . '</h3><ul>'
                    . $items->map(fn($item) => '<li>' . e($item) . '</li>')->implode('')
                    . '</ul>';
            }
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'JobPosting',
            'title' => $vacancy->title,
            'description' => $descriptionHtml,
            'datePosted' => optional($vacancy->published_at ?? $vacancy->created_at)->toDateString(),
            'employmentType' => $employmentType,
            'directApply' => true,
            'url' => route('vacancy.details', $vacancy->slug),
            'hiringOrganization' => [
                '@type' => 'Organization',
                'name' => 'Polysphere Tech',
                'sameAs' => url('/'),
                'logo' => asset('assets/main/imgs/logo/logo-white.png'),
            ],
            'identifier' => ['@type' => 'PropertyValue', 'name' => 'Polysphere Tech', 'value' => (string) $vacancy->id],
        ];

        if ($vacancy->closing_date) {
            $schema['validThrough'] = $vacancy->closing_date->toIso8601String();
        }

        if ($isRemote) {
            $schema['jobLocationType'] = 'TELECOMMUTE';
            if ($vacancy->country) {
                $schema['applicantLocationRequirements'] = ['@type' => 'Country', 'name' => $vacancy->country];
            }
        }

        if (!$isRemote || $vacancy->location) {
            $schema['jobLocation'] = [
                '@type' => 'Place',
                'address' => array_filter([
                    '@type' => 'PostalAddress',
                    'addressLocality' => $vacancy->location,
                    'addressCountry' => $vacancy->country,
                ]),
            ];
        }

        if ($vacancy->is_salary_visible && ($vacancy->salary_min || $vacancy->salary_max)) {
            $schema['baseSalary'] = [
                '@type' => 'MonetaryAmount',
                'currency' => $vacancy->salary_currency,
                'value' => array_filter([
                    '@type' => 'QuantitativeValue',
                    'minValue' => $vacancy->salary_min ? (float) $vacancy->salary_min : null,
                    'maxValue' => $vacancy->salary_max ? (float) $vacancy->salary_max : null,
                    'unitText' => 'YEAR',
                ]),
            ];
        }
    @endphp

    @push('schema')
        <script
            type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
    @endpush
@endif