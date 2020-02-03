<?php

namespace Pxpm\BpSettings\App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\app\Models\Traits\CrudTrait;

class BpSettings extends Model
{
    use CrudTrait;


    protected $table = 'bp_settings';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'label',
        'value',
        'type',
        'namespace',
        'name',
        'options'
    ];

    protected $casts = [
        'options' => 'array'
    ];

    public static function boot()
    {
        parent::boot();
    }
}
