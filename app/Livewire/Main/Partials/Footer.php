<?php

namespace App\Livewire\Main\Partials;

use App\Models\Post;
use App\Models\Service;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Footer extends Component
{
    public function render()
    {
        // ─── Latest 2 published, public, non-scheduled blog posts ────────
        $recentPosts = Cache::remember(
            'footer:recent_posts',
            now()->addMinutes(10),
            fn() => Post::query()
                ->where('status', 'published')
                ->where('visibility', 'public')
                ->where(function ($q) {
                    $q->whereNull('published_at')
                        ->orWhere('published_at', '<=', now());
                })
                ->orderByDesc('published_at')
                ->limit(2)
                ->get(['id', 'title', 'slug', 'featured_image', 'published_at'])
        );

        // ─── Up to 5 active services ─────────────────────────────────────
        $services = Cache::remember(
            'footer:services',
            now()->addMinutes(10),
            fn() => Service::query()
                ->where('status', 'active')
                ->orderBy('order', 'asc')
                ->limit(5)
                ->get(['id', 'name', 'slug'])
        );

        // ─── Social media URLs ──────────────────────────────────────────
        $socials = [
            'linkedin'  => 'https://www.linkedin.com/company/polysphere-tech/',
            'facebook'  => 'https://web.facebook.com/polyspheretech',
            'instagram' => 'https://www.instagram.com/polyspheretech',
            'x'         => 'https://x.com/polyspheretech',
            'youtube'   => 'https://www.youtube.com/@polyspheretech',
        ];

        return view('livewire.main.partials.footer', [
            'recentPosts' => $recentPosts,
            'services'    => $services,
            'socials'     => $socials,
        ]);
    }
}
