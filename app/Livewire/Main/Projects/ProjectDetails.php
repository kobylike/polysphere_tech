<?php

namespace App\Livewire\Main\Projects;

use App\Models\Project;
use App\Models\Service;
use Livewire\Component;

class ProjectDetails extends Component
{
    public Project $project;
    public $relatedProjects;
    public $recentProjects;

    public function mount($slug)
    {
        $this->project = Project::with(['service', 'author'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        // ─── Related projects (same service, for the main content) ──────
        $this->relatedProjects = Project::where('status', 'published')
            ->where('id', '!=', $this->project->id)
            ->where('service_id', $this->project->service_id)
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get();

        // ─── Recent projects for sidebar (same service, with fallback) ──
        $limit = 5;

        // Try to get projects from the same service first
        $sameServiceProjects = Project::where('status', 'published')
            ->where('id', '!=', $this->project->id)
            ->where('service_id', $this->project->service_id)
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();

        // If we have enough, use them
        if ($sameServiceProjects->count() >= $limit) {
            $this->recentProjects = $sameServiceProjects;
        } else {
            // Otherwise, get more from other services to fill the list
            $otherProjects = Project::where('status', 'published')
                ->where('id', '!=', $this->project->id)
                ->where(function ($query) {
                    $query->where('service_id', '!=', $this->project->service_id)
                        ->orWhereNull('service_id');
                })
                ->orderBy('published_at', 'desc')
                ->limit($limit - $sameServiceProjects->count())
                ->get();

            $this->recentProjects = $sameServiceProjects->merge($otherProjects);
        }
    }

    // ─── Computed Properties ──────────────────────────────────────────────

    public function getYearRangeProperty()
    {
        if ($this->project->start_year && $this->project->end_year) {
            return $this->project->start_year . ' - ' . $this->project->end_year;
        }
        return $this->project->start_year ?: null;
    }

    public function getVideoSrcProperty()
    {
        if ($this->project->video_file) {
            return asset('storage/' . $this->project->video_file);
        }
        return $this->project->video_url;
    }

    public function getAttachmentSizeFormattedProperty()
    {
        $bytes = $this->project->attachment_size;
        if (empty($bytes)) {
            return null;
        }
        $units = ['B', 'KB', 'MB', 'GB'];
        $factor = floor((strlen((string) $bytes) - 1) / 3);
        $factor = min($factor, count($units) - 1);
        return sprintf('%.1f', $bytes / (1024 ** $factor)) . $units[$factor];
    }

    public function getServicesProperty()
    {
        return Service::where('status', 'active')->orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.main.projects.project-details');
    }
}
