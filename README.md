# backpack-crud-settings

#### Create settings dashboards to your Backpack application.

![bp_settings_main](https://user-images.githubusercontent.com/7188159/74541740-19563900-4f3a-11ea-8819-00f3687be636.PNG)

![bp_settings_graphics](https://user-images.githubusercontent.com/7188159/74541812-38ed6180-4f3a-11ea-9646-4dc79e2c8cc8.PNG)

### Instalation:

`composer require pxpm/backpack-crud-settings`

- Migrate the settings table:
`php artisan db:migrate`

 Publish the config file
 `php artisan vendor:publish --provider="Pxpm\BpSettings\BpSettingsServiceProvider" --tag=config`

Create a seeder that will seed your settings into database:

`php artisan make:seeder SettingsSeeder`

### Important note
> This seeder will be the only source of truth for your settings. This is the way you can add/delete/update settings from database.

```php
<?php

use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
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
        ];

        app('settingsmanager')->create($settings);
    }
```

##### IMPORTANT: 
- After creating your seeder you should `composer dump-autoload` 

- Seed the settings with: `php artisan db:seed --class=SettingsSeeder`


You can now access your setting panel in: `your_url.com/backpack_admin_prefix/bp-settings`

### Key points

- Settings are regular crud fields. See [the documentation](https://backpackforlaravel.com/docs/4.1/crud-fields) on backpack website.

- NOT ALL FIELDS AVAILABLE. Majority is. (Relationship field does not work for example.)

- `namespace` is used to create different setting panels, for example, creating a setting panel for your articles. Those settings will be available in `your_url.com/backpack_admin_prefix/bp-settings/articles`

- `group` allows your to group settings inside a setting panel.

- All settings are pushed into config array on run-time. You can use them with `config('admin_settings.setting_name)` or `app('settingsmanager)->get('setting_name')`

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

