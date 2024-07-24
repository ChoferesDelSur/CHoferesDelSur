<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\empresa;

class EmpresaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $empresa = [
            "Choferes del Sur",
            "Urbanos de Oaxaca",
        ];
        foreach($empresa as $empresa){
            empresa::create(['empresa'=> $empresa]);
        }
    }
}
