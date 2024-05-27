<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\formacionunidades;
use Illuminate\Console\Command;
use App\Models\castigo;
use Illuminate\Support\Facades\Log;
use DB;

class ReestablecerDatosDiarios extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-daily-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reestablecer los datos de varias columas de la tabla FormacionUnidades';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Obtener la fecha actual
        $currentDate = Carbon::today()->toDateString();

        // Obtener las unidades afectadas que tenían un castigo
        $unidadesConCastigos = FormacionUnidades::whereNotNull('idCastigo')
                                        ->pluck('idUnidad');

        // Registrar los resultados obtenidos para verificar
        Log::info('Unidades con castigos:', $unidadesConCastigos->toArray());

        DB::transaction(function () use ($unidadesConCastigos) {
            // Actualizar los datos en la tabla formacionunidades
            FormacionUnidades::query()->update([
                'horaEntrada' => null,
                'tipoEntrada' => null,
                'extremo' => null,
                'horaCorte' => null,
                'causa' => null,
                'horaRegreso' => null,
                'ultimaCorrida' => null,
                'horaInicioUC' => null,
                'horaFinUC' => null,
                'idCastigo' => null
            ]);

            // Actualizar los datos en la tabla castigo
            Castigo::query()->update([
                /* 'castigo' => null, */
                'observaciones' => null,
                'horaInicio' => null,
                'horaFin' => null,
                'idUnidad' => null
            ]);

            // Si hay unidades con castigos, actualizarlas
            if ($unidadesConCastigos->isNotEmpty()) {
                $updated = Castigo::whereIn('idUnidad', $unidadesConCastigos)
                    ->update(['idUnidad' => null]);

                // Registrar la cantidad de registros afectados
                Log::info('Número de castigos restablecidos:', ['count' => $updated]);
            } else {
                Log::info('No se encontraron unidades con castigos para restablecer.');
            }
        });

        $this->info('Los datos diarios y los castigos asociados se han restablecido correctamente!');

    }
}