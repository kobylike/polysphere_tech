<?php

namespace App\Livewire\Main\Services;

use App\Models\Service;
use Livewire\Component;

class ServiceDetails extends Component
{
    public $service;
    public $relatedServices;

    public function mount($slug)
    {
        $this->service = Service::where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        $this->relatedServices = Service::where('status', 'active')
            ->where('id', '!=', $this->service->id)
            ->orderBy('order', 'asc')
            ->limit(3)
            ->get();
    }

    public function render()
    {
        return view('livewire.main.services.service-details');
    }
}
