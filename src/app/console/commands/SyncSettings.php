<?php

namespace Pxpm\BpSettings\app\Console\Commands;

use Illuminate\Console\Command;

class SyncSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bpsettings:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the settings seeder';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $seederClass = str_replace("\\","\\\\",config('bpsettings.settings_seeder'));
        \Illuminate\Support\Facades\Artisan::call('db:seed --class="'.$seederClass.'"');
        return true;
    }
}
