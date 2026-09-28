<?php

namespace App\Livewire\Main\Partials;

use App\Models\Post;
use App\Models\Service;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Footer extends Component
{
    public function render()
    {
        // ─── Latest 2 published, public, non-scheduled blog posts ────────
        $recentPosts = collect(Cache::remember(
            'footer:recent_posts_v2',
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
                ->map(fn($p) => [
                    'id'             => $p->id,
                    'title'          => $p->title,
                    'slug'           => $p->slug,
                    'featured_image' => $p->featured_image,
                    'published_at'   => $p->published_at?->toDateTimeString(),
                ])
                ->all()
        ))->map(fn($p) => (object) array_merge($p, [
            'published_at' => $p['published_at'] ? Carbon::parse($p['published_at']) : null,
        ]));

        // ─── Up to 5 active services ─────────────────────────────────────
        $services = collect(Cache::remember(
            'footer:services_v2',
            now()->addMinutes(10),
            fn() => Service::query()
                ->where('status', 'active')
                ->orderBy('order', 'asc')
                ->limit(5)
                ->get(['id', 'name', 'slug'])
                ->map(fn($s) => [
                    'id'   => $s->id,
                    'name' => $s->name,
                    'slug' => $s->slug,
                ])
                ->all()
        ))->map(fn($s) => (object) $s);

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
