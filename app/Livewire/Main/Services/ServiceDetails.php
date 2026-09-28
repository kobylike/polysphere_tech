<?php

namespace App\Livewire\Main\Services;

use App\Models\Service;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
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

    private function ogImage(): ?string
    {
        return $this->service->featured_image
            ? asset('storage/' . $this->service->featured_image)
            : null;
    }

    public function render()
    {
        return view('livewire.main.services.service-details')
            ->layoutData([
                'description' => Str::limit(strip_tags((string) $this->service->description), 160),
                'canonical'   => route('service.details', $this->service->slug),
                'ogImage'     => $this->ogImage(),
                'ogType'      => 'website',
            ])
            ->title($this->service->name . ' | Polysphere Tech');
    }
}
