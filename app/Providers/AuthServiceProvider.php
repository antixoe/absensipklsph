<?php

namespace App\Providers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('viewAbsenceData', function (?User $user): bool {
            return $user?->hasAnyRole([Role::KESISWAAN, Role::WALI_KELAS]) ?? false;
        });

        Gate::define('viewAllAbsenceData', function (?User $user): bool {
            return $user?->hasRole(Role::KESISWAAN) ?? false;
        });
    }
}
