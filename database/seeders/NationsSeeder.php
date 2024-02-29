<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $file = fopen(storage_path('app/csv/nazioni.csv'), 'r');

        while (($data = fgetcsv($file)) !== false) {
            DB::table('nations')->insert([
                'name' => $data[1],
                'continent' => $data[2],
                'iso_code' => $data[3],
                'iso3_code' => $data[4],
                'phone_code' => $data[5],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        fclose($file);
    }
}
