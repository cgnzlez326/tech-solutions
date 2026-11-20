<?php

namespace Database\Seeders;

use App\Models\Proyecto;
use Illuminate\Database\Seeder;

class ProyectoSeeder extends Seeder
{
    public function run(): void
    {
        Proyecto::getProyectos();
    }
}
