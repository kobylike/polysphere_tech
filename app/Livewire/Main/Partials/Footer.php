<?php

namespace App\Livewire\Main\Partials;

use App\Models\Post;
use App\Models\Service;
use Livewire\Component;

class Footer extends Component
{
    public function render()
    {
        // ─── Latest 2 published blog posts ──────────────────────────────
        $recentPosts = Post::where('status', 'published')
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->limit(2)
            ->get(['id', 'title', 'slug', 'featured_image', 'published_at']);

        // ─── Up to 5 active services ─────────────────────────────────────
        $services = Service::where('status', 'active')
            ->orderBy('order', 'asc')
            ->limit(5)
            ->get(['id', 'name', 'slug']);

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
