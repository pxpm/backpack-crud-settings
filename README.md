# backpack-crud-settings

As the name implies this package allows you to create settings for your backpack admin panels that your admin can edit in the dashboard.

### How to:

`composer require pxpm/backpack-crud-settings`

Migrate the settings table:
`php artisan db:migrate`


Create a seeder that will seed your settings into database:
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
                'name' => 'company_moto',
                'type' => 'text',
                'label' => 'Company Moto',
                'namespace' => 'Main',
            ],
            [
                'name' => 'company_logo',
                'type' => 'image',
                'label' => 'Company Logo',
                'namespace' => 'Graphics',
                'prefix' => 'uploads/'
            ]
        ];

        foreach($settings as $setting) {
            app('settingsmanager')->create($setting);
        }

        app('settingsmanager')->rebuildSettingsCache();
```

You can now access your setting panel in: `your_url.com/backpack_admin_prefix/bp-settings`

### Key points

- Settings are regular crud fields. View backpack documentation on how to use them. 
- `namespace` is like `tab` in crud fields.
- `group` allows your to group settings inside a tab.


# IMPORTANT NOTE:

> This is still a work in progress, i cannot guarantee 100% flawless work.

