<?php
Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => ['web', config('backpack.base.middleware_key', 'admin')],
], function () { 

    Route::post(config('bpsettings.settings_route_prefix', 'bp-settings').'/save', config('bpsettings.settings_controller').'@save');
    Route::get(config('bpsettings.settings_route_prefix', 'bp-settings').'/{namespace?}', config('bpsettings.settings_controller').'@index');



});