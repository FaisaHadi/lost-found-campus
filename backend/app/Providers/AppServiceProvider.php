<?php

namespace App\Providers;

use App\Models\Claim;
use App\Models\Notification;
use App\Models\Report;
use App\Policies\ClaimPolicy;
use App\Policies\NotificationPolicy;
use App\Policies\ReportPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        Gate::policy(Report::class, ReportPolicy::class);
        Gate::policy(Claim::class, ClaimPolicy::class);
        Gate::policy(Notification::class, NotificationPolicy::class);
    }
}
