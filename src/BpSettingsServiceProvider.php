<?php

namespace Pxpm\BpSettings;

use Illuminate\Support\ServiceProvider;
use Pxpm\BpSettings\SettingsManager;

class BpSettingsServiceProvider extends ServiceProvider
{
    protected $commands = [
        \Pxpm\BpSettings\app\console\commands\SyncSettings::class,
    ];

    // Indicates if loading of the provider is deferred.
    protected $defer = false;

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */

    public function boot()
    {
       
        $this->loadViewsFrom(__DIR__.'/resources/views', 'bpsettings');

        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'bpsettings');

        $this->mergeConfigFrom(__DIR__.'/config/config.php', 'bpsettings');

        $this->pushToConfig();

        $this->loadRoutesFrom(__DIR__.'/routes/routes.php'); 
        
         // register the artisan commands
         $this->commands($this->commands);

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
            config()->set(($setting_prefix ? $setting_prefix .'.' : '').($setting['namespace'] ? $setting['namespace'] .'.' . $setting->name :  $setting->name), $setting->value);
        }
    }

}
