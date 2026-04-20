<?php

namespace Database\Seeders;

use App\Models\Coordinate;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CoordinateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Coordinate::create(
            [
                'latitude' => '11.587092',
                'longitude' => '104.844918',
            ]
        );
    }
}
