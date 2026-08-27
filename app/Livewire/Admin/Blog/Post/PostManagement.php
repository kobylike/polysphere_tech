<?php

namespace App\Livewire\Admin\Blog\Post;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.users')]
class PostManagement extends Component
{
    use WithPagination;

    // ─── Filters ────────────────────────────────────────────────────

    public $search = '';
    public $status = '';
    public $category = '';
    public $date_from = '';
    public $date_to = '';
    public $perPage = 10;

    // ─── Bulk actions ──────────────────────────────────────────────

    public $selectedPosts = [];
    public $selectAll = false;
    public $bulkAction = '';

    // ─── Sorting ────────────────────────────────────────────────────

    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // ─── Reset pagination when filters change ─────────────────────

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingCategory()
    {
        $this->resetPage();
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
    {
        $this->resetPage();
    }

    // ─── Sort ──────────────────────────────────────────────────────

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    // ─── Select all / deselect all ────────────────────────────────

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedPosts = $this->getPosts()->pluck('id')->toArray();
        } else {
            $this->selectedPosts = [];
        }
    }

    // ─── Bulk action handler ──────────────────────────────────────

    public function applyBulkAction()
    {
        if (empty($this->selectedPosts)) {
            session()->flash('error', 'No posts selected.');
            return;
        }

        switch ($this->bulkAction) {
            case 'delete':
                Post::whereIn('id', $this->selectedPosts)->delete();
                session()->flash('success', 'Selected posts deleted successfully.');
                break;

            case 'publish':
                Post::whereIn('id', $this->selectedPosts)->update(['status' => 'published', 'published_at' => now()]);
                session()->flash('success', 'Selected posts published.');
                break;

            case 'draft':
                Post::whereIn('id', $this->selectedPosts)->update(['status' => 'draft']);
                session()->flash('success', 'Selected posts moved to draft.');
                break;

            case 'trash':
                Post::whereIn('id', $this->selectedPosts)->update(['status' => 'trash']);
                session()->flash('success', 'Selected posts moved to trash.');
                break;

            default:
                session()->flash('error', 'Invalid bulk action.');
                return;
        }

        $this->selectedPosts = [];
        $this->selectAll = false;
        $this->bulkAction = '';
    }

    // ─── Delete a single post ─────────────────────────────────────

    public function deleteSingle($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();
        session()->flash('success', 'Post deleted successfully.');
    }

    // ─── Get posts with filters ────────────────────────────────────

    public function getPosts()
    {
        $query = Post::with(['author', 'categories', 'tags']);

        // Search
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('excerpt', 'like', '%' . $this->search . '%')
                    ->orWhere('content', 'like', '%' . $this->search . '%');
            });
        }

        // Status filter
        if (!empty($this->status)) {
            $query->where('status', $this->status);
        }

        // Category filter
        if (!empty($this->category)) {
            $query->whereHas('categories', function ($q) {
                $q->where('categories.id', $this->category);
            });
        }

        // Date range
        if (!empty($this->date_from)) {
            $query->whereDate('created_at', '>=', $this->date_from);
        }
        if (!empty($this->date_to)) {
            $query->whereDate('created_at', '<=', $this->date_to);
        }

        // Sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate($this->perPage);
    }

    // ─── Render ────────────────────────────────────────────────────

    public function render()
    {
        $posts = $this->getPosts();
        $categories = Category::orderBy('name')->get();

        return view('livewire.admin.blog.post.post-management', [
            'posts'      => $posts,
            'categories' => $categories,
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
