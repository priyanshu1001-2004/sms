<?php

namespace App\Providers;

use App\Models\Organization;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

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
        Paginator::useBootstrapFive();

        // pass origination data on header page
        view()->composer('layouts.header', function ($view) {
            $organizations = Organization::all();
            $view->with('organizations', $organizations);
        });
    }
}
