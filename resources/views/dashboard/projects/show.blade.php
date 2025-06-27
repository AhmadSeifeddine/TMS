<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ $project->name }}
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Created by {{ $project->creator->name }} • {{ $project->created_at->diffForHumans() }}
                </p>
            </div>

            <!-- Project Actions -->
            <div class="flex items-center space-x-2">
                @can('update', $project)
                    <button onclick="openEditModal({{ $project->id }})" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Project
                    </button>
                @endcan

                @can('manageMembers', $project)
                    <button onclick="openManageTeamModal({{ $project->id }}, '{{ $project->name }}')" class="inline-flex items-center px-3 py-2 border border-purple-300 dark:border-purple-600 shadow-sm text-sm leading-4 font-medium rounded-md text-purple-700 dark:text-purple-300 bg-purple-50 dark:bg-purple-900 hover:bg-purple-100 dark:hover:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Manage Team
                    </button>
                @endcan

                @can('createInProject', [App\Models\Task::class, $project])
                    <button onclick="openCreateTaskModal({{ $project->id }}, '{{ addslashes($project->name) }}', {{ json_encode($project->users->merge(collect([$project->creator]))->unique('id')->filter(function($user) { return $user->role !== 'admin'; })->map(function($user) { return ['id' => $user->id, 'name' => $user->name, 'role' => $user->role]; })->values()) }})" class="inline-flex items-center px-3 py-2 border border-green-300 dark:border-green-600 shadow-sm text-sm leading-4 font-medium rounded-md text-green-700 dark:text-green-300 bg-green-50 dark:bg-green-900 hover:bg-green-100 dark:hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Task
                    </button>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Project Overview Section -->
            <div class="mb-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Project Status and Progress -->
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                            <!-- Project Info -->
                            <div class="lg:col-span-2">
                                <div class="flex items-center space-x-3 mb-4">
                                    @php
                                        $statusColors = [
                                            'active' => 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200',
                                            'completed' => 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-200',
                                            'archived' => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200'
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$project->status] }}">
                                        {{ ucfirst($project->status) }}
                                    </span>

                                    @if($overdueTasks->count() > 0)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $overdueTasks->count() }} Overdue
                                        </span>
                                    @endif
                                </div>

                                @if($project->description)
                                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                                        {{ $project->description }}
                                    </p>
                                @endif

                                <!-- Progress Bar -->
                                <div class="mb-4">
                                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-2">
                                        <span>Overall Progress</span>
                                        <span>{{ $progressPercentage }}% ({{ $completedTasks }}/{{ $totalTasks }} tasks)</span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                        <div class="bg-blue-600 h-3 rounded-full transition-all duration-500" style="width: {{ $progressPercentage }}%"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Statistics Cards -->
                            <div class="space-y-4">
                                <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-medium text-blue-700 dark:text-blue-300">Total Tasks</p>
                                            <p class="text-2xl font-semibold text-blue-900 dark:text-blue-100">{{ $totalTasks }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-green-50 dark:bg-green-900 p-4 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-medium text-green-700 dark:text-green-300">Completed</p>
                                            <p class="text-2xl font-semibold text-green-900 dark:text-green-100">{{ $completedTasks }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-purple-50 dark:bg-purple-900 p-4 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-medium text-purple-700 dark:text-purple-300">Team Members</p>
                                            <p class="text-2xl font-semibold text-purple-900 dark:text-purple-100">{{ $project->users->count() }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Team Members -->
                        @if($project->users->count() > 0)
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Team Members</h3>
                                <div class="flex flex-wrap gap-3">
                                    @foreach($project->users as $member)
                                        <div class="flex items-center space-x-2 bg-gray-50 dark:bg-gray-700 px-3 py-2 rounded-lg">
                                            <div class="w-6 h-6 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center">
                                                <span class="text-xs font-medium text-gray-600 dark:text-gray-400">
                                                    {{ substr($member->name, 0, 1) }}
                                                </span>
                                            </div>
                                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $member->name }}</span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 capitalize">{{ $member->role }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tasks Timeline Section -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Project Tasks</h2>

                    <!-- Status Filter Component -->
                    <x-status-filter
                        :currentStatus="$currentFilter"
                        :taskCounts="$taskCounts"
                        :projectId="$project->id"
                    />
                </div>

                <!-- Task Display -->
                <div id="tasks-container">
                    @if($tasks->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($tasks as $task)
                                <x-task-card :task="$task" />
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            @if($currentFilter === 'all')
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No tasks yet</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating your first task.</p>
                                @can('createInProject', [App\Models\Task::class, $project])
                                    <div class="mt-6">
                                        <button onclick="openCreateTaskModal({{ $project->id }}, '{{ addslashes($project->name) }}', {{ json_encode($project->users->merge(collect([$project->creator]))->unique('id')->filter(function($user) { return $user->role !== 'admin'; })->map(function($user) { return ['id' => $user->id, 'name' => $user->name, 'role' => $user->role]; })->values()) }})" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            Add Task
                                        </button>
                                    </div>
                                @endcan
                            @elseif($currentFilter === 'pending')
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No pending tasks</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">All tasks are either in progress or completed.</p>
                            @elseif($currentFilter === 'in_progress')
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No tasks in progress</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Start working on pending tasks to see them here.</p>
                            @elseif($currentFilter === 'completed')
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No completed tasks</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Complete some tasks to see them here.</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Include Team Management Modal -->
    @include('dashboard.projects.partials.manage-team-modal')

    <!-- Include Edit Project Modal -->
    @include('dashboard.projects.partials.edit-modal')

    <!-- Include Delete Task Modal -->
    @include('dashboard.projects.partials.delete-task-modal')

    <!-- Include Create Task Modal -->
    @include('dashboard.projects.partials.create-task-modal')

    <!-- Include Edit Task Modal -->
    @include('dashboard.projects.partials.edit-task-modal')

    <!-- Include Edit Task Modal Script -->
    @include('dashboard.projects.partials.edit-task-script')

    <!-- Status Update Confirmation Modal -->
    <div id="statusUpdateModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10" id="statusModalIcon">
                            <!-- Icon will be set dynamically -->
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="statusModalTitle">
                                Confirm Status Update
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400" id="statusModalMessage">
                                    <!-- Message will be set dynamically -->
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button
                        type="button"
                        id="confirmStatusUpdate"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                        <!-- Button text and color will be set dynamically -->
                    </button>
                    <button
                        type="button"
                        onclick="closeStatusUpdateModal()"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Dynamic Notification Container -->
    <div id="dynamic-notification-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <script>
        // Dynamic notification system
        function showNotification(type, message) {
            const container = document.getElementById('dynamic-notification-container');
            const notificationId = 'notification-' + Date.now();

            const typeStyles = {
                'success': {
                    bg: 'bg-green-100',
                    border: 'border-green-500',
                    text: 'text-green-700',
                    icon: `<svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>`,
                    closeColor: 'text-green-400 hover:text-green-600'
                },
                'error': {
                    bg: 'bg-red-100',
                    border: 'border-red-500',
                    text: 'text-red-700',
                    icon: `<svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>`,
                    closeColor: 'text-red-400 hover:text-red-600'
                },
                'warning': {
                    bg: 'bg-yellow-100',
                    border: 'border-yellow-500',
                    text: 'text-yellow-700',
                    icon: `<svg class="h-5 w-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>`,
                    closeColor: 'text-yellow-400 hover:text-yellow-600'
                }
            };

            const style = typeStyles[type] || typeStyles['error'];

            const notification = document.createElement('div');
            notification.id = notificationId;
            notification.className = `${style.bg} border-l-4 ${style.border} ${style.text} p-4 rounded-lg shadow-lg max-w-sm transform translate-x-full opacity-0 transition-all duration-500 ease-in-out`;

            notification.innerHTML = `
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        ${style.icon}
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">${message}</p>
                    </div>
                    <div class="ml-auto pl-3">
                        <button type="button" class="notification-close ${style.closeColor} focus:outline-none" onclick="hideNotification('${notificationId}')">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            `;

            container.appendChild(notification);

            // Show notification with animation
            setTimeout(() => {
                notification.classList.remove('translate-x-full', 'opacity-0');
                notification.classList.add('translate-x-0', 'opacity-100');
            }, 100);

            // Auto-hide after 5 seconds
            setTimeout(() => {
                hideNotification(notificationId);
            }, 5000);
        }

        function hideNotification(notificationId) {
            const notification = document.getElementById(notificationId);
            if (notification) {
                notification.classList.remove('translate-x-0', 'opacity-100');
                notification.classList.add('translate-x-full', 'opacity-0');

                setTimeout(() => {
                    notification.remove();
                }, 500);
            }
        }
    </script>
</x-app-layout>
