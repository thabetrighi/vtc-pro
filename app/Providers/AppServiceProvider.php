<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Orchid\Platform\Dashboard;

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
        $this->app->bind(\Laravel\Fortify\Http\Controllers\PasswordResetLinkController::class, \App\Http\Controllers\Auth\PasswordResetLinkController::class);
        $this->app->bind(\Laravel\Fortify\Actions\AttemptToAuthenticate::class, \App\Actions\Fortify\AttemptToAuthenticate::class);
        Dashboard::useModel(
            \Orchid\Platform\Models\User::class,
            \App\Models\User::class
        );
    }
}
