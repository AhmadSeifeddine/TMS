<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the 4 specific test accounts
        $admin = User::where('email', 'admin@company.com')->first();
        $creator = User::where('email', 'creator@company.com')->first();
        $assignee = User::where('email', 'assignee@company.com')->first();
        $member = User::where('email', 'member@company.com')->first();

        // Project 1: Admin's E-commerce Platform
        $project1 = Project::create([
            'name' => 'E-commerce Platform',
            'description' => 'A comprehensive e-commerce platform with payment gateway integration, inventory management, and customer support features. This project showcases admin capabilities in managing large-scale applications.',
            'status' => 'active',
            'created_by' => $admin->id,
        ]);

        // Assign creator and assignee to admin's project (admin has system-wide access, no need to assign)
        $project1->users()->attach([$creator->id, $assignee->id]);

        // Project 2: Creator's Mobile App Development
        $project2 = Project::create([
            'name' => 'Mobile App Development',
            'description' => 'Cross-platform mobile application for task management with real-time notifications and offline sync. A modern approach to productivity applications.',
            'status' => 'active',
            'created_by' => $creator->id,
        ]);

        // Assign assignee to creator's project
        $project2->users()->attach([$assignee->id]);

        // Project 3: Creator's Data Analytics Dashboard
        $project3 = Project::create([
            'name' => 'Data Analytics Dashboard',
            'description' => 'Business intelligence dashboard with advanced reporting capabilities and data visualization. Helping businesses make data-driven decisions.',
            'status' => 'active',
            'created_by' => $creator->id,
        ]);

        // Assign assignee to creator's second project (admin has system access, no need to assign)
        $project3->users()->attach([$assignee->id]);

        // Project 4: Admin's API Integration System (Completed)
        $project4 = Project::create([
            'name' => 'API Integration System',
            'description' => 'Microservices architecture for third-party API integrations with rate limiting and caching. A robust system for handling multiple API connections.',
            'status' => 'completed',
            'created_by' => $admin->id,
        ]);

        // Assign creator and assignee to admin's completed project
        $project4->users()->attach([$creator->id, $assignee->id]);

        // Project 5: Admin's Customer Support Portal (Archived)
        $project5 = Project::create([
            'name' => 'Customer Support Portal',
            'description' => 'Comprehensive customer support system with ticketing, live chat, and knowledge base. Providing excellent customer service experience.',
            'status' => 'archived',
            'created_by' => $admin->id,
        ]);

        // Assign creator to admin's archived project
        $project5->users()->attach([$creator->id]);

        $this->command->info('Created 5 projects with specific test data for the 4 test accounts.');
        $this->command->info('- Admin created 3 projects with system-wide access (no explicit assignment needed)');
        $this->command->info('- Creator created 2 projects and is assigned to 3 admin projects');
        $this->command->info('- Assignee is assigned to 4 projects');
        $this->command->info('- Member has no project assignments (role constraint prevents assignment)');
    }
}
