<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TaskSeeder extends Seeder
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

        $projects = Project::with('users')->get();

        foreach ($projects as $project) {
            // Get users assigned to this project
            $projectUsers = $project->users;

            // If no users assigned to project, skip creating tasks for this project
            if ($projectUsers->isEmpty()) {
                $this->command->warn("Project '{$project->name}' has no assigned users. Skipping task creation.");
                continue;
            }

            // Create 6-8 tasks per project with specific assignments
            $taskCount = rand(6, 8);
            $taskData = $this->getTaskDataForProject($project->name);

            for ($i = 0; $i < $taskCount; $i++) {
                $taskInfo = $taskData[$i % count($taskData)];

                // Ensure assignee gets assigned to tasks across all projects they're in
                // And ensure admin and creator also get some task assignments
                $assignedUser = $this->getAssignedUser($projectUsers, $i, $assignee, $admin, $creator, $project);

                Task::create([
                    'title' => $taskInfo['title'],
                    'description' => $taskInfo['description'],
                    'status' => $taskInfo['status'],
                    'priority' => $taskInfo['priority'],
                    'due_date' => $taskInfo['due_date'],
                    'project_id' => $project->id,
                    'assigned_to' => $assignedUser->id,
                    'created_by' => $project->created_by, // Project creator creates the tasks
                ]);
            }
        }

        // Get task counts for summary
        $adminTasks = Task::where('assigned_to', $admin->id)->count();
        $creatorTasks = Task::where('assigned_to', $creator->id)->count();
        $assigneeTasks = Task::where('assigned_to', $assignee->id)->count();
        $memberTasks = Task::where('assigned_to', $member->id)->count();

        $this->command->info('Created tasks for all projects with specific assignments:');
        $this->command->info("- Admin has {$adminTasks} tasks assigned");
        $this->command->info("- Creator has {$creatorTasks} tasks assigned");
        $this->command->info("- Assignee has {$assigneeTasks} tasks assigned");
        $this->command->info("- Member has {$memberTasks} tasks assigned");
    }

    private function getAssignedUser($projectUsers, $taskIndex, $assignee, $admin, $creator, $project)
    {
        // Ensure assignee gets priority for task assignments when they're in the project
        if ($projectUsers->contains($assignee)) {
            // Give assignee 50% of tasks, distribute rest among other project members
            if ($taskIndex % 2 === 0) {
                return $assignee;
            }
        }

        // For admin-created projects, admin can still get tasks assigned even if not explicitly in project
        if ($project->created_by === $admin->id && $taskIndex % 3 === 1) {
            return $admin;
        }

        // For other tasks, prioritize creator if they're in the project
        $otherUsers = $projectUsers->reject(function($user) use ($assignee) {
            return $user->id === $assignee->id;
        });

        if ($otherUsers->contains($creator) && $taskIndex % 3 === 2) {
            return $creator;
        }

        // Otherwise assign to a random project member
        return $projectUsers->random();
    }

    private function getTaskDataForProject($projectName): array
    {
        $tasks = [
            // E-commerce Platform tasks
            'E-commerce Platform' => [
                ['title' => 'Design Product Catalog UI', 'description' => 'Create responsive design for product catalog with filters and search functionality.', 'status' => 'completed', 'priority' => 'high'],
                ['title' => 'Implement Payment Gateway', 'description' => 'Integrate Stripe payment gateway with secure transaction handling.', 'status' => 'in_progress', 'priority' => 'high'],
                ['title' => 'Setup Inventory Management', 'description' => 'Build inventory tracking system with low stock alerts.', 'status' => 'pending', 'priority' => 'medium'],
                ['title' => 'Customer Authentication System', 'description' => 'Implement secure user registration and login with email verification.', 'status' => 'completed', 'priority' => 'high'],
                ['title' => 'Shopping Cart Functionality', 'description' => 'Develop shopping cart with persistent storage and quantity management.', 'status' => 'in_progress', 'priority' => 'medium'],
                ['title' => 'Order Management Dashboard', 'description' => 'Create admin dashboard for order processing and status updates.', 'status' => 'pending', 'priority' => 'medium'],
                ['title' => 'Product Review System', 'description' => 'Build customer review and rating system for products.', 'status' => 'pending', 'priority' => 'low'],
                ['title' => 'Email Notification Service', 'description' => 'Setup automated email notifications for order confirmations and updates.', 'status' => 'pending', 'priority' => 'low'],
            ],
            // Mobile App Development tasks
            'Mobile App Development' => [
                ['title' => 'UI/UX Design for Mobile App', 'description' => 'Create modern and intuitive mobile app interface design.', 'status' => 'completed', 'priority' => 'high'],
                ['title' => 'User Authentication Flow', 'description' => 'Implement secure login/registration with biometric support.', 'status' => 'in_progress', 'priority' => 'high'],
                ['title' => 'Offline Data Synchronization', 'description' => 'Develop offline sync capability for seamless user experience.', 'status' => 'in_progress', 'priority' => 'medium'],
                ['title' => 'Push Notification Setup', 'description' => 'Integrate push notification service for real-time updates.', 'status' => 'pending', 'priority' => 'medium'],
                ['title' => 'Task Management Features', 'description' => 'Build core task creation, editing, and management functionality.', 'status' => 'in_progress', 'priority' => 'high'],
                ['title' => 'Cross-platform Testing', 'description' => 'Comprehensive testing on iOS and Android devices.', 'status' => 'pending', 'priority' => 'medium'],
                ['title' => 'App Store Submission', 'description' => 'Prepare and submit app to Apple App Store and Google Play Store.', 'status' => 'pending', 'priority' => 'low'],
                ['title' => 'Performance Optimization', 'description' => 'Optimize app performance and reduce battery consumption.', 'status' => 'pending', 'priority' => 'low'],
            ],
            // Data Analytics Dashboard tasks
            'Data Analytics Dashboard' => [
                ['title' => 'Database Schema Design', 'description' => 'Design optimized database schema for analytics data storage.', 'status' => 'completed', 'priority' => 'high'],
                ['title' => 'Data Visualization Components', 'description' => 'Create interactive charts and graphs using D3.js or Chart.js.', 'status' => 'in_progress', 'priority' => 'high'],
                ['title' => 'Real-time Data Processing', 'description' => 'Implement real-time data ingestion and processing pipeline.', 'status' => 'in_progress', 'priority' => 'medium'],
                ['title' => 'Custom Report Builder', 'description' => 'Build drag-and-drop report builder for business users.', 'status' => 'pending', 'priority' => 'medium'],
                ['title' => 'User Access Control', 'description' => 'Implement role-based access control for different dashboard views.', 'status' => 'pending', 'priority' => 'high'],
                ['title' => 'Export Functionality', 'description' => 'Add export capabilities for reports in PDF, Excel, and CSV formats.', 'status' => 'pending', 'priority' => 'low'],
                ['title' => 'Performance Monitoring', 'description' => 'Setup monitoring for dashboard performance and query optimization.', 'status' => 'pending', 'priority' => 'low'],
                ['title' => 'Data Security Audit', 'description' => 'Conduct security audit for sensitive data handling and access.', 'status' => 'pending', 'priority' => 'medium'],
            ],
            // API Integration System tasks
            'API Integration System' => [
                ['title' => 'Microservices Architecture Setup', 'description' => 'Design and implement microservices architecture foundation.', 'status' => 'completed', 'priority' => 'high'],
                ['title' => 'API Gateway Configuration', 'description' => 'Setup API gateway with rate limiting and authentication.', 'status' => 'completed', 'priority' => 'high'],
                ['title' => 'Third-party API Integrations', 'description' => 'Integrate payment, shipping, and communication APIs.', 'status' => 'completed', 'priority' => 'medium'],
                ['title' => 'Caching Strategy Implementation', 'description' => 'Implement Redis caching for improved performance.', 'status' => 'completed', 'priority' => 'medium'],
                ['title' => 'Error Handling and Logging', 'description' => 'Setup comprehensive error handling and logging system.', 'status' => 'completed', 'priority' => 'medium'],
                ['title' => 'API Documentation', 'description' => 'Create comprehensive API documentation using Swagger.', 'status' => 'completed', 'priority' => 'low'],
                ['title' => 'Load Testing', 'description' => 'Perform load testing to ensure system scalability.', 'status' => 'completed', 'priority' => 'low'],
                ['title' => 'Production Deployment', 'description' => 'Deploy system to production with monitoring and alerts.', 'status' => 'completed', 'priority' => 'medium'],
            ],
            // Customer Support Portal tasks
            'Customer Support Portal' => [
                ['title' => 'Ticketing System Design', 'description' => 'Design ticketing system with priority and category management.', 'status' => 'completed', 'priority' => 'high'],
                ['title' => 'Live Chat Integration', 'description' => 'Integrate real-time chat functionality for customer support.', 'status' => 'completed', 'priority' => 'high'],
                ['title' => 'Knowledge Base CMS', 'description' => 'Build content management system for knowledge base articles.', 'status' => 'completed', 'priority' => 'medium'],
                ['title' => 'Agent Dashboard', 'description' => 'Create support agent dashboard with ticket management tools.', 'status' => 'completed', 'priority' => 'medium'],
                ['title' => 'Customer Satisfaction Survey', 'description' => 'Implement post-resolution satisfaction survey system.', 'status' => 'completed', 'priority' => 'low'],
                ['title' => 'Escalation Workflow', 'description' => 'Setup automatic escalation workflow for high-priority tickets.', 'status' => 'completed', 'priority' => 'medium'],
                ['title' => 'Reporting and Analytics', 'description' => 'Build reporting system for support metrics and performance.', 'status' => 'completed', 'priority' => 'low'],
                ['title' => 'Mobile Support App', 'description' => 'Develop mobile app for support agents to handle tickets on-the-go.', 'status' => 'completed', 'priority' => 'low'],
            ],
        ];

        $projectTasks = $tasks[$projectName] ?? [];

        // Add due dates to tasks
        foreach ($projectTasks as &$task) {
            $dueDays = rand(1, 30);
            $task['due_date'] = $task['status'] === 'completed'
                ? Carbon::now()->subDays(rand(1, 15))
                : Carbon::now()->addDays($dueDays);
        }

        return $projectTasks;
    }
}
