<?php

namespace App\Livewire\Admin\Projects;

use App\Models\Project;
use App\Models\Service;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.users')]
class ProjectManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $service = ''; // changed from 'category'
    public $perPage = 10;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $selectedProjects = [];
    public $selectAll = false;
    public $bulkAction = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingService()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedProjects = $this->getProjects()->pluck('id')->toArray();
        } else {
            $this->selectedProjects = [];
        }
    }

    public function applyBulkAction()
    {
        if (empty($this->selectedProjects)) {
            session()->flash('error', 'No projects selected.');
            return;
        }

        switch ($this->bulkAction) {
            case 'delete':
                Project::whereIn('id', $this->selectedProjects)->delete();
                session()->flash('success', 'Selected projects deleted.');
                break;
            case 'publish':
                Project::whereIn('id', $this->selectedProjects)->update(['status' => 'published', 'published_at' => now()]);
                session()->flash('success', 'Selected projects published.');
                break;
            case 'draft':
                Project::whereIn('id', $this->selectedProjects)->update(['status' => 'draft']);
                session()->flash('success', 'Selected projects moved to draft.');
                break;
            case 'trash':
                Project::whereIn('id', $this->selectedProjects)->update(['status' => 'trash']);
                session()->flash('success', 'Selected projects moved to trash.');
                break;
            default:
                session()->flash('error', 'Invalid bulk action.');
                return;
        }

        $this->selectedProjects = [];
        $this->selectAll = false;
        $this->bulkAction = '';
    }

    public function deleteSingle($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();
        session()->flash('success', 'Project deleted.');
    }

    public function getProjects()
    {
        $query = Project::with(['service', 'author']); // eager load service instead of category

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('excerpt', 'like', '%' . $this->search . '%')
                    ->orWhere('content', 'like', '%' . $this->search . '%');
            });
        }
        if (!empty($this->status)) {
            $query->where('status', $this->status);
        }
        if (!empty($this->service)) {
            $query->where('service_id', $this->service);
        }

        $query->orderBy($this->sortField, $this->sortDirection);
        return $query->paginate($this->perPage);
    }

    public function render()
    {
        $projects = $this->getProjects();
        $services = Service::orderBy('name')->get();

        return view('livewire.admin.projects.project-management', [
            'projects' => $projects,
            'services' => $services,
            'statusList' => [
                'draft'     => 'Draft',
                'published' => 'Published',
                'private'   => 'Private',
                'pending'   => 'Pending',
                'trash'     => 'Trash',
            ],
        ]);
    }
}
