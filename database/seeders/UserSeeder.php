<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $users = [


             [
                'first_name' => 'aghiad',
                'last_name' => 'asaad',
                'phone' => '1',
                'password' => Hash::make('12345678'),
                'status' => 'approved',
                'birth_date' => '1985-06-15',
                'id_photo_path'=> 'users/id_photos/1.jpg',
                'personal_photo_path'=>'users/personal_photos/1.jpg',
            ],
            [
                'first_name' => 'rida',
                'last_name' => 'beshli',
                'phone' => '2',
                'password' => Hash::make('12345678'),
                'status' => 'approved',
                'birth_date' => '1990-03-22',
                  'id_photo_path'=> 'users/id_photos/2.jpg',
                'personal_photo_path'=>'users/personal_photos/2.jpg',
            ],
           
            [
                'first_name' => 'moayad',
                'last_name' => 'almons',
                'phone' => '3',
                'password' => Hash::make('12345678'),
                'status' => 'pending',
                'birth_date' => '1993-11-08',
                'id_photo_path'=> 'users/id_photos/3.jpg',
                'personal_photo_path'=>'users/personal_photos/3.jpg',

            ],
            [
                'first_name' => 'mohammad',
                'last_name' => 'alyousif',
                'phone' => '4',
                'password' => Hash::make('12345678'),
                'status' => 'pending',
                'birth_date' => '1988-09-30',
                'id_photo_path'=> 'users/id_photos/4.jpg',
                'personal_photo_path'=>'users/personal_photos/4.jpg',
            ],
            [
                'first_name' => 'hussain',
                'last_name' => 'alwasty',
                'phone' => '5',
                'password' => Hash::make('12345678'),
                'status' => 'approved',
                'birth_date' => '1995-02-14',
                'id_photo_path'=> 'users/id_photos/5.jpg',
                'personal_photo_path'=>'users/personal_photos/5.jpg',
            ]
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }
        
    }
}
