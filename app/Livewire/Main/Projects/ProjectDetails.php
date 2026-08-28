<?php

namespace App\Livewire\Main\Projects;

use App\Models\Project;
use Livewire\Component;

class ProjectDetails extends Component
{
    public $project;
    public $relatedProjects;

    public function mount($slug)
    {
        $this->project = Project::with(['service', 'author'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        // Related projects (same service, exclude current)
        $this->relatedProjects = Project::where('status', 'published')
            ->where('id', '!=', $this->project->id)
            ->where('service_id', $this->project->service_id)
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get();
    }

    public function render()
    {
        return view('livewire.main.projects.project-details');
    }
}
