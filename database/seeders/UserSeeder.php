<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // all default users for db seeding
        $defualt_users = [
            ['name' => 'kunnarak', 'password'=>Hash::make('kunnarak123'), 'email'=>'kunnarak@gmail.com', 'phone_number'=>'016366573'],
            ['name' => 'bunthoun', 'password'=>Hash::make('bunthoun123'), 'email'=>'bunthoun@gmail.com', 'role'=>'driver', 'phone_number'=>'012345678'],
            ['name' => 'chimeng', 'password'=>Hash::make('chimeng123'), 'email'=>'chimeng@gmail.com', 'role'=>'driver', 'phone_number'=>'012555666'],
            ['name' => 'ratana', 'password'=>Hash::make('ratana123'), 'email'=>'ratana@gmail.com', 'role'=>'admin', 'phone_number'=>'016555666'],
            ['name' => 'sovannareach', 'password'=>Hash::make('nareach123'), 'email'=>'nareach@gmail.com', 'phone_number'=>'081366573'],
            ['name' => 'sokthun', 'password'=>Hash::make('sokthun123'), 'email'=>'sokthun@gmail.com', 'phone_number'=>'012789456'],
            ['name' => 'Lekana', 'password'=>Hash::make('lekana123'), 'email'=>'lekana@gmail.com', 'phone_number'=>'012345234'],
            ['name' => 'Somputhy', 'password'=>Hash::make('somputhy123'), 'email'=>'somputhy@gmail.com', 'phone_number'=>'016456345'],
            ['name' => 'Chhayvireak', 'password'=>Hash::make('vireak123'), 'email'=>'vireak@gmail.com', 'phone_number'=>'089896745'],
            ['name' => 'Sereyphea', 'password'=>Hash::make('sereyphea123'), 'email'=>'sereyphea@gmail.com', 'phone_number'=>'070567456'],
        ];
        
        // seeding the database with 3 defualt above users, with create method
        // create() method will auto generate created_at and updated_at, that why we use it instead of insert()
        foreach($defualt_users as $user){
            User::create(
                $user
            );
        }
    }
}
