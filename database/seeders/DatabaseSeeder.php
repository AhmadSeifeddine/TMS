<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting database seeding...');

        // Seed in order of dependencies
        $this->call([
            UserSeeder::class,
            ProjectSeeder::class,
            TaskSeeder::class,
            TaskCommentSeeder::class,
        ]);

        $this->command->info('✅ Database seeding completed successfully!');
        $this->command->info('');
        $this->command->info('📊 Summary:');
        $this->command->info('- 10 Users created with different roles (admin, creator, assignee, member)');
        $this->command->info('- 5 Projects created with team assignments');
        $this->command->info('- ~35 Tasks created across all projects');
        $this->command->info('- ~150 Comments created for tasks');
        $this->command->info('- All users have default profile images');
        $this->command->info('');
        $this->command->info('🔑 Login credentials: All users have password "password"');
        $this->command->info('🌐 All emails use @company.com domain');
    }
}
