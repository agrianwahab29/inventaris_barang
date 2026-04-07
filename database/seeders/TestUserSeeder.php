<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds for testing.
     *
     * @return void
     */
    public function run()
    {
        // Create admin user for testing
        User::create([
            'name' => 'Admin Test',
            'username' => 'admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // Create regular pengguna user for testing
        User::create([
            'name' => 'Pengguna Test',
            'username' => 'pengguna',
            'email' => 'pengguna@test.com',
            'password' => Hash::make('pengguna123'),
            'role' => 'pengguna',
        ]);
    }
}
