<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Gate khusus Super Administrator
        \Illuminate\Support\Facades\Gate::define('access-super-admin', function ($user) {
            return $user->isSuperAdmin();
        });

        // Gate perlindungan akun Super Administrator / System Account
        \Illuminate\Support\Facades\Gate::define('manage-system-account', function ($currentUser, $targetUser = null) {
            if ($targetUser && $targetUser->isSystemAccount()) {
                return $currentUser->isSuperAdmin();
            }
            return true;
        });
    }
}
