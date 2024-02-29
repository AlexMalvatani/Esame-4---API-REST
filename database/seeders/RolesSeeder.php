<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    public function run()
    {
        // Inserisci i ruoli desiderati
        DB::table('roles')->insert([
            ['name' => 'admin'],
            ['name' => 'user'],
            ['name' => 'guest'],
            // Aggiungi altri ruoli se necessario
        ]);
    }
}
