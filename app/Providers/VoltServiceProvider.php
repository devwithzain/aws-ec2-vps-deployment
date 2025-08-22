<?php

namespace App\Providers;

use Livewire\Volt\Volt;
use Illuminate\Support\ServiceProvider;

class VoltServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }
    public function boot(): void
    {
        Volt::mount([
            config('livewire.view_path', resource_path('views/livewire')),
            resource_path('views/pages'),
        ]);
    }
}
