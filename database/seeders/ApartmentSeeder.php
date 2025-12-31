<?php

namespace Database\Seeders;

use App\Models\Apartment;
use App\Models\User;
use Illuminate\Database\Seeder;

class ApartmentSeeder extends Seeder
{
    public function run(): void
    {
        $owners = User::where('role', 'user')->get();
        
        if ($owners->isEmpty()) {
            $this->command->error('  UsersTableSeederلا يوجد ملاك في قاعدة البيانات! قم بتشغيل  .');
            return;
        }

        $apartments = [
            [
                'description' => 'شقة فاخرة 3 غرف في العاصمة',
                'governorate' => 'hama',
                'city' => 'hama',
                'price' => 4500,
                'owner_id' => $owners[1]->id, // aghiad 
                'is_available' =>  true,
                'created_at' => now(),
            ],
            [
                'description' => 'شقة عائلية 4 غرف  ',
                'governorate' => 'aleppo',
                'city' => 'hama',
                'price' => 3800,
                'owner_id' => $owners[2]->id, //  aghiad
                'is_available' => true,
                'created_at' => now(),
            ],
            [
                'description' => 'شقة اقتصادية 2 غرفة  ',
                'governorate' => 'daraa',
                'city' => 'daraa',
                'price' => 2000,
                'owner_id' => $owners[3]->id, // rida
                'is_available' => true,
                'created_at' => now(),
            ],
          
        ];

        foreach ($apartments as $apartmentData) {
            Apartment::create($apartmentData);
        }
        
       
    }
}