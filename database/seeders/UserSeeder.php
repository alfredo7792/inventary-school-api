<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'names' => 'Jose',
            'surnames' => 'Buenaventura',
            'email' => 'prueba@hotmail.com',
            'password' => Hash::make('12345678'),
            'status' => 1,
            'user_created_at' => 'admin@service.com',
            'user_updated_at' => 'admin@service.com',
            'role_id' => 1
        ]);
    }
}
