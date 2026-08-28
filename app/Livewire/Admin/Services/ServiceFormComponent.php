<?php

namespace App\Livewire\Admin\Services;

use App\Models\Service;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.users')]
class ServiceFormComponent extends Component
{
    use WithFileUploads;

    public $serviceId = null;
    public $name = '';
    public $slug = '';
    public $description = '';
    public $icon = '';
    public $status = 'active';
    public $order = 0;

    // Image uploads
    public $featured_image = null;
    public $existing_featured_image = null;

    public $additional_images = [];
    public $existing_additional_images = [];

    protected function rules()
    {
        $uniqueRule = 'unique:services,slug';
        if ($this->serviceId) {
            $uniqueRule .= ',' . $this->serviceId;
        }

        return [
            'name'          => 'required|string|max:255',
            'slug'          => ['required', 'string', 'max:255', $uniqueRule],
            'description'   => 'nullable|string|max:5000',
            'icon'          => 'nullable|string|max:100',
            'status'        => 'required|in:active,inactive',
            'order'         => 'nullable|integer',
            'featured_image' => 'nullable|image|max:5120',
            'additional_images' => 'nullable|array|max:2',
            'additional_images.*' => 'image|max:5120',
        ];
    }

    protected function messages()
    {
        return [
            'name.required' => 'The service name is required.',
            'slug.unique'   => 'This slug is already taken.',
            'additional_images.max' => 'You can upload a maximum of 2 additional images.',
        ];
    }

    public function mount($id = null)
    {
        if ($id) {
            $this->serviceId = $id;
            $service = Service::findOrFail($id);

            $this->name = $service->name;
            $this->slug = $service->slug;
            $this->description = $service->description;
            $this->icon = $service->icon;
            $this->status = $service->status;
            $this->order = $service->order;

            $this->existing_featured_image = $service->featured_image;
            $this->existing_additional_images = $service->additional_images ?? [];
        }

        if (empty($this->slug) && !empty($this->name)) {
            $this->slug = $this->generateUniqueSlug(Str::slug($this->name));
        }
    }

    protected function generateUniqueSlug($baseSlug)
    {
        $slug = $baseSlug;
        $counter = 1;
        while (Service::where('slug', $slug)->when($this->serviceId, function ($query) {
            return $query->where('id', '!=', $this->serviceId);
        })->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }
        return $slug;
    }

    public function updatedName($value)
    {
        if (empty($this->slug) || $this->slug === Str::slug($value)) {
            $this->slug = $this->generateUniqueSlug(Str::slug($value));
        }
    }

    public function removeAdditionalImage($index)
    {
        if (isset($this->existing_additional_images[$index])) {
            Storage::disk('public')->delete($this->existing_additional_images[$index]);
            unset($this->existing_additional_images[$index]);
            $this->existing_additional_images = array_values($this->existing_additional_images);
        }
    }

    public function save()
    {
        $this->validate();

        $featuredPath = null;
        if ($this->featured_image) {
            $featuredPath = $this->featured_image->store('services/featured', 'public');
            if ($this->serviceId && $this->existing_featured_image) {
                Storage::disk('public')->delete($this->existing_featured_image);
            }
        }

        $additionalPaths = [];
        if ($this->additional_images) {
            foreach ($this->additional_images as $img) {
                $additionalPaths[] = $img->store('services/additional', 'public');
            }
            if ($this->serviceId && $this->existing_additional_images) {
                foreach ($this->existing_additional_images as $old) {
                    Storage::disk('public')->delete($old);
                }
            }
        }

        $data = [
            'name'        => $this->name,
            'slug'        => $this->slug,
            'description' => $this->description,
            'icon'        => $this->icon,
            'status'      => $this->status,
            'order'       => $this->order,
        ];

        if ($featuredPath) {
            $data['featured_image'] = $featuredPath;
        }
        if (!empty($additionalPaths)) {
            $data['additional_images'] = $additionalPaths;
        } elseif ($this->serviceId && $this->existing_additional_images) {
            $data['additional_images'] = $this->existing_additional_images;
        } else {
            $data['additional_images'] = [];
        }

        if ($this->serviceId) {
            $service = Service::findOrFail($this->serviceId);
            $service->update($data);
            session()->flash('success', 'Service updated successfully!');
        } else {
            $maxOrder = Service::max('order') ?? 0;
            $data['order'] = $maxOrder + 1;
            Service::create($data);
            session()->flash('success', 'Service created successfully!');
        }

        return redirect()->route('admin.services.index');
    }

    public function render()
    {
        return view('livewire.admin.services.service-form-component');
    }
}
