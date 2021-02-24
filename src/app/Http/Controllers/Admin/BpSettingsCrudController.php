<?php

namespace Pxpm\BpSettings\app\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BpSettingsCrudController extends CrudController
{

    public function setup() {
        $this->crud->setModel('Pxpm\BpSettings\app\models\BpSettings');
        $this->crud->setEntityNameStrings(trans('bpsettings::bpsettings.setting'), trans('bpsettings::bpsettings.settings'));
    }

    public function index($namespace = null) {
       
        $fields = app('settingsmanager')->getFieldsForEditor($namespace);

        foreach($fields as $field) {
            $this->crud->addField($field);
        }
        
        $this->data['crud'] = $this->crud;
        $this->data['title'] = $this->crud->getTitle() ?? mb_ucfirst($this->crud->entity_name_plural);

        return view('bpsettings::settings_editor', $this->data);
        
    }

    public function save(Request $request) {
        $settings = $request->except(['http_referrer', '_token']);
        
        $namespace = last(request()->segments()) === 'save' ? null : last(request()->segments());

        $validationRules = app('settingsmanager')->getFieldValidations($settings);
        
        Validator::make($settings, $validationRules)->validate();

        if(app('settingsmanager')->saveSettingsValues($settings, $namespace)) {
            return response()->json('success');
        }

        return response()->json('saving error');
    }

}
