<?php

use App\Http\Controllers\CallController;
use App\Http\Controllers\CKEditorController;
use App\Livewire\Admin\Blog\Category\CategoryComponent;
use App\Livewire\Admin\Blog\Category\CategoryFormComponent;
use App\Livewire\Admin\Blog\Post\PostFormComponent;
use App\Livewire\Admin\Blog\Post\PostManagement;
use App\Livewire\Admin\Dashboard\DashboardComponent;
use App\Livewire\Admin\Hrm\HrDashboard;
use App\Livewire\Admin\Messenger\ChatMessengerComponent;
use App\Livewire\Admin\Messenger\ChatMessengerMain;
use App\Livewire\Admin\Projects\ProjectFormComponent;
use App\Livewire\Admin\Projects\ProjectManagement;
use App\Livewire\Admin\Services\ServiceFormComponent;
use App\Livewire\Admin\Services\ServiceManagement;
use App\Livewire\Admin\Users\Account\AccountSettings;
use App\Livewire\Admin\Users\Account\ActivityComponent;
use App\Livewire\Admin\Users\Account\Overview;
use App\Livewire\Admin\Users\Account\ProfileComponent;
use App\Livewire\Admin\Users\Account\SecurityComponent;
use App\Livewire\Admin\Users\PermissionManagement;
use App\Livewire\Admin\Users\RoleManagement;
use App\Livewire\Admin\Users\UserManagement;
use App\Livewire\Auth\EmailVerification;
use App\Livewire\Auth\ForcePasswordChange;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\PasswordReset;
use App\Livewire\Auth\PrivacyComponent;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\TermsComponent;
use App\Livewire\Auth\TwoFactorVerification;
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
use App\Livewire\Main\Team\TeamDetails;
use App\Models\Comment;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;




Route::middleware(['auth'])->prefix('call')->name('call.')->group(function () {
    Route::post('initiate', [CallController::class, 'initiate'])->name('initiate');
    Route::post('signal',   [CallController::class, 'signal'])->name('signal');
    Route::post('end',      [CallController::class, 'end'])->name('end');
});

Route::get('/two-factor-verification', TwoFactorVerification::class)
    ->middleware('guest')
    ->name('two-factor.verification');


Route::get('/email/verify', EmailVerification::class)
    ->middleware('auth')
    ->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('dashboard')->with('success', 'Email verified successfully!');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('success', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

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
Route::get('/register/{token?}', Register::class)->name('register');
Route::get('/forgot-password', ForgotPassword::class)
    ->middleware('guest')
    ->name('password.request');
Route::get('/reset-password/{token}', PasswordReset::class)
    ->middleware('guest')
    ->name('password.reset');


Route::get('/team', TeamComponent::class)->name('team');
Route::get('/team/{slug}', TeamDetails::class)->name('team.details');
Route::get('/privacy', PrivacyComponent::class)->name('privacy');

// Main Public
Route::get('/', IndexComponent::class)->name('index');
Route::get('/contact-us', ContactComponent::class)->name('contact');
Route::get('/about-us', AboutComponent::class)->name('about');
Route::get('/faq', FaqComponent::class)->name('faq');
Route::get('/blog', PostComponent::class)->name('posts');
Route::get('/blog/{slug}', PostDetails::class)->name('blog.details');
Route::get('/projects', ProjectComponent::class)->name('projects');
Route::get('/projects/{slug}', ProjectDetails::class)->name('project.details');
Route::get('/services', ServiceComponent::class)->name('services');
Route::get('/services/{slug}', ServiceDetails::class)->name('service.details');


Route::get('/force-password-change', ForcePasswordChange::class)
    ->middleware('auth')
    ->name('password.change.force');



Route::middleware(['auth', 'force.password.change'])->group(function () {

    Route::middleware(['verified', 'not.suspended'])->group(function () {

        // ─── Public (for all authenticated users) ──────────────────────────
        Route::get('/account/{tab?}', AccountSettings::class)
            ->where('tab', 'overview|profile|security|activity')
            ->name('account');

        Route::get('/dashboard', DashboardComponent::class)->name('dashboard');

        // Chat messenger – accessible to everyone (may have internal permissions)
        Route::get('/chat-messenger', ChatMessengerMain::class)->name('messenger');

        // ─── User Management ────────────────────────────────────────────────
        Route::get('/user-management', UserManagement::class)
            ->middleware('can:View Users')
            ->name('users');

        Route::get('/user-management/roles', RoleManagement::class)
            ->middleware('can:manage-roles')
            ->name('roles');

        Route::get('/user-management/permissions', PermissionManagement::class)
            ->middleware('can:manage-permissions')
            ->name('permissions');

        // ─── HR Dashboard ────────────────────────────────────────────────────
        Route::get('/hr', HrDashboard::class)
            ->middleware('can:View HR Dashboard')
            ->name('hr.dashboard');

        // ─── Blog Management ────────────────────────────────────────────────
        Route::prefix('blog-management')->group(function () {
            Route::get('/', PostManagement::class)
                ->middleware('can:View Posts')
                ->name('manage.posts');

            Route::get('/create', PostFormComponent::class)
                ->middleware('can:Create Posts')
                ->name('create.post');

            Route::get('/{slug}/edit', PostFormComponent::class)
                ->middleware('can:Edit Posts')
                ->name('edit.post');
        });

        Route::post('/ckeditor/upload', [CKEditorController::class, 'upload'])->name('ckeditor.upload');

        // ─── Category Management ────────────────────────────────────────────
        Route::prefix('category-management')->group(function () {
            Route::get('/', CategoryComponent::class)
                ->middleware('can:View Categories')
                ->name('manage.categories');

            Route::get('/create', CategoryFormComponent::class)
                ->middleware('can:Create Categories')
                ->name('create.categories');

            Route::get('/{id}/edit', CategoryFormComponent::class)
                ->middleware('can:Edit Categories')
                ->name('edit.categories');
        });

        // ─── Project Management ─────────────────────────────────────────────
        Route::prefix('projects-management')->group(function () {
            Route::get('/', ProjectManagement::class)
                ->middleware('can:View Projects')
                ->name('admin.projects.index');

            Route::get('/create', ProjectFormComponent::class)
                ->middleware('can:Create Projects')
                ->name('admin.projects.create');

            Route::get('/{id}/edit', ProjectFormComponent::class)
                ->middleware('can:Edit Projects')
                ->name('admin.projects.edit');
        });

        // ─── Service Management ─────────────────────────────────────────────
        Route::prefix('services-management')->group(function () {
            Route::get('/', ServiceManagement::class)
                ->middleware('can:View Services')
                ->name('admin.services.index');

            Route::get('/create', ServiceFormComponent::class)
                ->middleware('can:Create Services')
                ->name('admin.services.create');

            Route::get('/{id}/edit', ServiceFormComponent::class)
                ->middleware('can:Edit Services')
                ->name('admin.services.edit');
        });
    });
});
