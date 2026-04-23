<?php

// FILE: database/seeders/CitySeeder.php
// ============================================================
namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            'Zagazig', 'Minya El Qamh', 'Qenayat', 'Belbeis',
            'Faqous', 'Hehia', 'Abu Kebir', 'Kafr Saqr',
            'El-Hussainiya', 'Diarb Negm',
        ];

        foreach ($cities as $city) {
            City::firstOrCreate(['name' => $city]);
        }
    }
}


// ============================================================