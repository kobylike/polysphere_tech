<?php

namespace App\Livewire\Main\Services;

use App\Models\Service;
use Livewire\Component;
use Livewire\WithPagination;

class ServiceComponent extends Component
{
    use WithPagination;

    public $perPage = 6;

    public function getServices()
    {
        return Service::where('status', 'active')
            ->orderBy('order', 'asc')
            ->orderBy('name', 'asc')
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.main.services.service-component', [
            'services' => $this->getServices(),
        ]);
    }
}
