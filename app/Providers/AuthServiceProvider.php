<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Gate that answers “may this user see the Admin nav?”
        Gate::define('view-admin-nav', function (User $user) {
            // adapt to your schema:
            return $user->role === 'admin';   // string column
            // return $user->is_admin;        // boolean column
        });
    }
}
