<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        // eventos => listeners aquí si los usas
    ];

    public function boot(): void
    {
        //
    }
}
