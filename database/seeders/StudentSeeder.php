<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    
    {
        //
        $students = [
            ['name' => 'lihour', 'parent_id' => 1, 'latlng' => '11.562206, 104.851101', 'sex' => 'M', 'bus_id' => 1],
            ['name' => 'sovanlisa', 'parent_id' => 5, 'latlng' => '11.587098,104.844813', 'sex' => 'F', 'bus_id' => 1],
            ['name' => 'soksan', 'parent_id' => 6, 'latlng' => '11.544286, 104.892245', 'sex' => 'F', 'bus_id' => 1],
            ['name' => 'vorlek', 'parent_id' => 7, 'latlng' => '11.5607913,104.8903696', 'sex' => 'F', 'bus_id' => 1],
            ['name' => 'sompitou', 'parent_id' => 8, 'latlng' => '11.546562, 104.906830', 'sex' => 'M', 'bus_id' => 1],
            ['name' => 'chhay Yuth', 'parent_id' => 9, 'latlng' => '11.541641, 104.914120', 'sex' => 'F', 'bus_id' => 1],
            ['name' => 'heng lita', 'parent_id' => 10, 'latlng' => '11.562339, 104.840870', 'sex' => 'F', 'bus_id' => 1],
        ];
        //
        foreach($students as $student){
            Student::create($student);
        }
    
    }
}
