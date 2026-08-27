<?php

namespace App\Livewire\Admin\Blog\Category;

use App\Models\Category;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.users')]
class CategoryFormComponent extends Component
{
    public $categoryId = null;
    public $name = '';
    public $slug = '';
    public $description = '';
    public $editingId = null;

    // ─── Dynamic validation ──────────────────────────────────────

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

    // ─── Mount ────────────────────────────────────────────────────

    public function mount($id = null)
    {
        if ($id) {
            $this->categoryId = $id;
            $category = Category::findOrFail($id);
            $this->name = $category->name;
            $this->slug = $category->slug;
            $this->description = $category->description;
        }

        if (empty($this->slug) && !empty($this->name)) {
            $this->slug = $this->generateUniqueSlug(Str::slug($this->name));
        }
    }

    // ─── Helper: generate unique slug ─────────────────────────────

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

    // ─── Update slug when name changes ────────────────────────────

    public function updatedName($value)
    {
        if (empty($this->slug) || $this->slug === Str::slug($value)) {
            $this->slug = $this->generateUniqueSlug(Str::slug($value));
        }
    }

    // ─── Save ──────────────────────────────────────────────────────

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
            $category->update($data);
            $message = 'Category updated successfully!';
        } else {
            $maxOrder = Category::max('order') ?? 0;
            $data['order'] = $maxOrder + 1;
            Category::create($data);
            $message = 'Category created successfully!';
        }

        $this->reset(['name', 'slug', 'description', 'categoryId']);
        $this->editingId = null;
        session()->flash('success', $message);
    }

    // ─── Edit (inline) ────────────────────────────────────────────

    public function edit($id)
    {
        $this->editingId = $id;
        $category = Category::findOrFail($id);
        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->description = $category->description;
        $this->categoryId = $id;
    }

    // ─── Cancel edit ──────────────────────────────────────────────

    public function cancelEdit()
    {
        $this->reset(['name', 'slug', 'description', 'categoryId', 'editingId']);
        if (!empty($this->name)) {
            $this->slug = $this->generateUniqueSlug(Str::slug($this->name));
        }
    }

    // ─── Delete ──────────────────────────────────────────────────

    public function delete($id)
    {
        $category = Category::findOrFail($id);
        if ($category->posts()->count() > 0) {
            session()->flash('error', 'Cannot delete category because it has posts associated.');
            return;
        }
        $category->delete();
        session()->flash('success', 'Category deleted successfully.');
        $this->reset(['name', 'slug', 'description', 'categoryId', 'editingId']);
    }

    // ─── Move Up/Down ─────────────────────────────────────────────

    public function moveUp($id)
    {
        $category = Category::findOrFail($id);
        $previous = Category::where('order', '<', $category->order)
            ->orderBy('order', 'desc')
            ->first();
        if ($previous) {
            $temp = $category->order;
            $category->order = $previous->order;
            $previous->order = $temp;
            $category->save();
            $previous->save();
        }
    }

    public function moveDown($id)
    {
        $category = Category::findOrFail($id);
        $next = Category::where('order', '>', $category->order)
            ->orderBy('order', 'asc')
            ->first();
        if ($next) {
            $temp = $category->order;
            $category->order = $next->order;
            $next->order = $temp;
            $category->save();
            $next->save();
        }
    }

    // ─── Computed property ────────────────────────────────────────

    public function getCategoriesProperty()
    {
        return Category::ordered()->get();
    }

    // ─── Render ────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.admin.blog.category.category-form-component');
    }
}
