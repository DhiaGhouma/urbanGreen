<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Services\EnvironmentalDataService;
use App\Models\Participation;
use App\Observers\ParticipationObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
         $this->app->singleton(EnvironmentalDataService::class, function ($app) {
            return new EnvironmentalDataService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Use Bootstrap 5 for pagination
        Paginator::useBootstrapFive();

        // Register Participation Observer
        Participation::observe(ParticipationObserver::class);

        $this->configureRateLimiting();
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        // General web rate limiting
        RateLimiter::for('web', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // API rate limiting (for future use)
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Login rate limiting - more restrictive
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;
            return [
                Limit::perMinute(5)->by($email.$request->ip()),
                Limit::perMinute(3)->by($email),
            ];
        });

        // Registration rate limiting
        RateLimiter::for('register', function (Request $request) {
            return [
                Limit::perMinute(3)->by($request->ip()),
                Limit::perHour(10)->by($request->ip()),
            ];
        });

        // Password reset rate limiting
        RateLimiter::for('password-reset', function (Request $request) {
            return [
                Limit::perMinute(1)->by($request->ip()),
                Limit::perHour(5)->by($request->ip()),
            ];
        });

        // Global authentication rate limiting
        RateLimiter::for('auth', function (Request $request) {
            return [
                Limit::perMinute(10)->by($request->ip()),
                Limit::perHour(60)->by($request->ip()),
            ];
        });
    }
}
