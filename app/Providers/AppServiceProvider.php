<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Console\Scheduling\Schedule;
use App\Http\ViewComposers\StudentViewComposer;

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
        // Configure login rate limiter
        // Limits login attempts to 5 per minute per IP address
        // This prevents brute force attacks on the login endpoint
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });
        
        // Share student variable with all student views
        View::composer('student.*', StudentViewComposer::class);

        Paginator::useBootstrapFive();
    }
    
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Run timetable status update every hour
        $schedule->command('timetable:mark-completed')->hourly();
    }
}
