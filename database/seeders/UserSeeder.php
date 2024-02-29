<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Creazione di un utente admin
        DB::table('users')->insert([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        // Creazione di un utente user
        DB::table('users')->insert([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
        ]);

        // Creazione di un utente guest
        DB::table('users')->insert([
            'name' => 'Guest User',
            'email' => 'guest@example.com',
            'password' => Hash::make('password'),
        ]);
    }
}
