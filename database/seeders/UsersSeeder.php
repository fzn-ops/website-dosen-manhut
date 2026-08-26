<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        User::create([
            'NIP' => '1234567890',
            'username' => 'admin',
            'role' => 'admin',
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'profile_picture' => null,
            'password' => Hash::make('admin123'),
        ]);

        User::create([
            'NIP' => '0987654321',
            'username' => 'Fulano',
            'role' => 'dosen',
            'name' => 'Fulano de Tal',
            'email' => 'dosen@example.com',
            'profile_picture' => null,
            'password' => Hash::make('fulano123'),
        ]);
    }
}
