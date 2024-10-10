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
        $tipoUsuario1 = new tipoUsuario();
        $tipoUsuario1->tipoUsuario = "Administrador";
        $tipoUsuario1->save();

        $tipoUsuario2 = new tipoUsuario();
        $tipoUsuario2->tipoUsuario = "Servicio";
        $tipoUsuario2->save();

        $tipoUsuario3 = new tipoUsuario();
        $tipoUsuario3->tipoUsuario = "RH";
        $tipoUsuario3->save();

        $tipoUsuario4 = new tipoUsuario();
        $tipoUsuario4->tipoUsuario = "Directivo";
        $tipoUsuario4->save();
    }
}
