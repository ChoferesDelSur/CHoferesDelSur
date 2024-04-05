<?php

namespace Database\Seeders;

use App\Models\tipoOperador;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipoOperadorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipoOperador = [
            "Base",
            "Eventual",
        ];
        foreach($tipoOperador as $tipOperador){
            tipoOperador::create(['tipOperador'=> $tipOperador]);
        }
    }
}
