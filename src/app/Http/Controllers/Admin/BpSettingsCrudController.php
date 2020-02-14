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
        $this->crud->denyAccess(['create','update','delete']);
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

        $namespace = last(request()->segments());

        $validationRules = app('settingsmanager')->getFieldValidations($namespace);
        
        Validator::make($settings, $validationRules)->validate();

        if(app('settingsmanager')->saveSettingsValues($settings)) {
            return response()->json('success');
        }

        return response()->json('saving error');
    }

    public function namespacedSettingsEditor($namespace) {
        $this->crud->hasAccessOrFail('list');
        $fields = app('settingsmanager')->getFieldsForEditor($namespace);
        if(count($fields) < 1) {
            abort(500, 'Inexistent namespace.');
        }
        foreach($fields as $field) {
            $this->crud->addField($field);
        }
        
        $this->data['crud'] = $this->crud;
        $this->data['title'] = $this->crud->getTitle() ?? mb_ucfirst($this->crud->entity_name_plural);

        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view('bpsettings::settings_editor', $this->data);
    }
}