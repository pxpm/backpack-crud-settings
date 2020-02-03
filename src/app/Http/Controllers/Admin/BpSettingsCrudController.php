<?php

namespace Pxpm\BpSettings\App\Http\Controllers\Admin;

// VALIDATION: change the requests to match your own file names if you need form validation
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BpSettingsCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;

    public function setup() {
        $this->crud->setModel('Pxpm\BpSettings\App\Models\BpSettings');
        $this->crud->setRoute(config('backpack.base.route_prefix').'/bp-settings');
        $this->crud->setEntityNameStrings('setting', 'settings');
    }

    public function setupListOperation() {
        $this->crud->setListView('bpsettings::settings_editor');
        $fields = app('settingsmanager')->getFieldsForEditor();
        foreach($fields as $field) {
            $this->crud->addField($field);
        }  
        
    }

    public function save(Request $request) {
        $settings = $request->except(['http_referrer', '_token']);

        $validationRules = app('settingsmanager')->getFieldValidations();
        
        Validator::make($settings, $validationRules)->validate();

        if(app('settingsmanager')->saveSettingsValues($settings)) {
            return response()->json('success');
        }

        return response()->json('saving error');
    }
}