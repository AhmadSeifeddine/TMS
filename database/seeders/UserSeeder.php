<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the 4 specific test accounts as requested

        // 1. Admin Account
        $admin = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@company.com',
            'password' => Hash::make('adminadmin'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // 2. Creator Account
        $creator = User::create([
            'name' => 'Project Creator',
            'email' => 'creator@company.com',
            'password' => Hash::make('creatorcreator'),
            'role' => 'creator',
            'email_verified_at' => now(),
        ]);

        // 3. Assignee Account
        $assignee = User::create([
            'name' => 'Task Assignee',
            'email' => 'assignee@company.com',
            'password' => Hash::make('assignee'),
            'role' => 'assignee',
            'email_verified_at' => now(),
        ]);

        // 4. Member Account
        $member = User::create([
            'name' => 'Team Member',
            'email' => 'member@company.com',
            'password' => Hash::make('membermember'),
            'role' => 'member',
            'email_verified_at' => now(),
        ]);

        // Add default profile images for all users
        $users = [$admin, $creator, $assignee, $member];

        foreach ($users as $user) {
            $user->addMediaFromUrl('https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&color=7F9CF5&background=EBF4FF&size=200')
                 ->usingFileName('avatar_' . $user->id . '.png')
                 ->toMediaCollection('profile_images');
        }

        $this->command->info('Created 4 test accounts with specific credentials and profile images.');
    }
}
