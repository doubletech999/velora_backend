<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Create main admin user
        User::create([
            'name' => 'System Administrator',
            'email' => 'admin@velora.com',
            'password' => Hash::make('admin123456'),
            'role' => 'admin',
            'language' => 'en',
            'email_verified_at' => now(),
        ]);

        // Create a demo guide user
        $guideUser = User::create([
            'name' => 'Ahmad Al-Masri',
            'email' => 'ahmad.guide@velora.com',
            'password' => Hash::make('guide123456'),
            'role' => 'guide',
            'language' => 'ar',
            'email_verified_at' => now(),
        ]);

        // Create guide profile
        $guideUser->guide()->create([
            'bio' => 'Experienced tour guide specializing in historical sites in Palestine.',
            'languages' => 'Arabic,English',
            'phone' => '+970-599-123456',
            'hourly_rate' => 50.00,
            'is_approved' => true,
        ]);

        // Create demo users
        User::create([
            'name' => 'Sara Johnson',
            'email' => 'sara@example.com',
            'password' => Hash::make('user123456'),
            'role' => 'user',
            'language' => 'en',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'محمد أحمد',
            'email' => 'mohammed@example.com',
            'password' => Hash::make('user123456'),
            'role' => 'user',
            'language' => 'ar',
            'email_verified_at' => now(),
        ]);

        $this->command->info('✅ Admin and demo users created successfully!');
        $this->command->info('👤 Admin Login: admin@velora.com / admin123456');
        $this->command->info('🎯 Guide Login: ahmad.guide@velora.com / guide123456');
    }
}
