<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComuniSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $file = fopen(storage_path('app/csv/comuniitaliani.csv'), 'r');

        while (($data = fgetcsv($file)) !== false) {
            DB::table('comuni')->insert([
                'name' => $data[1],
                'region' => $data[2],
                'city' => $data[3],
                'province' => $data[5],
                'province_code' => $data[6],
                'postal_code' => $data[9],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        fclose($file);
    }
}
