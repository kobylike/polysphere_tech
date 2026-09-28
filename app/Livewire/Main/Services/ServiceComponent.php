<?php

namespace App\Livewire\Main\Services;

use App\Models\Service;
use Livewire\Component;
use Livewire\WithPagination;

class ServiceComponent extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

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
        ])->layoutData([
            'description' => 'Explore Polysphere Tech services — custom software development, SaaS engineering, cloud solutions, IT consulting and digital transformation.',
            'canonical'   => route('services'),
            // No filters on this page — the listing itself should be indexed.
            'noindex'     => false,
        ])->title('Services | Polysphere Tech');
    }
}
