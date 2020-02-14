<?php

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => ['web', config('backpack.base.middleware_key', 'admin')],
    'namespace'  => 'Pxpm\BpSettings\App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::get('bp-settings/{namespace}','BpSettingsCrudController@namespacedSettingsEditor');
    Route::crud('bp-settings', 'BpSettingsCrudController');

    Route::post('bp-settings/save','BpSettingsCrudController@save')->name('bp-settings-save');

});