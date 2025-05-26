<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeederWarung extends Seeder
{
    
    public function run(): void
    {
        $warung = [
            [
                'name' => 'Warung Kabut',
                'latitude' => -6.2000000,
                'longitude' => 106.8166667,
                'rating' => 4.6,
                'price' => 1,
                'accessibility' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('Data_Warungs')->insert($warung);
    }
}
