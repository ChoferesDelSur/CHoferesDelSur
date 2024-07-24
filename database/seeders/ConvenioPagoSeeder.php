<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\convenioPago;

class ConvenioPagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $convenioPago = [
            "Debe a Socio",
            "Debe Pensión",
            "Debe Pensión y a Socio",
            "No Debe"
        ];
        foreach($convenioPago as $convenioPago){
            convenioPago::create(['convenioPago'=> $convenioPago]);
        }
    }
}
