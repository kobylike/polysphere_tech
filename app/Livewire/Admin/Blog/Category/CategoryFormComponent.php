<?php

namespace App\Livewire\Admin\Blog\Category;

use App\Helpers\ActivityLogger;
use App\Models\Category;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Illuminate\Auth\Access\AuthorizationException;

#[Layout('layouts.users')]
class CategoryFormComponent extends Component
{
    public $categoryId = null;
    public $name = '';
    public $slug = '';
    public $description = '';
    public $editingId = null;

    protected function rules()
    {
        $uniqueRule = 'unique:categories,slug';
        if ($this->categoryId) {
            $uniqueRule .= ',' . $this->categoryId;
        }

        return [
            'name'        => 'required|string|max:255',
            'slug'        => ['required', 'string', 'max:255', $uniqueRule],
            'description' => 'nullable|string|max:1000',
        ];
    }

    protected function messages()
    {
        return [
            'name.required' => 'The name is required.',
            'slug.required' => 'The slug is required.',
            'slug.unique'   => 'This slug is already taken. Please use a different one.',
        ];
    }

    public function mount($id = null)
    {
        $this->authorize('viewAny', Category::class);

        if ($id) {
            $this->categoryId = $id;
            $category = Category::findOrFail($id);
            $this->authorize('update', $category);

            $this->name = $category->name;
            $this->slug = $category->slug;
            $this->description = $category->description;
        }

        if (empty($this->slug) && !empty($this->name)) {
            $this->slug = $this->generateUniqueSlug(Str::slug($this->name));
        }
    }

    protected function generateUniqueSlug($baseSlug)
    {
        $slug = $baseSlug;
        $counter = 1;
        while (Category::where('slug', $slug)->when($this->categoryId, function ($query) {
            return $query->where('id', '!=', $this->categoryId);
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

    public function save()
    {
        $this->validate();

        $data = [
            'name'        => $this->name,
            'slug'        => $this->slug,
            'description' => $this->description,
        ];

        if ($this->categoryId) {
            $category = Category::findOrFail($this->categoryId);
            $this->authorize('update', $category);
            $category->update($data);
            $message = 'Category updated successfully!';
            ActivityLogger::log('Category updated', [
                'category_id' => $category->id,
                'name'        => $category->name,
                'slug'        => $category->slug,
                'previous'    => $category->getOriginal(),
            ], 'category');
        } else {
            $this->authorize('create', Category::class);
            $maxOrder = Category::max('order') ?? 0;
            $data['order'] = $maxOrder + 1;
            $category = Category::create($data);
            $message = 'Category created successfully!';
            ActivityLogger::log('Category created', [
                'category_id' => $category->id,
                'name'        => $category->name,
                'slug'        => $category->slug,
            ], 'category');
        }

        $this->reset(['name', 'slug', 'description', 'categoryId']);
        $this->editingId = null;
        session()->flash('success', $message);
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $this->authorize('update', $category);

        $this->editingId = $id;
        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->description = $category->description;
        $this->categoryId = $id;
    }

    public function cancelEdit()
    {
        $this->reset(['name', 'slug', 'description', 'categoryId', 'editingId']);
        if (!empty($this->name)) {
            $this->slug = $this->generateUniqueSlug(Str::slug($this->name));
        }
    }

    public function delete($id)
    {
        $category = Category::findOrFail($id);
        $this->authorize('delete', $category);

        if ($category->posts()->count() > 0) {
            session()->flash('error', 'Cannot delete category because it has posts associated.');
            return;
        }
        $category->delete();
        ActivityLogger::log('Category deleted', [
            'category_id' => $category->id,
            'name'        => $category->name,
        ], 'category');

        session()->flash('success', 'Category deleted successfully.');
        $this->reset(['name', 'slug', 'description', 'categoryId', 'editingId']);
    }

    public function moveUp($id)
    {
        $category = Category::findOrFail($id);
        $this->authorize('update', $category);

        $previous = Category::where('order', '<', $category->order)
            ->orderBy('order', 'desc')
            ->first();
        if ($previous) {
            $temp = $category->order;
            $category->order = $previous->order;
            $previous->order = $temp;
            $category->save();
            $previous->save();

            ActivityLogger::log('Category order changed (move up)', [
                'category_id' => $category->id,
                'name'        => $category->name,
                'new_order'   => $category->order,
            ], 'category');
        }
    }

    public function moveDown($id)
    {
        $category = Category::findOrFail($id);
        $this->authorize('update', $category);

        $next = Category::where('order', '>', $category->order)
            ->orderBy('order', 'asc')
            ->first();
        if ($next) {
            $temp = $category->order;
            $category->order = $next->order;
            $next->order = $temp;
            $category->save();
            $next->save();

            ActivityLogger::log('Category order changed (move down)', [
                'category_id' => $category->id,
                'name'        => $category->name,
                'new_order'   => $category->order,
            ], 'category');
        }
    }

    public function getCategoriesProperty()
    {
        return Category::ordered()->get();
    }

    public function render()
    {
        return view('livewire.admin.blog.category.category-form-component');
    }
}
