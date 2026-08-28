<?php

namespace App\Livewire\Admin\Services;

use App\Models\Service;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.users')]
class ServiceManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $perPage = 10;
    public $sortField = 'order';
    public $sortDirection = 'asc';
    public $selectedServices = [];
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
            $this->selectedServices = $this->getServices()->pluck('id')->toArray();
        } else {
            $this->selectedServices = [];
        }
    }

    public function applyBulkAction()
    {
        if (empty($this->selectedServices)) {
            session()->flash('error', 'No services selected.');
            return;
        }

        switch ($this->bulkAction) {
            case 'delete':
                Service::whereIn('id', $this->selectedServices)->delete();
                session()->flash('success', 'Selected services deleted.');
                break;
            case 'active':
                Service::whereIn('id', $this->selectedServices)->update(['status' => 'active']);
                session()->flash('success', 'Selected services activated.');
                break;
            case 'inactive':
                Service::whereIn('id', $this->selectedServices)->update(['status' => 'inactive']);
                session()->flash('success', 'Selected services deactivated.');
                break;
            default:
                session()->flash('error', 'Invalid bulk action.');
                return;
        }

        $this->selectedServices = [];
        $this->selectAll = false;
        $this->bulkAction = '';
    }

    public function deleteSingle($id)
    {
        $service = Service::findOrFail($id);
        $service->delete();
        session()->flash('success', 'Service deleted.');
    }

    public function moveUp($id)
    {
        $service = Service::findOrFail($id);
        $prev = Service::where('order', '<', $service->order)
            ->orderBy('order', 'desc')
            ->first();
        if ($prev) {
            $temp = $service->order;
            $service->order = $prev->order;
            $prev->order = $temp;
            $service->save();
            $prev->save();
        }
    }

    public function moveDown($id)
    {
        $service = Service::findOrFail($id);
        $next = Service::where('order', '>', $service->order)
            ->orderBy('order', 'asc')
            ->first();
        if ($next) {
            $temp = $service->order;
            $service->order = $next->order;
            $next->order = $temp;
            $service->save();
            $next->save();
        }
    }

    public function getServices()
    {
        $query = Service::query();

        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('description', 'like', '%' . $this->search . '%');
        }
        if (!empty($this->status)) {
            $query->where('status', $this->status);
        }

        $query->orderBy($this->sortField, $this->sortDirection);
        return $query->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.admin.services.service-management', [
            'services' => $this->getServices(),
        ]);
    }
}
