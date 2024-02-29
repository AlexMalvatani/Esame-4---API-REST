<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MovieSeeder extends Seeder
{
    public function run()
    {
        // Popola la tabella movies con dati fittizi e associa le categorie
        DB::table('movies')->insert([
            [
                'title' => 'Glamodrama',
                'description' => 'Description for Movie 1.',
                'actors' => 'Actor 1, Actor 2',
                'director' => 'Director 1',
                'year' => 2020,
                'category_id' => 1, // ID della categoria Fantasy
            ],
            [
                'title' => 'Blood Suckers',
                'description' => 'Description for Movie 2.',
                'actors' => 'Actor 3, Actor 4',
                'director' => 'Director 2',
                'year' => 2021,
                'category_id' => 2, // ID della categoria Horror
            ],
            [
                'title' => 'The New World',
                'description' => 'Description for Movie 3.',
                'actors' => 'Actor 5, Actor 6',
                'director' => 'Director 3',
                'year' => 2019,
                'category_id' => 3, // ID della categoria Sci-Fi
            ],
            
        ]);
    }
}
