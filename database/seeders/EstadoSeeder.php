<?php

namespace Database\Seeders;

use App\Models\estado;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EstadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estado = [
            "Alta",
            "Baja",
            "Incapacidad",
        ];
        foreach($estado as $estado){
            estado::create(['estado'=> $estado]);
        }
    }
}
