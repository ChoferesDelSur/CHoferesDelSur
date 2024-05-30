<?php

namespace Database\Seeders;

use App\Models\tipoUltimaCorrida;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipoUltimaCorridaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipoUltimaCorrida = [
            "BN",
            "UB",
            "UC"
        ];
        foreach($tipoUltimaCorrida as $tipoUltimaCorrida){
            tipoUltimaCorrida::create(['tipoUltimaCorrida'=> $tipoUltimaCorrida]);
        }
    }
}
