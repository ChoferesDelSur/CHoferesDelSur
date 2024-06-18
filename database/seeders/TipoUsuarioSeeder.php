<?php

namespace Database\Seeders;

use App\Models\tipoUsuario;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipoUsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipoUsuario = [
            "Administrador",
            "Servicio",
        ];
        foreach($tipoUsuario as $tipoUsuario){
            tipoUsuario::create(['tipoUsuario'=> $tipoUsuario]);
        }
    }
}
