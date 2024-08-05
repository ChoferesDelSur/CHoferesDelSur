<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\tipoMovimiento;

class TipoMovimientoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tiposMovimiento = [
            // Alta
            ['tipoMovimiento' => 'Nuevo Ingreso', 'idEstado' => 1],
            ['tipoMovimiento' => 'Reingreso', 'idEstado' => 1],
            ['tipoMovimiento' => 'Cambio de Socio (Alta)', 'idEstado' => 1],
            ['tipoMovimiento' => 'Alta Eventual', 'idEstado' => 1],
            ['tipoMovimiento' => 'Cumplió Sanción', 'idEstado' => 1],
            ['tipoMovimiento' => 'Prestamo', 'idEstado' => 1],
            ['tipoMovimiento' => 'Por Lista', 'idEstado' => 1],
            ['tipoMovimiento' => 'Se Levantó Sanción', 'idEstado' => 1],

            // Baja
            ['tipoMovimiento' => 'Por el Socio', 'idEstado' => 2],
            ['tipoMovimiento' => 'Por el Encargado', 'idEstado' => 2],
            ['tipoMovimiento' => 'No Se Reportó', 'idEstado' => 2],
            ['tipoMovimiento' => 'Sin Unidad', 'idEstado' => 2],
            ['tipoMovimiento' => 'Sin Laborar', 'idEstado' => 2],
            ['tipoMovimiento' => 'Sin Incapacidad', 'idEstado' => 2],
            ['tipoMovimiento' => 'Por la Comisión de Tesorería', 'idEstado' => 2],
            ['tipoMovimiento' => 'Por la Comisión de Servicio', 'idEstado' => 2],
            ['tipoMovimiento' => 'Por la Comisión de Personal', 'idEstado' => 2],
            ['tipoMovimiento' => 'Por la Comisión', 'idEstado' => 2],
            ['tipoMovimiento' => 'Pensión +60 años', 'idEstado' => 2],
            ['tipoMovimiento' => 'No Se Presentó', 'idEstado' => 2],
            ['tipoMovimiento' => 'No Ha Formado', 'idEstado' => 2],
            ['tipoMovimiento' => 'No Agarró Camión', 'idEstado' => 2],
            ['tipoMovimiento' => 'Más De 5 Días Sin Laborar', 'idEstado' => 2],
            ['tipoMovimiento' => 'Levantó Reporte', 'idEstado' => 2],
            ['tipoMovimiento' => 'Incapacidad Permanente', 'idEstado' => 2],
            ['tipoMovimiento' => 'Falta de Pago/Adeudo', 'idEstado' => 2],
            ['tipoMovimiento' => 'Falta de Abono', 'idEstado' => 2],
            ['tipoMovimiento' => 'Baja Eventual', 'idEstado' => 2],
            ['tipoMovimiento' => 'Días Sin Laborar', 'idEstado' => 2],
            ['tipoMovimiento' => 'Cambio de Socio (Baja)', 'idEstado' => 2],
            ['tipoMovimiento' => 'Carro En Taller', 'idEstado' => 2],
            ['tipoMovimiento' => 'Baja Voluntaria', 'idEstado' => 2],
            ['tipoMovimiento' => 'Asunto Personal', 'idEstado' => 2],
        ];

        foreach ($tiposMovimiento as $tipo) {
            tipoMovimiento::create($tipo);
        }
    }
}
