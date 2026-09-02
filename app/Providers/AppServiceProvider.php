<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Permission;
use App\Models\Post;
use App\Models\Project;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use App\Policies\CategoryPolicy;
use App\Policies\PermissionPolicy;
use App\Policies\PostPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\RolePolicy;
use App\Policies\ServicePolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(Permission::class, PermissionPolicy::class);
        Gate::policy(Post::class, PostPolicy::class);
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Project::class, ProjectPolicy::class);
        Gate::policy(Service::class, ServicePolicy::class);


        Gate::define('manage-roles', function ($user) {
            return $user->hasRole('Super Admin');
        });

        Gate::define('manage-permissions', function ($user) {
            return $user->hasRole('Super Admin');
        });
    }
}
