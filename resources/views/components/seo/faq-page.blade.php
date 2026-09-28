@props(['faqs' => []])

{{-- FAQPage schema. Pass the FULL list of FAQs (not the search-filtered one). --}}
@php
    $entities = collect($faqs)
        ->filter(fn($faq) => filled($faq['question'] ?? null) && filled($faq['answer'] ?? null))
        ->map(fn($faq) => [
            '@type' => 'Question',
            'name' => strip_tags($faq['question']),
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => $faq['answer'],
            ],
        ])
        ->values()
        ->all();

    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => $entities,
    ];
@endphp

@if (count($entities))
    @push('schema')
        <script
            type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
    @endpush
@endif