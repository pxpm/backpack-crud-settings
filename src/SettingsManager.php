<?php

namespace Pxpm\BpSettings;

use Pxpm\BpSettings\app\models\BpSettings;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SettingsManager
{
    public $settings;

    private $preLoadedSettings = array();


    public function __construct()
    {
        $this->settings = $this->getDatabaseSettings();
        $this->preLoadedSettings = $this->settings ? $this->settings->pluck('name')->toArray() : [];
    }

    /**
     * Check if settings are cached, if not cache them. 
     * Returs the cached version if available or retrive the settings from DB.
     *
     * @return void
     */
    public function getDatabaseSettings() {
        
        return Cache::rememberForever('bp-settings', function () {
            if (Schema::connection(DB::getDefaultConnection())->hasTable('bp_settings')) {
                return BpSettings::all();
            }

            return collect();
            
        });
    }

    /**
     * Deletes unused settings from database. This uses the seeder as the only source of truth
     *
     * @return void
     */
    public function cleanUpDatabaseSettings($seededSettings) {
        if(!empty($this->preLoadedSettings) && !empty($seededSettings)) {
            if(!empty($diff = array_diff($this->preLoadedSettings,$seededSettings))) {
                foreach ($diff as $settingToDelete) {
                    if (in_array($settingToDelete, $this->preLoadedSettings)) {
                        BpSettings::where('name', $settingToDelete)->first()->delete();
                    }
                }
            }
        }
        $this->rebuildSettingsCache();
    }


    /**
     * Clears the setting cache and get fresh setting data from database.
     *
     * @return void
     */
     public function rebuildSettingsCache() {

        if (Cache::has('bp-settings')) {
            Cache::forget('bp-settings');
        }
         $this->settings = $this->getDatabaseSettings();
         
     }

    /**
     * Returns the setting value.
     *
     * @param string $setting 
     * @param mixed $namespace
     * @return mixed
     */
    public function get($setting, $namespace = null)
    {
        if($this->settingExists($setting, $namespace)) {
            return $this->settings->where('name',$setting)->where('namespace', $namespace)->first()->value;
        }
    }

    public function settingExists($setting, $namespace = null) {
        $name = is_string($setting) ? $setting : (is_array($setting) ? $setting['name'] : abort(500, 'Could not parse setting.'));
        $namespace = $setting['namespace'] ?? $namespace;
        return $this->settings->contains(function($setting_in_collection) use ($name, $namespace) {
                return $setting_in_collection['name'] == $name && $setting_in_collection['namespace'] == $namespace;
            });
    }

    public function create($settings) {
        foreach ($settings as $setting) {
            $dbSetting = null;
            $setting['type'] ?? abort(500, 'Setting need a type.');
            $setting['name'] ?? abort(500, 'Setting need a name.');
            $setting['label'] = $setting['label'] ?? $setting['name'];
            $setting['tab'] = $setting['tab'] ?? null;
            $setting['group'] = $setting['group'] ?? null;
            $setting['namespace'] = $setting['namespace'] ?? null;

            $settingOptions = Arr::except($setting, ['type','name','label','tab','group','value','id', 'namespace']);
            
            if ($this->settingExists($setting)) {
               
                $dbSetting = BpSettings::where('name', $setting['name'])->first();

                $dbSetting->update(Arr::except($setting, array_keys($settingOptions)));
            }
            
            if (!isset($dbSetting) && is_null($dbSetting)) {
                $dbSetting = BpSettings::create([
                'name' => $setting['name'],
                'type' => $setting['type'],
                'label' => $setting['label'],
                'tab' => $setting['tab'] ?? null,
                'namespace' => $setting['namespace'] ?? null,
                'group' => $setting['group'] ?? null,

            ]);
            }
        
            $dbSetting->options = $settingOptions;
            $dbSetting->save();

        }
        $this->cleanUpDatabaseSettings(Arr::pluck($settings, 'name'));
    }

    public function getFieldValidations($settings, $namespace = null) {
        $validations = array();
        foreach($this->settings->whereIn('name',array_keys($settings))->where('namespace', $namespace) as $setting) {
                $validations[$setting['name']] = $setting['options']['validation'] ?? null;
        }
        return array_filter($validations);
    }

    public function saveSettingsValues($settings, $namespace = null) {
        $settingsInDb = BpSettings::whereIn('name', array_keys($settings))->where('namespace', $namespace)->get();
        foreach($settings as $settingName => $settingValue) {
            $setting = $settingsInDb->where('name',$settingName)->first();
            if (!is_null($setting)) {
                switch ($setting->type) {
                case 'image': {
                    $settingValue = $this->saveImageToDisk($settingValue, $settingName);
                }
                }

                $setting->update(['value' => $settingValue]);
            }

        }
        $this->rebuildSettingsCache();

        return true;
    }

    public function getFieldsForEditor($namespace = null) {
        foreach ($this->settings as &$setting) {
            if ($setting->namespace === $namespace) {
                if(is_string($setting->access) && is_callable($setting->access)) {
                    //$hasAccess = $setting->access(request(), $setting);
                }
                foreach ($setting->options as $key => $option) {
                    $setting->{$key} = $option;
                }
                unset($setting->options);
            }
        }
        return $this->settings->where('namespace',$namespace)->keyBy('name')->toArray();
    }

    public function update(array $setting) {
        return BpSettings::where('name',$setting['name'])->update(Arr::except($setting, ['name']));
    }

    public function saveImageToDisk($image,$settingName)
    {
        $disk = config('bpsettings.image_save_disk');
        $prefix = config('bpsettings.image_save_disk');

        $setting = BpSettings::where('name',$settingName)->first();

        if ($image === null) {
            // delete the image from disk
            if(Storage::disk($disk)->has($setting->value))
            {
                Storage::disk($disk)->delete($setting->value);
            }

            // set null in the database column
            return null;
        }

        // if a base64 was sent, store it in the db
        if (Str::startsWith($image, 'data:image'))
        {
            // 0. Make the image
            $imageCreated = \Image::make($image);

            // 1. Generate a filename.
            $filename = md5($image.time()).'.jpg';
            // 2. Store the image on disk.
            if(Storage::disk($disk)->has($setting->value))
            {
                Storage::disk($disk)->delete($setting->value);
            }

            Storage::disk($disk)->put($prefix.$filename, $imageCreated->stream());
            // 3. Save the path to the database

            return $prefix.$filename;
        }

        return $setting->value;
    }

    
}
