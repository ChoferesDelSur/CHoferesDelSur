<?php

namespace Database\Seeders;

use App\Models\ruta;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RutaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ruta = [
            "LIBRAMIENTO - PLAZA DEL VALLE",
            "ESMERALDA - COL. JARDIN",
            "CRIT - 7 REGIONES",
            "SAN FELIPE - EXPERIMENTAL",
            "1a. ETAPA - FRACC. ESMERALDA",
            "PLAZA DEL VALLE - FORESTAL",
            "CRIT - ISSSTE",
            "GUADALUPE VICTORIA - FORESTAL",
            "PLAZA DEL VALLE - ESMERALDA",
            "LA SOLEDAD - ANIMAS TRUJANO",
            "SAN FELIPE - GUELAGUETZA",
            "BENITO JUAREZ - PLAZA DEL VALLE",
            "PLAZA DEL VALLE - MONJONERA",
            "MOJONERA - AMPL. PROGRESO",
            "PLAZA DEL VALLE - LOS ANGELES",
            "VIGUERA - CECYTE",
            "MANZANA L - DONAJI",
            "VISTA HERMOSA - SANTA CRUZ",
            "VIGUERA - 1a. ETAPA",
            "MOJONERA - COL. MOCTEZUMA",
            "ANAHUAC - VOLCANES",
            "LA JOYA - COL. JARDIN",
            "AGUAYO - ISSSTE",
        ];
        foreach($ruta as $nombreRuta){
            ruta::create(['nombreRuta'=> $nombreRuta]);
        }
    }
}
