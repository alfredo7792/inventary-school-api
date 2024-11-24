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
            'names' => 'Jose Breisem',
            'surnames' => 'Torres Villavicencio',
            'email' => 'admin@ierp.edu.pe',
            'password' => Hash::make('12345678'),
            'status' => 1,
            'user_created_at' => 'admin@ierp.edu.pe',
            'user_updated_at' => 'admin@ierp.edu.pe',
            'role_id' => 1
        ]);
    }
}
