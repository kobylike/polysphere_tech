@props(['post'])

{{-- BlogPosting schema for a published post. --}}
@php
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'BlogPosting',
        'headline' => \Illuminate\Support\Str::limit($post->seo_title ?: $post->title, 110, ''),
        'description' => $post->seo_description ?: \Illuminate\Support\Str::limit(strip_tags((string) ($post->excerpt ?: $post->content)), 160),
        'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => route('blog.details', $post->slug)],
        'datePublished' => optional($post->published_at ?? $post->created_at)->toIso8601String(),
        'dateModified' => optional($post->updated_at)->toIso8601String(),
        'author' => ['@type' => 'Person', 'name' => $post->author?->name ?? 'Polysphere Tech'],
        'publisher' => [
            '@type' => 'Organization',
            'name' => 'Polysphere Tech',
            'logo' => ['@type' => 'ImageObject', 'url' => asset('assets/main/imgs/logo/logo-white.png')],
        ],
    ];

    if ($post->featured_image) {
        $schema['image'] = [asset('storage/' . $post->featured_image)];
    }
@endphp

@push('schema')
    <script
        type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endpush