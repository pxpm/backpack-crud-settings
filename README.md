# Backpack Settings

#### Create settings dashboards to your Backpack application.

![bp_settings_main](https://user-images.githubusercontent.com/7188159/74541740-19563900-4f3a-11ea-8819-00f3687be636.PNG)

### Instalation:

`composer require pxpm/backpack-crud-settings`

- Migrate the settings table:
`php artisan db:migrate`

 Publish the config file
 `php artisan vendor:publish --provider="Pxpm\BpSettings\BpSettingsServiceProvider" --tag=config`

Create a seeder that will seed your settings into database:

`php artisan make:seeder SettingsSeeder`

##### IMPORTANT NOTE
> This seeder will be the only source of truth for your settings. This is the way you can add/delete/update settings from database.

The above command will generate something like this in your `Database\Seeds\` folder by default in Laravel application.

```php
<?php

use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        //create your array of settings like defining Backpack fields.
        $settings = [
             [
                'name' => 'company_name',
                'type' => 'text',
                'label' => 'Company Name',
                'tab' => 'Main',
                'wrapperAttributes' => ['class' => 'col-md-6 form-group']
            ]
        ];
        
        //call the setting manager to manage those settings. It will take care of create/update/delete settings.
        app('settingsmanager')->create($settings);
    }
```

##### NOTES: 
- After creating your seeder you should `composer dump-autoload` 

- Seed the settings with: `php artisan db:seed --class=SettingsSeeder`

### Configuration

If you follow all the instalation steps you should now have an `bpsettings.php` file in your `config/` directory. The default config is ready to work out-of-box, but you are free to customize it the way you want.

### Creating settings

> You can now access your settings panel in: `your_url.com/backpack_admin_prefix/bp-settings`

To add settings to your panels like described above you should create an array of `Backpack fields` and pass them to the `Settings Manager`. 
There are some key points in setting definition that you can use to better organize your setting panels.

##### namespace
By default the settings namespace is `null`, that means it's a general setting and will appear in the main settings panel. By defining settings namespaces you can create aditional setting panels. 

If you setup some settings with `namespace => 'users'` those settings will be available separately in: `your_url.com/backpack_admin_prefix/bp-settings/users`

##### group
Allow you to group settings further inside the panel. You can use `tab` provided in Backpack with conjunction with group.

##### validation

You can validate your settings inputs using regular laravel validation. 

Define the key `validation => 'required|min:5'` and we will run your setting input against the laravel validator and return any errors found. 


### Using settings

- All settings are pushed into config array on run-time. We use cache mechanism to make sure we don't query the database if there are no changes to settings and just grab the cached version.

You can get the values of `non namespaced` settings with: `config('bpsettings.setting_name)` or `app('settingsmanager)->get('setting_name')`
To get namespaced setting value you can do it with: `config('bpsettings.namespace.setting_name)` or `app('settingsmanager)->get('namespace.setting_name')`


# IMPORTANT NOTE:

> This is still a work in progress, i cannot guarantee 100% flawless work.

