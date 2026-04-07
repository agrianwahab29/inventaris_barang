<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UpdateAdminPasswordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Update password admin menjadi admin123
        $user = User::where('username', 'admin')->first();
        
        if ($user) {
            $user->password = Hash::make('admin123');
            $user->save();
            $this->command->info('✅ Password admin berhasil diubah menjadi: admin123');
        } else {
            // Jika user admin tidak ada, buat baru
            User::create([
                'name' => 'Administrator',
                'username' => 'admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]);
            $this->command->info('✅ User admin berhasil dibuat dengan password: admin123');
        }
    }
}
