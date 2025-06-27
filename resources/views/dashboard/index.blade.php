<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Welcome Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="flex-shrink-0">
                            @if(auth()->user()->image)
                                <img class="h-16 w-16 rounded-full object-cover border-2 border-gray-300 dark:border-gray-600"
                                    src="{{ Auth::user()->image }}">
                                <div class="h-16 w-16 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center border-2 border-gray-400 dark:border-gray-500" style="display: none;">
                                    <span class="text-gray-600 dark:text-gray-400 font-medium text-lg">
                                        {{ substr(auth()->user()->name, 0, 1) }}
                                    </span>
                                </div>
                            @else
                                <div class="h-16 w-16 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center border-2 border-gray-400 dark:border-gray-500">
                                    <span class="text-gray-600 dark:text-gray-400 font-medium text-lg">
                                        {{ substr(auth()->user()->name, 0, 1) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                                Welcome back, {{ auth()->user()->name }}!
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400">
                                {{ ucfirst(auth()->user()->role) }} • {{ now()->format('l, F j, Y') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Projects Card -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 p-6 rounded-lg border border-blue-200 dark:border-blue-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-semibold text-blue-900 dark:text-blue-100 mb-1">Projects</h4>
                            <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['projects']['total'] }}</p>
                            <p class="text-sm text-blue-700 dark:text-blue-300">Total projects</p>
                        </div>
                        <div class="text-blue-500 dark:text-blue-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex space-x-2 text-xs">
                        <span class="bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 px-2 py-1 rounded">
                            {{ $stats['projects']['active'] }} Active
                        </span>
                        <span class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 px-2 py-1 rounded">
                            {{ $stats['projects']['completed'] }} Done
                        </span>
                    </div>
                </div>

                <!-- Tasks Card -->
                <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 p-6 rounded-lg border border-green-200 dark:border-green-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-semibold text-green-900 dark:text-green-100 mb-1">Tasks</h4>
                            <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $stats['tasks']['total'] }}</p>
                            <p class="text-sm text-green-700 dark:text-green-300">
                                @if(auth()->user()->role === 'admin')
                                    All tasks
                                @else
                                    Your tasks
                                @endif
                            </p>
                        </div>
                        <div class="text-green-500 dark:text-green-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex space-x-2 text-xs">
                        <span class="bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 px-2 py-1 rounded">
                            {{ $stats['tasks']['pending'] }} Pending
                        </span>
                        <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 px-2 py-1 rounded">
                            {{ $stats['tasks']['in_progress'] }} In Progress
                        </span>
                    </div>
                </div>

                <!-- Comments Card -->
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 p-6 rounded-lg border border-purple-200 dark:border-purple-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-semibold text-purple-900 dark:text-purple-100 mb-1">Comments</h4>
                            <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $stats['comments']['total'] }}</p>
                            <p class="text-sm text-purple-700 dark:text-purple-300">Total comments</p>
                        </div>
                        <div class="text-purple-500 dark:text-purple-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300 px-2 py-1 rounded text-xs">
                            {{ $stats['comments']['this_week'] }} This week
                        </span>
                    </div>
                </div>

                <!-- Productivity Card -->
                <div class="bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 p-6 rounded-lg border border-orange-200 dark:border-orange-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-semibold text-orange-900 dark:text-orange-100 mb-1">Completion</h4>
                            <p class="text-3xl font-bold text-orange-600 dark:text-orange-400">{{ $stats['productivity']['completion_rate'] }}%</p>
                            <p class="text-sm text-orange-700 dark:text-orange-300">Success rate</p>
                        </div>
                        <div class="text-orange-500 dark:text-orange-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 px-2 py-1 rounded text-xs">
                            {{ $stats['productivity']['tasks_completed_this_week'] }} Completed this week
                        </span>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Task Status Chart -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Task Distribution</h3>
                        <div class="relative h-64">
                            <canvas id="taskStatusChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Project Status Chart -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Project Status</h3>
                        <div class="relative h-64">
                            <canvas id="projectStatusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Task Overview - Full Width -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Task Overview</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Completed Tasks -->
                        <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-700">
                            <div class="w-12 h-12 bg-green-500 rounded-full mx-auto mb-3 flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <h4 class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['tasks']['completed'] }}</h4>
                            <p class="text-sm text-green-700 dark:text-green-300">Completed</p>
                        </div>

                        <!-- In Progress Tasks -->
                        <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-700">
                            <div class="w-12 h-12 bg-blue-500 rounded-full mx-auto mb-3 flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h4 class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['tasks']['in_progress'] }}</h4>
                            <p class="text-sm text-blue-700 dark:text-blue-300">In Progress</p>
                        </div>

                        <!-- Pending Tasks -->
                        <div class="text-center p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-700">
                            <div class="w-12 h-12 bg-yellow-500 rounded-full mx-auto mb-3 flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h4 class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['tasks']['pending'] }}</h4>
                            <p class="text-sm text-yellow-700 dark:text-yellow-300">Pending</p>
                        </div>

                        <!-- Overdue/Assigned Tasks -->
                        @if($stats['tasks']['overdue'] > 0)
                        <div class="text-center p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-700">
                            <div class="w-12 h-12 bg-red-500 rounded-full mx-auto mb-3 flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <h4 class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['tasks']['overdue'] }}</h4>
                            <p class="text-sm text-red-700 dark:text-red-300">Overdue</p>
                        </div>
                        @elseif(auth()->user()->role !== 'admin' && $stats['tasks']['assigned'] > 0)
                        <div class="text-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200 dark:border-purple-700">
                            <div class="w-12 h-12 bg-purple-500 rounded-full mx-auto mb-3 flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <h4 class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $stats['tasks']['assigned'] }}</h4>
                            <p class="text-sm text-purple-700 dark:text-purple-300">Assigned to You</p>
                        </div>
                        @else
                        <div class="text-center p-4 bg-gray-50 dark:bg-gray-700/20 rounded-lg border border-gray-200 dark:border-gray-600">
                            <div class="w-12 h-12 bg-gray-400 rounded-full mx-auto mb-3 flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h4 class="text-2xl font-bold text-gray-600 dark:text-gray-400">{{ $stats['tasks']['total'] }}</h4>
                            <p class="text-sm text-gray-700 dark:text-gray-300">Total Tasks</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Activity - Full Width -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Recent Activity</h3>

                    @if(count($recentActivities) > 0)
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            @foreach($recentActivities as $activity)
                                <div class="flex items-start space-x-3 p-4 bg-gray-50 dark:bg-gray-700/20 rounded-lg">
                                    <div class="flex-shrink-0 mt-1">
                                        @if($activity['type'] === 'task_assigned')
                                            <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                        @elseif($activity['type'] === 'task_completed')
                                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                        @elseif($activity['type'] === 'task_updated')
                                            <div class="w-3 h-3 bg-orange-500 rounded-full"></div>
                                        @elseif($activity['type'] === 'comment_added')
                                            <div class="w-3 h-3 bg-purple-500 rounded-full"></div>
                                        @elseif($activity['type'] === 'project_created')
                                            <div class="w-3 h-3 bg-indigo-500 rounded-full"></div>
                                        @elseif($activity['type'] === 'task_created')
                                            <div class="w-3 h-3 bg-teal-500 rounded-full"></div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-gray-900 dark:text-gray-100 font-medium">
                                            {{ $activity['message'] }}
                                        </p>
                                        <div class="flex items-center space-x-2 mt-1">
                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $activity['project'] }}
                                            </span>
                                            <span class="text-xs text-gray-400 dark:text-gray-500">•</span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $activity['time']->diffForHumans() }}
                                            </span>
                                        </div>
                                        @if(isset($activity['priority']))
                                            <x-priority-badge :priority="$activity['priority']" class="mt-2" />
                                        @endif
                                        @if(isset($activity['comment']))
                                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-2 italic bg-gray-100 dark:bg-gray-600 p-2 rounded">
                                                "{{ $activity['comment'] }}"
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-gray-100">No recent activity</h3>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                Your recent activities will appear here once you start working on tasks.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check if we're in dark mode
            const isDarkMode = document.documentElement.classList.contains('dark');

            // Common chart options for better text visibility
            const chartOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            color: '#FFFFFF',
                            font: {
                                size: 12,
                                weight: '500'
                            }
                        }
                    }
                }
            };

            // Task Status Doughnut Chart
            const taskCtx = document.getElementById('taskStatusChart').getContext('2d');
            new Chart(taskCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Completed', 'In Progress', 'Pending'],
                    datasets: [{
                        data: [
                            {{ $stats['tasks']['completed'] }},
                            {{ $stats['tasks']['in_progress'] }},
                            {{ $stats['tasks']['pending'] }}
                        ],
                        backgroundColor: [
                            '#10B981', // Green for completed
                            '#3B82F6', // Blue for in progress
                            '#F59E0B'  // Yellow for pending
                        ],
                        borderColor: isDarkMode ? '#1F2937' : '#FFFFFF',
                        borderWidth: 2
                    }]
                },
                options: {
                    ...chartOptions,
                    plugins: {
                        ...chartOptions.plugins,
                        legend: {
                            ...chartOptions.plugins.legend,
                            position: 'bottom'
                        }
                    }
                }
            });

            // Project Status Bar Chart
            const projectCtx = document.getElementById('projectStatusChart').getContext('2d');
            new Chart(projectCtx, {
                type: 'bar',
                data: {
                    labels: ['Active', 'Completed', 'Archived'],
                    datasets: [{
                        label: 'Projects',
                        data: [
                            {{ $stats['projects']['active'] }},
                            {{ $stats['projects']['completed'] }},
                            {{ $stats['projects']['archived'] }}
                        ],
                        backgroundColor: [
                            '#3B82F6', // Blue for active
                            '#10B981', // Green for completed
                            '#6B7280'  // Gray for archived
                        ],
                        borderColor: [
                            '#2563EB',
                            '#059669',
                            '#4B5563'
                        ],
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    ...chartOptions,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#FFFFFF',
                                stepSize: 1,
                                font: {
                                    size: 11,
                                    weight: '500'
                                }
                            },
                            grid: {
                                color: isDarkMode ? '#4B5563' : '#D1D5DB'
                            }
                        },
                        x: {
                            ticks: {
                                color: '#FFFFFF',
                                font: {
                                    size: 11,
                                    weight: '500'
                                }
                            },
                            grid: {
                                color: isDarkMode ? '#4B5563' : '#D1D5DB'
                            }
                        }
                    },
                    plugins: {
                        ...chartOptions.plugins,
                        legend: {
                            display: false
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
