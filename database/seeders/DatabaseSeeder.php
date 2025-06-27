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
        $this->command->info('🌱 Starting database seeding with test accounts...');

        // Seed in order of dependencies
        $this->call([
            UserSeeder::class,
            ProjectSeeder::class,
            TaskSeeder::class,
            TaskCommentSeeder::class,
        ]);

        $this->command->info('✅ Database seeding completed successfully!');
        $this->command->info('');
        $this->command->info('📊 Test Data Summary:');
        $this->command->info('╔══════════════════════════════════════════════════════════╗');
        $this->command->info('║                    TEST ACCOUNTS                         ║');
        $this->command->info('╠══════════════════════════════════════════════════════════╣');
        $this->command->info('║ Admin Account:    admin@company.com    | adminadmin       ║');
        $this->command->info('║ Creator Account:  creator@company.com  | creatorcreator   ║');
        $this->command->info('║ Assignee Account: assignee@company.com | assignee         ║');
        $this->command->info('║ Member Account:   member@company.com   | membermember     ║');
        $this->command->info('╚══════════════════════════════════════════════════════════╝');
        $this->command->info('');
        $this->command->info('📈 Data Created:');
        $this->command->info('- 4 Test users with different roles and specific credentials');
        $this->command->info('- 5 Projects with realistic descriptions and team assignments');
        $this->command->info('- ~35 Tasks distributed across projects with proper assignments');
        $this->command->info('- ~100+ Comments from all user types with role-specific content');
        $this->command->info('- Profile images generated for all users');
        $this->command->info('');
        $this->command->info('🎯 Test Scenarios:');
        $this->command->info('- Admin: Has own projects, assigned tasks, and oversight comments');
        $this->command->info('- Creator: Has own projects, assigned to others, has tasks and comments');
        $this->command->info('- Assignee: Assigned to multiple projects and tasks, active commenter');
        $this->command->info('- Member: Basic account with limited access (role constraints apply)');
        $this->command->info('');
        $this->command->info('⚠️  Note: Members cannot be assigned to projects due to role constraints');
        $this->command->info('🌐 Ready to test! All accounts use @company.com domain');
    }
}
