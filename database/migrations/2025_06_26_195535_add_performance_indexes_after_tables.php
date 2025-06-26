<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Index for search functionality
            $table->index('name', 'projects_name_index');
            $table->index('status', 'projects_status_index');
            $table->index('created_by', 'projects_created_by_index');
            $table->index('created_at', 'projects_created_at_index');

            // Composite index for common queries (created_by + status)
            $table->index(['created_by', 'status'], 'projects_creator_status_index');

            // Composite index for search + sort queries
            $table->index(['status', 'created_at'], 'projects_status_created_index');
        });

        Schema::table('users', function (Blueprint $table) {
            // Index for search functionality and role-based queries
            $table->index('name', 'users_name_index');
            $table->index('email', 'users_email_index');
        });

        Schema::table('project_user', function (Blueprint $table) {
            // Additional indexes for team management queries
            $table->index('project_id', 'project_user_project_index');
            $table->index('user_id', 'project_user_user_index');

            // Composite index for efficient lookups
            $table->index(['project_id', 'user_id'], 'project_user_lookup_index');
        });

        Schema::table('tasks', function (Blueprint $table) {
            // Indexes for task-related queries
            $table->index('project_id', 'tasks_project_index');
            $table->index('assigned_to', 'tasks_assigned_index');
            $table->index('status', 'tasks_status_index');
            $table->index('created_by', 'tasks_created_by_index');

            // Composite indexes for common queries
            $table->index(['project_id', 'status'], 'tasks_project_status_index');
            $table->index(['assigned_to', 'status'], 'tasks_assignee_status_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex('projects_name_index');
            $table->dropIndex('projects_status_index');
            $table->dropIndex('projects_created_by_index');
            $table->dropIndex('projects_created_at_index');
            $table->dropIndex('projects_creator_status_index');
            $table->dropIndex('projects_status_created_index');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_name_index');
            $table->dropIndex('users_email_index');
        });

        Schema::table('project_user', function (Blueprint $table) {
            $table->dropIndex('project_user_project_index');
            $table->dropIndex('project_user_user_index');
            $table->dropIndex('project_user_lookup_index');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex('tasks_project_index');
            $table->dropIndex('tasks_assigned_index');
            $table->dropIndex('tasks_status_index');
            $table->dropIndex('tasks_created_by_index');
            $table->dropIndex('tasks_project_status_index');
            $table->dropIndex('tasks_assignee_status_index');
        });
    }
};
