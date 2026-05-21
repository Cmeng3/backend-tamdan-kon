<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('chat1')->insert([
            ['message'=>'does your child come today!', 'user_id'=>2, 'created_at'=>now(), 'updated_at'=>now()],
            ['message'=>'No he dosn\'t attend the school', 'user_id'=>1, 'created_at'=>now(), 'updated_at'=>now()]
        ]);
    }
}
