<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EpisodesSeeder extends Seeder
{
    public function run()
    {
        // Season 1 - Animals From Space
        DB::table('episodes')->insert([
            'number' => 1,
            'title' => 'Episode 1',
            'description' => 'Description for Episode 1 of Animals From Space - Season 1',
            'season_id' => 1,
        ]);

        DB::table('episodes')->insert([
            'number' => 2,
            'title' => 'Episode 2',
            'description' => 'Description for Episode 2 of Animals From Space - Season 1',
            'season_id' => 1,
        ]);

        // Season 2 - Animals From Space
        DB::table('episodes')->insert([
            'number' => 1,
            'title' => 'Episode 1',
            'description' => 'Description for Episode 1 of Animals From Space - Season 2',
            'season_id' => 2,
        ]);

        DB::table('episodes')->insert([
            'number' => 2,
            'title' => 'Episode 2',
            'description' => 'Description for Episode 2 of Animals From Space - Season 2',
            'season_id' => 2,
        ]);

        // Season 1 - The Black Viper
        DB::table('episodes')->insert([
            'number' => 1,
            'title' => 'Episode 1',
            'description' => 'Description for Episode 1 of The Black Viper - Season 1',
            'season_id' => 3,
        ]);

        DB::table('episodes')->insert([
            'number' => 2,
            'title' => 'Episode 2',
            'description' => 'Description for Episode 2 of The Black Viper - Season 1',
            'season_id' => 3,
        ]);

        // Season 1 - Space Invaders
        DB::table('episodes')->insert([
            'number' => 1,
            'title' => 'Episode 1',
            'description' => 'Description for Episode 1 of Space Invaders - Season 1',
            'season_id' => 4,
        ]);

        DB::table('episodes')->insert([
            'number' => 2,
            'title' => 'Episode 2',
            'description' => 'Description for Episode 2 of Space Invaders - Season 1',
            'season_id' => 4,
        ]);

        // Season 2 - Space Invaders
        DB::table('episodes')->insert([
            'number' => 1,
            'title' => 'Episode 1',
            'description' => 'Description for Episode 1 of Space Invaders - Season 2',
            'season_id' => 5,
        ]);

        DB::table('episodes')->insert([
            'number' => 2,
            'title' => 'Episode 2',
            'description' => 'Description for Episode 2 of Space Invaders - Season 2',
            'season_id' => 5,
        ]);

        // Season 3 - Space Invaders
        DB::table('episodes')->insert([
            'number' => 1,
            'title' => 'Episode 1',
            'description' => 'Description for Episode 1 of Space Invaders - Season 3',
            'season_id' => 6,
        ]);

        DB::table('episodes')->insert([
            'number' => 2,
            'title' => 'Episode 2',
            'description' => 'Description for Episode 2 of Space Invaders - Season 3',
            'season_id' => 6,
        ]);
    }
}