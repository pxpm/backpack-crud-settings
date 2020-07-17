<?php

namespace Pxpm\BpSettings;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Pxpm\BpSettings\SettingsManager;
use Illuminate\Support\Facades\Schema;

class BpSettingsServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/routes.php');
         
        $this->loadViewsFrom(__DIR__.'/resources/views', 'bpsettings');

        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        $this->mergeConfigFrom(__DIR__.'/config/config.php', 'bpsettings');

        $this->pushToConfig();

    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->publishes([
            __DIR__.'/config/config.php' => config_path('bpsettings.php')
        ], 'config');

        $this->app->singleton(SettingsManager::class, function () {
            return new SettingsManager();
        });

        $this->app->alias(SettingsManager::class, 'settingsmanager');
    }

    public function pushToConfig() {
        $setting_prefix = config('bpsettings.settings_prefix');
        foreach(app('settingsmanager')->settings as $setting) {
            config()->set(($setting_prefix ?? '').($setting_prefix ? '.' : '').$setting->name, $setting->value);
        } 
    }

}
