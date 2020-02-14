<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBpSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bp_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type');
            $table->text('label')->nullable();
            $table->longtext('options')->nullable();
            $table->string('name')->unique();
            $table->text('namespace')->nullable();
            $table->text('tab')->nullable();
            $table->text('group')->nullable();
            $table->longText('value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bp_settings');
    }
}
