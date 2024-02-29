<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeasonsSeeder extends Seeder
{
    public function run()
    {
        // Popola la tabella seasons con dati fittizi e associa le serie TV
        DB::table('seasons')->insert([
            // Stagioni per la serie TV "Animals From Space" (Fantasy)
            [
                'number' => 1,
                'description' => 'Description for Season 1 of Animals From Space.',
                'tv_series_id' => 1, // ID della serie TV "Animals From Space"
            ],
            [
                'number' => 2,
                'description' => 'Description for Season 2 of Animals From Space.',
                'tv_series_id' => 1, // ID della serie TV "Animals From Space"
            ],

            // Stagioni per la serie TV "The Black Viper" (Horror)
            [
                'number' => 1,
                'description' => 'Description for Season 1 of The Black Viper.',
                'tv_series_id' => 2, // ID della serie TV "The Black Viper"
            ],

            // Stagioni per la serie TV "Space Invaders" (Sci-Fi)
            [
                'number' => 1,
                'description' => 'Description for Season 1 of Space Invaders.',
                'tv_series_id' => 3, // ID della serie TV "Space Invaders"
            ],
            [
                'number' => 2,
                'description' => 'Description for Season 2 of Space Invaders.',
                'tv_series_id' => 3, // ID della serie TV "Space Invaders"
            ],
            [
                'number' => 3,
                'description' => 'Description for Season 3 of Space Invaders.',
                'tv_series_id' => 3, // ID della serie TV "Space Invaders"
            ],
        ]);
    }
}