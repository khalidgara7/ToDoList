<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AuthServiceInterface;
use App\Services\AuthService;
use App\Services\TaskServiceInterface;
use App\Services\TaskService;
use App\Services\NotificationServiceInterface;
use App\Services\NotificationService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind Service Interfaces
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
        $this->app->bind(TaskServiceInterface::class, TaskService::class);
        $this->app->bind(NotificationServiceInterface::class, NotificationService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}