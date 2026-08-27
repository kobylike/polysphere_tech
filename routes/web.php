<?php

use App\Livewire\Admin\Blog\Category\CategoryComponent;
use App\Livewire\Admin\Blog\Category\CategoryFormComponent;
use App\Livewire\Admin\Blog\Post\PostFormComponent;
use App\Livewire\Admin\Blog\Post\PostManagement;
use App\Livewire\Admin\Dashboard\DashboardComponent;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Main\AboutComponent;
use App\Livewire\Main\Blog\Posts\PostComponent;
use App\Livewire\Main\Blog\Posts\PostDetailComponent;
use App\Livewire\Main\Blog\Posts\PostDetails;
use App\Livewire\Main\ContactComponent;
use App\Livewire\Main\FaqComponent;
use App\Livewire\Main\IndexComponent;
use App\Livewire\Main\Projects\ProjectComponent;
use App\Livewire\Main\Services\ServiceComponent;
use App\Livewire\Main\Team\TeamComponent;
use Illuminate\Support\Facades\Route;

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
Route::get('/services', ServiceComponent::class)->name('services');

// Admin – protected
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', DashboardComponent::class)->name('dashboard');

    // Blog Management – using slug for edit
    Route::get('/blog-management', PostManagement::class)->name('manage.posts');
    Route::get('/blog-management/create', PostFormComponent::class)->name('create.post');
    Route::get('/blog-management/{slug}/edit', PostFormComponent::class)->name('edit.post'); // slug

    // Category Management – using id (kept as is)
    Route::get('/category-management', CategoryComponent::class)->name('manage.categories');
    Route::get('/category-management/create', CategoryFormComponent::class)->name('create.categories');
    Route::get('/category-management/{id}/edit', CategoryFormComponent::class)->name('edit.categories');
});
