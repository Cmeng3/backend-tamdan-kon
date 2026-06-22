<?php

namespace Database\Seeders;

use App\Models\Attendance;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $attendances = [
            // ['student_id' => 3,],
            // ['student_id' => 4,],
            ['student_id' => 5,],
            ['student_id' => 6,],
            ['student_id' => 1,],
            ['student_id' => 2,],
            ['student_id' => 7,],
        ];
        //
        foreach($attendances as $attendance){
            Attendance::create($attendance);
        }
    }
}
