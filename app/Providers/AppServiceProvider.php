<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use App\Support\CurrentOrganization;
use Illuminate\Support\Facades\Gate;
use App\Support\Roles;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CurrentOrganization::class, fn () => new CurrentOrganization());
    }

    /**
     * Bootstrap any application services.
     */
        public function boot(): void
        {
            Gate::define('org.read', fn ($user) => $user->hasOrgRole(Roles::READONLY));
            Gate::define('org.write', fn ($user) => $user->hasOrgRole(Roles::MEMBER));
            Gate::define('org.manage', fn ($user) => $user->hasOrgRole(Roles::ADMIN));
            Gate::define('org.owner', fn ($user) => $user->hasOrgRole(Roles::OWNER));
        }

    
}
