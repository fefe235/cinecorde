<?php

namespace App\Providers;

use App\Models\Critiques;
use App\Models\Movies;
use App\Models\News;
use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class => \App\Policies\UserPolicy::class,
        Critiques::class => \App\Policies\CritiquesPolicy::class,
        Movies::class => \App\Policies\MoviesPolicy::class,
        News::class => \App\Policies\NewsPolicy::class,
    ];
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
    public function boot()
{
    Paginator::useBootstrapFive(); // 👈 Important
}
}
