<?php

namespace App\Providers;

use App\Models\Event;
use App\Models\GroupUser;
use App\Policies\EventPolicy;
use App\Policies\GroupUserPolicy;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    protected $policies = [
        GroupUser::class => GroupUserPolicy::class,
        Event::class => EventPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

    }
}
