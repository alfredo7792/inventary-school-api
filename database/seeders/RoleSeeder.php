<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create([
            'name' => 'Administrador',
            'user_created_at' => 'admin@service.com' 
        ]);
        Role::create([
            'name' => 'Administrativo',
            'user_created_at' => 'admin@service.com' 
        ]);
        Role::create([
            'name' => 'Encargado de inventario',
            'user_created_at' => 'admin@service.com' 
        ]);
    }
}
