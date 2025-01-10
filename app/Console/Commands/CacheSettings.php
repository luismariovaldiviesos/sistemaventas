<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class CacheSettings extends Command
{
   //nombre del comando para usar en consola
    protected $signature = 'cache:settings';



    // Descripción del comando
    protected $description = 'Cachear los datos de la tabla settings';


    public function handle()
    {
        // Cachear los datos de la tabla settings
        $settings =  Setting::first();

        if($settings){
            //guarda en cache de forma permanente
            Cache::forever('settings', $settings);
            $this->info('Configuración de settings cacheada correctamente.');
                }   else { $this->error('no hay datos de empresa');}

    }
}
