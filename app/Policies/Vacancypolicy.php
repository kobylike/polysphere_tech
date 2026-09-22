<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Auth\Access\HandlesAuthorization;

class VacancyPolicy
{
    use HandlesAuthorization;

    // ─── Super Admin bypass ────────────────────────────────────────────────
    public function before(User $user, $ability)
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }
    }

    // ─── View Vacancies ────────────────────────────────────────────────────
    public function viewAny(User $user): bool
    {
        return $user->can('View Vacancies');
    }

    public function view(User $user, Vacancy $vacancy): bool
    {
        return $user->can('View Vacancies');
    }

    // ─── Create Vacancy ────────────────────────────────────────────────────
    public function create(User $user): bool
    {
        return $user->can('Create Vacancy');
    }

    // ─── Edit Vacancy ──────────────────────────────────────────────────────
    // Covers update, publish, close, archive, and toggling "featured" —
    // the Livewire component authorizes all of those against 'update'.
    public function update(User $user, Vacancy $vacancy): bool
    {
        return $user->can('Edit Vacancy');
    }

    // ─── Delete Vacancy ────────────────────────────────────────────────────
    public function delete(User $user, Vacancy $vacancy): bool
    {
        return $user->can('Delete Vacancy');
    }
}
