<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\rolServicio;

class CambiarTrabajaDomingo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cambiar_trabaja_domingo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea nuevos registros para cambiar trabajaDomingo en la tabla rolServicio';

    /**
     * Execute the console command.
     */
    public function handle()
    {
           // Obtén todas las unidades
           $unidades = rolServicio::all();

           foreach ($unidades as $unidad) {
               // Verifica si ya existe un registro con el mismo idUnidad y trabajaDomingo
               $unidadExistente = rolServicio::where('idUnidad', $unidad->idUnidad)
                   ->where('trabajaDomingo', ($unidad->trabajaDomingo === 'SI') ? 'NO' : 'SI')
                   ->first();
   
               if (!$unidadExistente) {
                   // Si no existe, crea un nuevo registro
                   $nuevaUnidad = new rolServicio();
                   $nuevaUnidad->trabajaDomingo = ($unidad->trabajaDomingo === 'SI') ? 'NO' : 'SI';
                   $nuevaUnidad->idUnidad = $unidad->idUnidad;
                   $nuevaUnidad->save();
               }
           }
   
           $this->info('Se han creado nuevos registros para cambiar trabajaDomingo correctamente.');
       
    }
}