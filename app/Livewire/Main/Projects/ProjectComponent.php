<?php

namespace App\Livewire\Main\Projects;

use App\Models\Project;
use App\Models\Service;
use Livewire\Component;
use Livewire\WithPagination;

class ProjectComponent extends Component
{
    use WithPagination;

    public $service = ''; // filter by service slug
    public $perPage = 6;

    public function updatingService()
    {
        $this->resetPage();
    }

    public function getProjects()
    {
        $query = Project::with(['service', 'author'])
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc');

        if (!empty($this->service)) {
            $query->whereHas('service', function ($q) {
                $q->where('slug', $this->service);
            });
        }

        return $query->paginate($this->perPage);
    }

    public function getServices()
    {
        return Service::where('status', 'active')
            ->withCount('projects')
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        return view('livewire.main.projects.project-component', [
            'projects' => $this->getProjects(),
            'services' => $this->getServices(),
        ]);
    }
}
