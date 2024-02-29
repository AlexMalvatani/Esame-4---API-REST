<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TvSeriesSeeder extends Seeder
{
    public function run()
    {
        // Popola la tabella tvseries con dati fittizi e associa le categorie
        DB::table('tv_series')->insert([
            [
                'title' => 'Animals From Space',
                'description' => 'Description for TV Series 1.',
                'actors' => 'Actor A, Actor B',
                'director' => 'Director X',
                'year' => 2020,
                'category_id' => 1, // ID della categoria Fantasy
            ],
            [
                'title' => 'The Black Viper',
                'description' => 'Description for TV Series 2.',
                'actors' => 'Actor C, Actor D',
                'director' => 'Director Y',
                'year' => 2018,
                'category_id' => 2, // ID della categoria Horror
            ],
            [
                'title' => 'Space Invaders',
                'description' => 'Description for TV Series 3.',
                'actors' => 'Actor E, Actor F',
                'director' => 'Director Z',
                'year' => 2019,
                'category_id' => 3, // ID della categoria Sci-Fi
            ],
            
        ]);
    }
}
