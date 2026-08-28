<?php

use App\Http\Controllers\CKEditorController;
use App\Livewire\Admin\Blog\Category\CategoryComponent;
use App\Livewire\Admin\Blog\Category\CategoryFormComponent;
use App\Livewire\Admin\Blog\Post\PostFormComponent;
use App\Livewire\Admin\Blog\Post\PostManagement;
use App\Livewire\Admin\Dashboard\DashboardComponent;
use App\Livewire\Admin\Projects\ProjectFormComponent;
use App\Livewire\Admin\Projects\ProjectManagement;
use App\Livewire\Admin\Services\ServiceFormComponent;
use App\Livewire\Admin\Services\ServiceManagement;
use App\Livewire\Admin\Users\PermissionManagement;
use App\Livewire\Admin\Users\RoleManagement;
use App\Livewire\Admin\Users\UserManagement;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Main\AboutComponent;
use App\Livewire\Main\Blog\Posts\CommentComponent;
use App\Livewire\Main\Blog\Posts\PostComponent;
use App\Livewire\Main\Blog\Posts\PostDetailComponent;
use App\Livewire\Main\Blog\Posts\PostDetails;
use App\Livewire\Main\ContactComponent;
use App\Livewire\Main\FaqComponent;
use App\Livewire\Main\IndexComponent;
use App\Livewire\Main\Projects\ProjectComponent;
use App\Livewire\Main\Projects\ProjectDetails;
use App\Livewire\Main\Services\ServiceComponent;
use App\Livewire\Main\Services\ServiceDetails;
use App\Livewire\Main\Team\TeamComponent;
use App\Models\Comment;
use Illuminate\Support\Facades\Route;


Route::get('/comment/verify/{token}', function ($token) {
    $comment = App\Models\Comment::where('verification_token', $token)->firstOrFail();

    if ($comment->verified_at) {
        return redirect()->route('blog.details', $comment->post->slug)
            ->with('info', 'This comment is already verified.');
    }

    $comment->update(['verified_at' => now()]);

    // ─── Store verified guest info in session ──────────────
    if ($comment->guest_email) {
        session()->put('verified_guest', [
            'name'  => $comment->guest_name,
            'email' => $comment->guest_email,
        ]);
    }

    return redirect()->route('blog.details', $comment->post->slug)
        ->with('success', '✅ Your comment has been verified and is now visible!');
})->name('comment.verify');
// Auth
Route::get('/login', Login::class)->name('login');
Route::get('/register', Register::class)->name('register');
Route::get('/forgot-password', ForgotPassword::class)
    ->middleware('guest')
    ->name('password.request');

// Main Public
Route::get('/', IndexComponent::class)->name('index');
Route::get('/contact-us', ContactComponent::class)->name('contact');
Route::get('/about-us', AboutComponent::class)->name('about');
Route::get('/faq', FaqComponent::class)->name('faq');
Route::get('/blog', PostComponent::class)->name('posts');
Route::get('/blog/{slug}', PostDetails::class)->name('blog.details');
Route::get('/team', TeamComponent::class)->name('team');
Route::get('/projects', ProjectComponent::class)->name('projects');
Route::get('/projects/{slug}', ProjectDetails::class)->name('project.details');
Route::get('/services', ServiceComponent::class)->name('services');
Route::get('/services/{slug}', ServiceDetails::class)->name('service.details');


// Admin – protected
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', DashboardComponent::class)->name('dashboard');
    //User Management
    Route::get('/user-management', UserManagement::class)->name('users');
    Route::get('/user-managemment/roles', RoleManagement::class)->name('roles');
    Route::get('/user-managemment/permissions', PermissionManagement::class)->name('permissions');


    // Blog Management – using slug for edit
    Route::get('/blog-management', PostManagement::class)->name('manage.posts');
    Route::get('/blog-management/create', PostFormComponent::class)->name('create.post');
    Route::get('/blog-management/{slug}/edit', PostFormComponent::class)->name('edit.post'); // slug
    Route::post('/ckeditor/upload', [CKEditorController::class, 'upload'])->name('ckeditor.upload');

    // Category Management
    Route::get('/category-management', CategoryComponent::class)->name('manage.categories');
    Route::get('/category-management/create', CategoryFormComponent::class)->name('create.categories');
    Route::get('/category-management/{id}/edit', CategoryFormComponent::class)->name('edit.categories');

    //Project Management
    Route::get('/projects-management', ProjectManagement::class)->name('admin.projects.index');
    Route::get('/projects-management/create', ProjectFormComponent::class)->name('admin.projects.create');
    Route::get('/projects-management/{id}/edit', ProjectFormComponent::class)->name('admin.projects.edit');

    //Service Management
    Route::get('/services-management', ServiceManagement::class)->name('admin.services.index');
    Route::get('/services-management/create', ServiceFormComponent::class)->name('admin.services.create');
    Route::get('/services-management/{id}/edit', ServiceFormComponent::class)->name('admin.services.edit');
});
