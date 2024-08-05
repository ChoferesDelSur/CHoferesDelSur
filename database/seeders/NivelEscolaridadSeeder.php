<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\escolaridad;

class NivelEscolaridadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $escolaridades = [
            "Sin Escolaridad",
            "Primaria Incompleta",
            "Primaria Terminada",
            "Secundaria Incompleta",
            "Secundaria Terminada",
            "Bachillerado Incompleto",
            "Bachillerato Terminado",
            "Técnico Medio",
            "Técnico Superior",
            "Universidad Incompleta",
            "Universidad Terminada (Licenciatura)",
            "Postgrado (Especialidad)",
            "Maestría",
            "Doctorado",
            "Postdoctorado"
        ];
        foreach($escolaridades as $nivel){
            escolaridad::create(['escolaridad'=> $nivel]);
        }
    }
}
