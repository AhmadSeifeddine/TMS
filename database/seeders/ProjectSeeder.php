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
        $creators = User::whereIn('role', ['admin', 'creator'])->get();
        // Only creators and assignees can be project members (no 'member' role users)
        $assignableUsers = User::whereIn('role', ['creator', 'assignee'])->get();

        // Project 1: E-commerce Platform
        $project1 = Project::create([
            'name' => 'E-commerce Platform',
            'description' => 'A comprehensive e-commerce platform with payment gateway integration, inventory management, and customer support features.',
            'status' => 'active',
            'created_by' => $creators->first()->id,
        ]);

        // Assign team members to project 1 (only creators and assignees)
        $project1->users()->attach($assignableUsers->random(3)->pluck('id')->toArray());

        // Project 2: Mobile App Development
        $project2 = Project::create([
            'name' => 'Mobile App Development',
            'description' => 'Cross-platform mobile application for task management with real-time notifications and offline sync.',
            'status' => 'active',
            'created_by' => $creators->skip(1)->first()->id,
        ]);

        // Assign team members to project 2 (only creators and assignees)
        $project2->users()->attach($assignableUsers->random(4)->pluck('id')->toArray());

        // Project 3: Data Analytics Dashboard
        $project3 = Project::create([
            'name' => 'Data Analytics Dashboard',
            'description' => 'Business intelligence dashboard with advanced reporting capabilities and data visualization.',
            'status' => 'active',
            'created_by' => $creators->first()->id,
        ]);

        // Assign team members to project 3 (only creators and assignees)
        $project3->users()->attach($assignableUsers->random(2)->pluck('id')->toArray());

        // Project 4: API Integration System
        $project4 = Project::create([
            'name' => 'API Integration System',
            'description' => 'Microservices architecture for third-party API integrations with rate limiting and caching.',
            'status' => 'completed',
            'created_by' => $creators->skip(1)->first()->id,
        ]);

        // Assign team members to project 4 (only creators and assignees)
        $project4->users()->attach($assignableUsers->random(3)->pluck('id')->toArray());

        // Project 5: Customer Support Portal
        $project5 = Project::create([
            'name' => 'Customer Support Portal',
            'description' => 'Comprehensive customer support system with ticketing, live chat, and knowledge base.',
            'status' => 'archived',
            'created_by' => $creators->first()->id,
        ]);

        // Assign team members to project 5 (only creators and assignees)
        $project5->users()->attach($assignableUsers->random(2)->pluck('id')->toArray());

        $this->command->info('Created 5 projects with team assignments (creators and assignees only).');
    }
}
