<?php

declare(strict_types=1);
// ============================================================
// FILE: database/factories/CityFactory.php
// ============================================================
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CityFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'Zagazig', 'Minya El Qamh', 'Qenayat',
                'Belbeis', 'Faqous', 'Hehia', 'Abu Kebir',
            ]),
        ];
    }
}
