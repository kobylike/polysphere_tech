<?php

namespace App\Providers;

use App\Models\Application;
use App\Models\Category;
use App\Models\Department;
use App\Models\Permission;
use App\Models\Post;
use App\Models\Project;
use App\Models\Role;
use App\Models\Service;
use App\Models\Subscriber;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Vacancy;
use App\Observers\ProjectObserver;
use App\Observers\ServiceObserver;
use App\Observers\UserProfileObserver;
use App\Observers\VacancyObserver;
use App\Policies\ApplicationPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\DepartmentPolicy;
use App\Policies\PermissionPolicy;
use App\Policies\PostPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\RolePolicy;
use App\Policies\ServicePolicy;
use App\Policies\SubscriberPolicy;
use App\Policies\UserPolicy;
use App\Policies\VacancyPolicy;
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
        Gate::policy(Subscriber::class, SubscriberPolicy::class);
        Gate::policy(Vacancy::class, VacancyPolicy::class);
        Gate::policy(Application::class, ApplicationPolicy::class);
        Gate::policy(Department::class, DepartmentPolicy::class);

        Gate::define('manage-roles', function ($user) {
            return $user->hasRole('Super Admin');
        });

        Gate::define('manage-permissions', function ($user) {
            return $user->hasRole('Super Admin');
        });



        //observers
        Vacancy::observe(VacancyObserver::class);
        Project::observe(ProjectObserver::class);
        Service::observe(ServiceObserver::class);
        UserProfile::observe(UserProfileObserver::class);
    }
}
