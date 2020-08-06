<?php 

return [
    //the route to access the settings dashboards
    'settings_route_prefix' => 'bp-settings',
    
    //the prefix used by settings when adding settings to the config() array. This `emulates` the config file.
    //by default you can grab your settings with: `config('bpsettings.your_setting_name')`
    'settings_prefix' => 'bpsettings',

    //The seeder class used as the only source of truth for your setting panels. 
    //Seeders in `database/seeds/` are automatically loaded by laravel class loader.
    'settings_seeder' => 'SettingsSeeder',
    
    //the controller that exposes the panels and allows the saving of settings
    'settings_controller' => '\Pxpm\BpSettings\App\Http\Controllers\Admin\BpSettingsCrudController'
];