# backpack-crud-settings

#### Create settings dashboards to your Backpack application.

![bp_settings_main](https://user-images.githubusercontent.com/7188159/74541740-19563900-4f3a-11ea-8819-00f3687be636.PNG)

![bp_settings_graphics](https://user-images.githubusercontent.com/7188159/74541812-38ed6180-4f3a-11ea-9646-4dc79e2c8cc8.PNG)

### How to:

`composer require pxpm/backpack-crud-settings`

Migrate the settings table:
`php artisan db:migrate`


Create a seeder that will seed your settings into database:

### Important note
> THIS WILL BE YOUR ONLY SOURCE OF TRUTH FOR YOUR DATABASE SETTINGS ANYTIME YOU NEED TO EDIT/REMOVE/ADD SETTINGS IT'S DONE IN THIS SEEDER, AFTER YOU ARE HAPPY WITH THE CHANGES RUN THE SEEDER AGAIN.

```php
<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = [
             [
                'name' => 'company_name',
                'type' => 'text',
                'label' => 'Company Name',
                'tab' => 'Main',
                'wrapperAttributes' => ['class' => 'col-md-6 form-group']
            ],
            [
                'name' => 'company_moto',
                'type' => 'text',
                'label' => 'Company Moto',
                'tab' => 'Main',
                'wrapperAttributes' => ['class' => 'col-md-6 form-group']
            ],
            [
                'name' => 'default_keywords',
                'type' => 'text',
                'label' => 'Default Keywords',
                'tab' => 'Main',
                'group' => 'CEO'
            ],
            [
                'name' => 'main_logo',
                'type' => 'image',
                'label' => 'Main Logo',
                'tab' => 'Graphics',
                'prefix' => 'uploads/',
                'wrapperAttributes' => ['class' => 'col-md-6 form-group']
                
            ],
            [
                'name' => 'black_white_logo',
                'type' => 'image',
                'label' => 'Black White Logo',
                'tab' => 'Graphics',
                'prefix' => 'uploads/',
                'wrapperAttributes' => ['class' => 'col-md-6 form-group']
                
            ],
           [
               'name' => 'require_email_verification',
               'type' => 'checkbox',
               'label' => 'Require email verification',
               'tab' => 'Panel Settings'
           ],
           [
            'name' => 'default_avatar',
            'type' => 'image',
            'label' => 'Avatar',
            'group' => 'Default Settings',
            'namespace' => 'users',
            'prefix' => 'uploads/',
            'wrapperAttributes' => ['class' => 'col-md-4 form-group']
            
        ],
        [
            'name' => 'require_email_verification',
            'type' => 'checkbox',
            'label' => 'Require email verification',
            'group' => 'Default Settings',
            'namespace' => 'users',
            'wrapperAttributes' => ['class' => 'col-md-4 form-group']
        ],
        [
            'name' => 'default_role',
            'type' => 'select2',
            'label' => 'Default User Role',
            'group' => 'Default Settings',
            'namespace' => 'users',
            'model' => 'App\Models\BackpackUser',
            'entity' => 'roles',
            'attribute' => 'name',
            'wrapperAttributes' => ['class' => 'col-md-4 form-group']
            
        ]    
        ];

        foreach($settings as $setting) {
            app('settingsmanager')->create($setting);
        }

        // allwways call this at the end of the seeder. It will remove any unused settings, eg: you changed a setting name, or you
        // just remove some setting from this array.
        app('settingsmanager')->cleanUpDatabaseSettings();
    }
```

You can now access your setting panel in: `your_url.com/backpack_admin_prefix/bp-settings`

### Key points

- Settings are regular crud fields. View backpack documentation on how to use them. 
- `namespace` is used to create different setting panels, for example, creating a setting panel for your articles. Those settings will be available in `your_url.com/backpack_admin_prefix/bp-settings/here_the_namespace`

- `group` allows your to group settings inside a setting panel.

- All settings are pushed into config array. You can grab them with `config('bpsettings.setting_name)` or `app('settingsmanager)->get('setting_name')`

- In image fields you can overwrite disk and prefix using keys `disk` and `prefix` correspondently.

- You can use validation on your settings using regular laravel validation. Define the key `validation` on setting config. Eg:
```php
[
                'name' => 'company_moto',
                'type' => 'text',
                'label' => 'Company Moto',
                'tab' => 'Main Info',
                'validation' => 'required|min:5',
            ],
```

# IMPORTANT NOTE:

> This is still a work in progress, i cannot guarantee 100% flawless work.

