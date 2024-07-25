<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@admin.com',
            'phone' => '082121212121',
            'password' => Hash::make('admin'),
            'role' => Role::ADMIN->status(),
        ]);

        User::factory()->create([
            'name' => 'Gestyan R',
            'email' => 'gestyanramadhan@gmail.com',
            'phone' => '082121212121',
            'password' => Hash::make('admin'),
            'role' => Role::ADMIN->status(),
        ]);

        User::factory()->create([
            'name' => 'IPDS 1111',
            'email' => 'ipdsbps1111@gmail.com',
            'phone' => '082121212121',
            'password' => Hash::make('admin'),
            'role' => Role::STAFF->status(),
        ]);
    }
}
