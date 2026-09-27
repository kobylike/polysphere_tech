<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use App\Models\Vacancy;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate the public sitemap.xml from static routes + dynamic content';

    public function handle(): int
    {
        $sitemap = Sitemap::create();

        // ─────────────────────────────────────────────────────────────
        // STATIC / LISTING PAGES — matched to your actual routes/web.php
        // ─────────────────────────────────────────────────────────────
        $staticPages = [
            ['route' => 'index',    'priority' => 1.0, 'freq' => Url::CHANGE_FREQUENCY_WEEKLY],
            ['route' => 'services', 'priority' => 0.9, 'freq' => Url::CHANGE_FREQUENCY_WEEKLY],
            ['route' => 'about',    'priority' => 0.7, 'freq' => Url::CHANGE_FREQUENCY_MONTHLY],
            ['route' => 'contact',  'priority' => 0.7, 'freq' => Url::CHANGE_FREQUENCY_MONTHLY],
            ['route' => 'faq',      'priority' => 0.5, 'freq' => Url::CHANGE_FREQUENCY_MONTHLY],
            ['route' => 'team',     'priority' => 0.6, 'freq' => Url::CHANGE_FREQUENCY_MONTHLY],
            ['route' => 'posts',    'priority' => 0.8, 'freq' => Url::CHANGE_FREQUENCY_DAILY],
            ['route' => 'projects', 'priority' => 0.7, 'freq' => Url::CHANGE_FREQUENCY_WEEKLY],
            ['route' => 'vacancies', 'priority' => 0.6, 'freq' => Url::CHANGE_FREQUENCY_WEEKLY],
        ];

        foreach ($staticPages as $page) {
            if (!Route::has($page['route'])) {
                $this->warn("Skipping unknown route: {$page['route']}");
                continue;
            }

            $sitemap->add(
                Url::create(route($page['route']))
                    ->setPriority($page['priority'])
                    ->setChangeFrequency($page['freq'])
            );
        }

        // ─────────────────────────────────────────────────────────────
        // BLOG POSTS — route('blog.details', $slug)
        // Matches App\Models\Post: status='published', visibility='public',
        // and published_at must not be in the future (scheduled posts
        // shouldn't be indexed until they actually go live).
        // ─────────────────────────────────────────────────────────────
        if (class_exists(Post::class) && Route::has('blog.details')) {
            Post::query()
                ->where('status', 'published')
                ->where('visibility', 'public')
                ->where(function ($q) {
                    $q->whereNull('published_at')->orWhere('published_at', '<=', now());
                })
                ->orderByDesc('updated_at')
                ->chunk(200, function ($posts) use ($sitemap) {
                    foreach ($posts as $post) {
                        $sitemap->add(
                            Url::create(route('blog.details', $post->slug))
                                ->setLastModificationDate($post->updated_at)
                                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                                ->setPriority(0.6)
                        );
                    }
                });
        }

        // ─────────────────────────────────────────────────────────────
        // SERVICES — route('service.details', $slug)
        // Matches App\Models\Service migration: status enum is
        // 'active'/'inactive', defaulting to 'active'.
        // ─────────────────────────────────────────────────────────────
        if (class_exists(Service::class) && Route::has('service.details')) {
            Service::query()
                ->where('status', 'active')
                ->orderByDesc('updated_at')
                ->chunk(200, function ($services) use ($sitemap) {
                    foreach ($services as $service) {
                        $sitemap->add(
                            Url::create(route('service.details', $service->slug))
                                ->setLastModificationDate($service->updated_at)
                                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                                ->setPriority(0.8)
                        );
                    }
                });
        }

        // ─────────────────────────────────────────────────────────────
        // PROJECTS — route('project.details', $slug)
        // Matches App\Models\Project migration: status enum defaults to
        // 'draft', visibility enum includes 'password_protected'/'private'
        // (neither belongs in a public sitemap), and published_at can be
        // set in the future for scheduling.
        // ─────────────────────────────────────────────────────────────
        if (class_exists(Project::class) && Route::has('project.details')) {
            Project::query()
                ->where('status', 'published')
                ->where('visibility', 'public')
                ->where(function ($q) {
                    $q->whereNull('published_at')->orWhere('published_at', '<=', now());
                })
                ->orderByDesc('updated_at')
                ->chunk(200, function ($projects) use ($sitemap) {
                    foreach ($projects as $project) {
                        $sitemap->add(
                            Url::create(route('project.details', $project->slug))
                                ->setLastModificationDate($project->updated_at)
                                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                                ->setPriority(0.6)
                        );
                    }
                });
        }

        // ─────────────────────────────────────────────────────────────
        // VACANCIES — route('vacancy.details', $slug)
        // Uses Vacancy's own scopeOpenForApplications(): status must be
        // Published (enum) AND published_at not in the future AND
        // closing_date not already past. A vacancy that's Published but
        // past its closing_date is correctly excluded — no point sending
        // search traffic to a role that's no longer accepting applicants.
        // ─────────────────────────────────────────────────────────────
        if (class_exists(Vacancy::class) && Route::has('vacancy.details')) {
            Vacancy::query()
                ->openForApplications()
                ->orderByDesc('updated_at')
                ->chunk(200, function ($vacancies) use ($sitemap) {
                    foreach ($vacancies as $vacancy) {
                        $sitemap->add(
                            Url::create(route('vacancy.details', $vacancy->slug))
                                ->setLastModificationDate($vacancy->updated_at)
                                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                                ->setPriority(0.5)
                        );
                    }
                });
        }

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated at public/sitemap.xml with ' . count($sitemap->getTags()) . ' URLs.');

        return self::SUCCESS;
    }
}
