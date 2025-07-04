@props([
    'project',
    'actions' => [],
    'user' => null
])

@php
    $statusConfig = [
        'active' => [
            'color' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-300 dark:border-emerald-800',
            'dot' => 'bg-emerald-500',
            'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'
        ],
        'completed' => [
            'color' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/20 dark:text-blue-300 dark:border-blue-800',
            'dot' => 'bg-blue-500',
            'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
        ],
        'archived' => [
            'color' => 'bg-gray-50 text-gray-700 border-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700',
            'dot' => 'bg-gray-500',
            'icon' => 'M5 8a2 2 0 012-2h6a2 2 0 012 2v3a2 2 0 01-2 2H7a2 2 0 01-2-2V8zM5 15a2 2 0 012-2h6a2 2 0 012 2v3a2 2 0 01-2 2H7a2 2 0 01-2-2v-3z'
        ]
    ];

    $config = $statusConfig[$project->status] ?? $statusConfig['active'];

    $totalTasks = $project->tasks->count();
    $completedTasks = $project->tasks->where('status', 'completed')->count();
    $progress = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;

    // Check if user can view the project (authorization check)
    $canView = $user && $user->can('view', $project);
@endphp

<div class="group relative bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-lg hover:border-gray-300 dark:hover:border-gray-600 transition-all duration-300 overflow-hidden {{ $canView ? 'cursor-pointer' : 'cursor-default' }}"
     @if($canView) onclick="window.location.href='{{ route('projects.show', $project) }}'" @endif>
    <!-- Status indicator line -->
    <div class="absolute top-0 left-0 right-0 h-1 {{ $config['dot'] }}"></div>

    <!-- Project Content -->
    <div class="p-6">
        <!-- Project Header -->
        <div class="flex items-start justify-between mb-4">
            <div class="flex-1 min-w-0">
                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2 {{ $canView ? 'group-hover:text-blue-600 dark:group-hover:text-blue-400' : '' }} transition-colors duration-200 line-clamp-2">
                    {{ $project->name }}
                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-3 leading-relaxed mb-3">
                    {{ Str::limit($project->description, 150) }}
                </p>

                <!-- Access restriction notice for non-authorized users -->
                @if(!$canView)
                    <div class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400 border border-gray-200 dark:border-gray-600 mb-3">
                        <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Access Restricted
                    </div>
                @endif
            </div>

            <!-- Status Badge -->
            <div class="ml-4 flex-shrink-0">
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium border {{ $config['color'] }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $config['dot'] }} mr-2"></span>
                    {{ ucfirst($project->status) }}
                </span>
            </div>
        </div>

        <!-- Progress Section -->
        <div class="mb-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Project Progress</span>
                <span class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ round($progress) }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700 shadow-inner">
                <div class="h-2.5 rounded-full transition-all duration-500 ease-out {{ $progress >= 100 ? 'bg-emerald-500' : ($progress >= 75 ? 'bg-blue-500' : ($progress >= 50 ? 'bg-yellow-500' : 'bg-orange-500')) }}" style="width: {{ $progress }}%"></div>
            </div>
            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-1">
                <span>{{ $completedTasks }} completed</span>
                <span>{{ $totalTasks }} total tasks</span>
            </div>
        </div>

        <!-- Project Stats -->
        <div class="grid grid-cols-2 gap-4 mb-5">
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/50 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $totalTasks }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Tasks</p>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/50 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $project->users->count() }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Members</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Project Creator -->
        <div class="flex items-center mb-5 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
            <div class="w-10 h-10 bg-gradient-to-br from-indigo-400 to-indigo-600 rounded-full flex items-center justify-center mr-3 shadow-sm">
                @if($project->creator->image)
                    <img src="{{ $project->creator->image }}" alt="{{ $project->creator->name }}" class="w-10 h-10 rounded-full object-cover">
                @else
                    <span class="text-sm font-medium text-white">
                        {{ substr($project->creator->name, 0, 1) }}
                    </span>
                @endif
            </div>
            <div class="flex-1">
                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $project->creator->name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Project Creator • {{ $project->created_at->diffForHumans() }}</p>
            </div>
        </div>

        <!-- Action Buttons -->
        @if(count($actions) > 0)
            <div class="flex flex-wrap gap-2 pt-4 border-t border-gray-200 dark:border-gray-700">
                @foreach($actions as $action)
                    @if($action === 'read' && $canView)
                        <button
                            onclick="event.stopPropagation(); openReadModal({{ $project->id }})"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            View Details
                        </button>
                    @elseif($action === 'enter')
                        @if($canView)
                            <a
                                href="{{ route('projects.show', $project) }}"
                                onclick="event.stopPropagation()"
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                </svg>
                                Enter Project
                            </a>
                        @else
                            <button
                                disabled
                                onclick="event.stopPropagation()"
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-lg text-gray-400 bg-gray-300 cursor-not-allowed opacity-50">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                Access Restricted
                            </button>
                        @endif
                    @elseif($action === 'edit')
                        <button
                            onclick="event.stopPropagation(); openEditModal({{ $project->id }})"
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-lg text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-all duration-200 shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit
                        </button>
                    @elseif($action === 'delete')
                        <button
                            onclick="event.stopPropagation(); openDeleteModal({{ $project->id }}, '{{ addslashes($project->name) }}')"
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Delete
                        </button>
                    @elseif($action === 'manage_team')
                        <button
                            onclick="event.stopPropagation(); openManageTeamModal({{ $project->id }}, '{{ addslashes($project->name) }}')"
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-lg text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200 shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Manage Team
                        </button>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</div>
