<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Insertar línea en tabla contadora de consultas
        $f = new \DateTime("now", new \DateTimeZone('America/Los_Angeles'));
        DB::table('contadorconsultas')->insert([
            'fecha' => $f->format('Y-m-d'),
            'contador' => 0,
        ]);
    }
}
