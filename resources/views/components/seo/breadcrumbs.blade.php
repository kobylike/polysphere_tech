@props(['crumbs' => []])

{{--
BreadcrumbList schema. Pass an ordered array of ['name' => ..., 'url' => ...],
starting with Home and ending with the current page. Example:

<x-seo.breadcrumbs :crumbs="[
        ['name' => 'Home', 'url' => route('index')],
        ['name' => 'Blog', 'url' => route('posts')],
        ['name' => $post->title, 'url' => route('blog.details', $post->slug)],
    ]" />
--}}
@php
    $items = collect($crumbs)
        ->values()
        ->map(fn($crumb, $i) => [
            '@type' => 'ListItem',
            'position' => $i + 1,
            'name' => $crumb['name'],
            'item' => $crumb['url'],
        ])
        ->all();

    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $items,
    ];
@endphp

@if (count($items) > 1)
    @push('schema')
        <script
            type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
    @endpush
@endif