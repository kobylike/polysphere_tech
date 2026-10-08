
@props(['path', 'alt' => '', 'eager' => false])

@php
    $file = public_path($path);
    $mtime = is_file($file) ? filemtime($file) : 0;
    $size = \Illuminate\Support\Facades\Cache::rememberForever(
        'imgdim:' . md5($path) . ':' . $mtime,
        fn () => $mtime ? (@getimagesize($file) ?: false) : false
    );
@endphp

<img src="{{ asset($path) }}" alt="{{ $alt }}"
    @if($size) width="{{ $size[0] }}" height="{{ $size[1] }}" @endif
    @if($eager) fetchpriority="high" decoding="async" @else loading="lazy" decoding="async" @endif
    {{ $attributes }}>