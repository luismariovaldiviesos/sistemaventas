<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Verifica si la tabla 'settings' existe antes de consultarla
    if (Schema::hasTable('settings')) {
        if (!Cache::has('settings')) {
            $settings = Setting::first();
            Cache::forever('settings', $settings);
        }
    }
    }
}
