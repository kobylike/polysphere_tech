<?php

namespace App\Livewire\Admin\Projects;

use App\Models\Project;
use App\Models\Service;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Auth\Access\AuthorizationException;

#[Layout('layouts.users')]
class ProjectManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $service = '';
    public $perPage = 10;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $selectedProjects = [];
    public $selectAll = false;
    public $bulkAction = '';

    // ─── Mount ──────────────────────────────────────────────────────────────

    public function mount()
    {
        $this->authorize('viewAny', Project::class);
    }

    // ─── Filters ────────────────────────────────────────────────────────────

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

    // ─── Sorting ────────────────────────────────────────────────────────────

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    // ─── Select All ─────────────────────────────────────────────────────────

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedProjects = $this->getProjects()->pluck('id')->toArray();
        } else {
            $this->selectedProjects = [];
        }
    }

    // ─── Bulk Actions ──────────────────────────────────────────────────────

    public function applyBulkAction()
    {
        if (empty($this->selectedProjects)) {
            session()->flash('error', 'No projects selected.');
            return;
        }

        // Check permission based on action
        switch ($this->bulkAction) {
            case 'delete':
                $this->authorize('delete', Project::class);
                break;
            case 'publish':
            case 'draft':
            case 'trash':
                $this->authorize('update', Project::class);
                break;
            default:
                session()->flash('error', 'Invalid bulk action.');
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

    // ─── Delete Single ──────────────────────────────────────────────────────

    public function deleteSingle($id)
    {
        $project = Project::findOrFail($id);
        $this->authorize('delete', $project);
        $project->delete();
        session()->flash('success', 'Project deleted.');
    }

    // ─── Get Projects ──────────────────────────────────────────────────────

    public function getProjects()
    {
        $query = Project::with(['service', 'author']);

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

    // ─── Render ────────────────────────────────────────────────────────────

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
