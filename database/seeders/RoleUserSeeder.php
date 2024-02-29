<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleUserSeeder extends Seeder
{
    public function run()
    {
        // Associa gli utenti ai ruoli
        DB::table('role_user')->insert([
            ['user_id' => 1, 'role_id' => 1], // Utente admin con ruolo admin
            ['user_id' => 2, 'role_id' => 2], // Utente user con ruolo user
            ['user_id' => 3, 'role_id' => 3], // Utente guest con ruolo guest
            // Aggiungi altre associazioni se necessario
        ]);
    }
}
