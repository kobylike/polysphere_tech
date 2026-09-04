<?php

namespace App\Livewire\Admin\Blog\Post;

use App\Helpers\ActivityLogger;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Auth\Access\AuthorizationException;

#[Layout('layouts.users')]
class PostManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $category = '';
    public $date_from = '';
    public $date_to = '';
    public $perPage = 10;

    public $selectedPosts = [];
    public $selectAll = false;
    public $bulkAction = '';

    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    public function mount()
    {
        $this->authorize('viewAny', Post::class);
    }

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
            $this->selectedPosts = $this->getPosts()->pluck('id')->toArray();
        } else {
            $this->selectedPosts = [];
        }
    }

    public function applyBulkAction()
    {
        if (empty($this->selectedPosts)) {
            session()->flash('error', 'No posts selected.');
            return;
        }

        $count = count($this->selectedPosts);

        switch ($this->bulkAction) {
            case 'delete':
                foreach ($this->selectedPosts as $id) {
                    $post = Post::find($id);
                    if ($post) {
                        $this->authorize('delete', $post);
                    }
                }
                Post::whereIn('id', $this->selectedPosts)->delete();
                ActivityLogger::log('Bulk delete posts', [
                    'post_ids' => $this->selectedPosts,
                    'count'    => $count,
                ], 'post');
                session()->flash('success', 'Selected posts deleted successfully.');
                break;

            case 'publish':
                foreach ($this->selectedPosts as $id) {
                    $post = Post::find($id);
                    if ($post) {
                        $this->authorize('update', $post);
                    }
                }
                Post::whereIn('id', $this->selectedPosts)->update(['status' => 'published', 'published_at' => now()]);
                ActivityLogger::log('Bulk publish posts', [
                    'post_ids' => $this->selectedPosts,
                    'count'    => $count,
                ], 'post');
                session()->flash('success', 'Selected posts published.');
                break;

            case 'draft':
                foreach ($this->selectedPosts as $id) {
                    $post = Post::find($id);
                    if ($post) {
                        $this->authorize('update', $post);
                    }
                }
                Post::whereIn('id', $this->selectedPosts)->update(['status' => 'draft']);
                ActivityLogger::log('Bulk move posts to draft', [
                    'post_ids' => $this->selectedPosts,
                    'count'    => $count,
                ], 'post');
                session()->flash('success', 'Selected posts moved to draft.');
                break;

            case 'trash':
                foreach ($this->selectedPosts as $id) {
                    $post = Post::find($id);
                    if ($post) {
                        $this->authorize('update', $post);
                    }
                }
                Post::whereIn('id', $this->selectedPosts)->update(['status' => 'trash']);
                ActivityLogger::log('Bulk move posts to trash', [
                    'post_ids' => $this->selectedPosts,
                    'count'    => $count,
                ], 'post');
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

    public function deleteSingle($id)
    {
        $post = Post::findOrFail($id);
        $this->authorize('delete', $post);
        $post->delete();

        ActivityLogger::log('Post deleted', [
            'post_id' => $post->id,
            'title'   => $post->title,
        ], 'post');

        session()->flash('success', 'Post deleted successfully.');
    }

    public function getPosts()
    {
        $query = Post::with(['author', 'categories', 'tags']);

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('excerpt', 'like', '%' . $this->search . '%')
                    ->orWhere('content', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->status)) {
            $query->where('status', $this->status);
        }

        if (!empty($this->category)) {
            $query->whereHas('categories', function ($q) {
                $q->where('categories.id', $this->category);
            });
        }

        if (!empty($this->date_from)) {
            $query->whereDate('created_at', '>=', $this->date_from);
        }
        if (!empty($this->date_to)) {
            $query->whereDate('created_at', '<=', $this->date_to);
        }

        $query->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate($this->perPage);
    }

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
