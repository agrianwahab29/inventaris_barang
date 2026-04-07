<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class FreshInstallSeeder extends Seeder
{
    /**
     * Run the database seeds for fresh installation.
     * 
     * This seeder is designed for single-admin system deployment.
     * Only creates admin user, no dummy data.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('🚀 Starting Fresh Install Seeder...');
        $this->command->info('📋 Single-Admin System Configuration');
        
        // Create single admin user
        $this->createAdmin();
        
        $this->command->info('');
        $this->command->info('✅ Fresh installation completed!');
        $this->command->info('');
        $this->command->info('🔐 Admin Login Credentials:');
        $this->command->info('   Username: admin');
        $this->command->info('   Password: admin123');
        $this->command->info('');
        $this->command->info('💡 Tips:');
        $this->command->info('   - Change default password after first login');
        $this->command->info('   - This is a single-admin system');
        $this->command->info('   - 32 employees can view reports without login');
    }
    
    private function createAdmin()
    {
        $this->command->info('👤 Creating admin user...');
        
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status' => 'aktif',
            ]
        );
        
        $this->command->info('✅ Admin user created successfully');
    }
}
