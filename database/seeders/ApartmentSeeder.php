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
                'title' => 'شقة فاخرة 3 غرف في العاصمة',
                'governorate' => 'hama',
                'city' => 'hama',
                'price' => 4500,
                'bedrooms' => 3,
                'bathrooms' => 2,
                'owner_id' => $owners[1]->id, // aghiad asaad
                'is_available' =>  true,
                'created_at' => now(),
            ],
            [
                'title' => 'شقة عائلية 4 غرف  ',
                'governorate' => 'aleppo',
                'city' => 'hama',
                'price' => 3800,
                'bedrooms' => 4,
                'bathrooms' => 3,
                'owner_id' => $owners[1]->id, //  aghiad
                
                'is_available' => true,
                'created_at' => now(),
            ],
            [
                'title' => 'شقة اقتصادية 2 غرفة  ',
                'governorate' => 'daraa',
                'city' => 'daraa',
                'price' => 2000,
                'bedrooms' => 2,
                'bathrooms' => 1,
                'owner_id' => $owners[1]->id, // rida
                'is_available' => true,
                'created_at' => now(),
            ],
          
        ];

        foreach ($apartments as $apartmentData) {
            Apartment::create($apartmentData);
        }
        
       
    }
}