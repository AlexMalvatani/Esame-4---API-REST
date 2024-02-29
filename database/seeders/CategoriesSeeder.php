<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Popola la tabella categories con dati fittizi
        DB::table('categories')->insert([
            [
                'name' => 'Fantasy',
                'description' => 'Category for Fantasy movies and TV shows.',
            ],
            [
                'name' => 'Horror',
                'description' => 'Category for Horror movies and TV shows.',
            ],
            [
                'name' => 'Sci-Fi',
                'description' => 'Category for Sci-Fi movies and TV shows.',
            ],
            // Puoi aggiungere altre categorie se necessario
        ]);
    }
}
