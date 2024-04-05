<?php

namespace Database\Seeders;

use App\Models\tipoDirectivo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipoDirectivoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipoDirectivo = [
            "Socio",
            "Prestador",
        ];
        foreach($tipoDirectivo as $tipoDirectivo){
            tipoDirectivo::create(['tipoDirectivo'=> $tipoDirectivo]);
        }
    }
}
