<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use App\Services\ActivityLoggerService;

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
        // Log user login
        $this->app['events']->listen(Login::class, function (Login $event) {
            try {
                ActivityLoggerService::logLogin();
            } catch (\Exception $e) {
                \Log::warning('Failed to log login: ' . $e->getMessage());
            }
        });

        // Log user logout
        $this->app['events']->listen(Logout::class, function (Logout $event) {
            try {
                if ($event->user) {
                    $userId = $event->user->id;
                    \App\Models\ActivityLog::create([
                        'user_id' => $userId,
                        'action' => 'logout',
                        'subject' => 'user',
                        'subject_id' => $userId,
                        'description' => 'User logged out',
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->header('User-Agent'),
                    ]);
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to log logout: ' . $e->getMessage());
            }
        });
    }
}
