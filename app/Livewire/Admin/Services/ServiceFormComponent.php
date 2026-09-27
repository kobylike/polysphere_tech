<?php

namespace App\Livewire\Admin\Services;

use App\Helpers\ActivityLogger;
use App\Models\Service;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Auth\Access\AuthorizationException;

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

    // Featured image: $featured_image holds a newly picked (not-yet-saved)
    // upload; $existing_featured_image always reflects the current saved
    // state (it's set to null the moment the user removes the saved image).
    public $featured_image = null;
    public $existing_featured_image = null;

    // Additional images: $additional_images holds newly picked (not-yet-saved)
    // uploads; $existing_additional_images always reflects the current saved
    // list (an entry is removed from it — and deleted from disk — the moment
    // the user clicks Remove on it).
    public $additional_images = [];
    public $existing_additional_images = [];

    // Not a public/reactive property — just holds the merged result between
    // the updating() and updated() hooks below within a single request.
    protected $mergedAdditionalImages = null;

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
            'additional_images' => [
                'nullable',
                'array',
                function ($attribute, $value, $fail) {
                    if ((count($this->existing_additional_images) + count($value)) > 2) {
                        $fail('You can have a maximum of 2 additional images in total.');
                    }
                },
            ],
            'additional_images.*' => 'image|max:5120',
        ];
    }

    protected function messages()
    {
        return [
            'name.required' => 'The service name is required.',
            'slug.unique'   => 'This slug is already taken.',
        ];
    }

    public function mount($id = null)
    {
        if ($id) {
            $this->serviceId = $id;
            $service = Service::findOrFail($id);
            $this->authorize('update', $service);

            $this->name = $service->name;
            $this->slug = $service->slug;
            $this->description = $service->description;
            $this->icon = $service->icon;
            $this->status = $service->status;
            $this->order = $service->order;

            $this->existing_featured_image = $service->featured_image;
            $this->existing_additional_images = $service->additional_images ?? [];
        } else {
            $this->authorize('create', Service::class);
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

    /**
     * Fires right before Livewire overwrites $additional_images with whatever
     * was just picked in the file dialog. A native <input type="file"> always
     * replaces its own file list on every dialog open — so without this,
     * picking a second image after a first would silently wipe the first one
     * out. We merge the incoming selection with what's already staged instead.
     */
    public function updatingAdditionalImages($value)
    {
        $incoming = is_array($value) ? $value : [$value];
        $this->mergedAdditionalImages = array_merge($this->additional_images, $incoming);
    }

    /**
     * Apply the merge from updatingAdditionalImages(), then live-trim to
     * whatever room is left (2 minus however many existing ones are still
     * kept), so the user gets immediate feedback instead of only finding out
     * at submit time.
     */
    public function updatedAdditionalImages()
    {
        if ($this->mergedAdditionalImages !== null) {
            $this->additional_images = $this->mergedAdditionalImages;
            $this->mergedAdditionalImages = null;
        }

        $remaining = max(0, 2 - count($this->existing_additional_images));

        if (count($this->additional_images) > $remaining) {
            $this->additional_images = array_slice($this->additional_images, 0, $remaining);
            $this->addError(
                'additional_images',
                $remaining > 0
                    ? "You can only add {$remaining} more image(s)."
                    : 'You already have 2 additional images — remove one first.'
            );
        }
    }

    /**
     * Remove the currently saved featured image (deletes it from disk),
     * or just discard a newly picked (not-yet-saved) one.
     */
    public function removeFeaturedImage()
    {
        if ($this->featured_image) {
            $this->featured_image = null;
            return;
        }

        if ($this->existing_featured_image) {
            Storage::disk('public')->delete($this->existing_featured_image);
            $this->existing_featured_image = null;
        }
    }

    /**
     * Remove one of the currently saved additional images (deletes it from
     * disk immediately).
     */
    public function removeAdditionalImage($index)
    {
        if (isset($this->existing_additional_images[$index])) {
            Storage::disk('public')->delete($this->existing_additional_images[$index]);
            unset($this->existing_additional_images[$index]);
            $this->existing_additional_images = array_values($this->existing_additional_images);
        }
    }

    /**
     * Discard one of the newly picked (not-yet-saved) additional images.
     */
    public function removeNewAdditionalImage($index)
    {
        if (isset($this->additional_images[$index])) {
            unset($this->additional_images[$index]);
            $this->additional_images = array_values($this->additional_images);
        }
    }

    public function save()
    {
        $this->validate();

        // Featured image: a new upload replaces the old file on disk;
        // otherwise keep whatever existing_featured_image currently is
        // (null if the user removed it, unchanged if left alone).
        $featuredPath = $this->existing_featured_image;
        if ($this->featured_image) {
            $featuredPath = $this->featured_image->store('services/featured', 'public');
            if ($this->existing_featured_image) {
                Storage::disk('public')->delete($this->existing_featured_image);
            }
        }

        // Additional images: keep whatever is left in existing_additional_images
        // (anything removed was already deleted from disk when the user clicked
        // Remove) and append newly uploaded files, capped at 2 total.
        $newPaths = [];
        foreach ($this->additional_images as $img) {
            $newPaths[] = $img->store('services/additional', 'public');
        }
        $finalAdditional = array_slice(
            array_merge($this->existing_additional_images, $newPaths),
            0,
            2
        );

        $data = [
            'name'              => $this->name,
            'slug'              => $this->slug,
            'description'       => $this->description,
            'icon'              => $this->icon,
            'status'            => $this->status,
            'order'             => $this->order,
            'featured_image'    => $featuredPath,
            'additional_images' => $finalAdditional,
        ];

        if ($this->serviceId) {
            $service = Service::findOrFail($this->serviceId);
            $this->authorize('update', $service);
            $service->update($data);

            ActivityLogger::log('Service updated', [
                'service_id' => $service->id,
                'name'       => $this->name,
                'slug'       => $this->slug,
                'status'     => $this->status,
                'order'      => $this->order,
            ], 'service');

            $this->dispatch('notify', ['type' => 'success', 'title' => 'Updated', 'message' => 'Project updated successfully.']);
        } else {
            $this->authorize('create', Service::class);
            $maxOrder = Service::max('order') ?? 0;
            $data['order'] = $maxOrder + 1;
            $service = Service::create($data);

            ActivityLogger::log('Service created', [
                'service_id' => $service->id,
                'name'       => $this->name,
                'slug'       => $this->slug,
                'status'     => $this->status,
                'order'      => $service->order,
            ], 'service');

            // session()->flash('success', 'Service created successfully!');
            $this->dispatch('notify', ['type' => 'success', 'title' => 'Created', 'message' => 'Service created successfully.']);
        }

        return $this->redirectRoute('admin.services.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.services.service-form-component');
    }
}
