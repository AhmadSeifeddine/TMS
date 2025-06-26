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
        // Create Admin Users
        $admin1 = User::create([
            'name' => 'John Admin',
            'email' => 'john.admin@company.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $admin2 = User::create([
            'name' => 'Sarah Admin',
            'email' => 'sarah.admin@company.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Create Creator Users
        $creator1 = User::create([
            'name' => 'Mike Creator',
            'email' => 'mike.creator@company.com',
            'password' => Hash::make('password'),
            'role' => 'creator',
            'email_verified_at' => now(),
        ]);

        $creator2 = User::create([
            'name' => 'Emma Creator',
            'email' => 'emma.creator@company.com',
            'password' => Hash::make('password'),
            'role' => 'creator',
            'email_verified_at' => now(),
        ]);

        // Create Assignee Users
        $assignee1 = User::create([
            'name' => 'David Assignee',
            'email' => 'david.assignee@company.com',
            'password' => Hash::make('password'),
            'role' => 'assignee',
            'email_verified_at' => now(),
        ]);

        $assignee2 = User::create([
            'name' => 'Lisa Assignee',
            'email' => 'lisa.assignee@company.com',
            'password' => Hash::make('password'),
            'role' => 'assignee',
            'email_verified_at' => now(),
        ]);

        $assignee3 = User::create([
            'name' => 'Tom Assignee',
            'email' => 'tom.assignee@company.com',
            'password' => Hash::make('password'),
            'role' => 'assignee',
            'email_verified_at' => now(),
        ]);

        // Create Member Users
        $member1 = User::create([
            'name' => 'Anna Member',
            'email' => 'anna.member@company.com',
            'password' => Hash::make('password'),
            'role' => 'member',
            'email_verified_at' => now(),
        ]);

        $member2 = User::create([
            'name' => 'Chris Member',
            'email' => 'chris.member@company.com',
            'password' => Hash::make('password'),
            'role' => 'member',
            'email_verified_at' => now(),
        ]);

        $member3 = User::create([
            'name' => 'Julie Member',
            'email' => 'julie.member@company.com',
            'password' => Hash::make('password'),
            'role' => 'member',
            'email_verified_at' => now(),
        ]);

        // Add default profile images for all users
        $users = [$admin1, $admin2, $creator1, $creator2, $assignee1, $assignee2, $assignee3, $member1, $member2, $member3];

        foreach ($users as $user) {
            $user->addMediaFromUrl('https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&color=7F9CF5&background=EBF4FF&size=200')
                 ->usingFileName('avatar_' . $user->id . '.png')
                 ->toMediaCollection('profile_images');
        }

        $this->command->info('Created 10 users with different roles and profile images.');
    }
}
