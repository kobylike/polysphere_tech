{{--
    resources/views/components/img.blade.php

    Usage:
      <x-img path="assets/main/imgs/about/about-1.jpg" alt="..." />
      <x-img :path="'storage/' . $post->featured_image" :alt="$post->title" />
      <x-img path="..." alt="..." eager />   (above-the-fold / LCP image)

    Reads real pixel dimensions from disk (cached by file mtime) and emits
    width/height to prevent layout shift. Below-the-fold images are lazy.
    Any failure in the lookup is swallowed: worst case, the image renders
    without dimensions, exactly as before.
--}}
@props(['path', 'alt' => '', 'eager' => false])

@php
    $size = false;
    try {
        $file = public_path($path);
        if (is_file($file)) {
            $size = \Illuminate\Support\Facades\Cache::rememberForever(
                'imgdim:' . md5($path) . ':' . filemtime($file),
                fn () => @getimagesize($file) ?: false
            );
        }
    } catch (\Throwable $e) {
        $size = false;
    }
@endphp

<img src="{{ asset($path) }}" alt="{{ $alt }}"
    @if($size) width="{{ $size[0] }}" height="{{ $size[1] }}" @endif
    @if($eager) fetchpriority="high" decoding="async" @else loading="lazy" decoding="async" @endif
    {{ $attributes }}>