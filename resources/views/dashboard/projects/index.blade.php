<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Projects') }}
            </h2>
            @can('create', App\Models\Project::class)
                <button onclick="openCreateModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>New Project</span>
                </button>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Projects Overview Stats -->
            <div class="mb-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Projects</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100" data-stat="total-projects">{{ $totalProjects }}</p>
                        </div>
                    </div>
                </div>

                @if($user->role === 'creator' || $user->role === 'admin')
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">My Projects</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100" data-stat="my-projects">{{ $organizedProjects['ownProjects']->count() }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($user->role === 'assignee' || $user->role === 'creator')
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Assigned Projects</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100" data-stat="assigned-projects">{{ $organizedProjects['assignedProjects']->count() }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Search and Filter Section -->
            <div class="mb-8 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                        <!-- Search Bar -->
                        <div class="flex-1 max-w-md">
                            <label for="projectSearch" class="sr-only">Search projects</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <input
                                    type="text"
                                    id="projectSearch"
                                    value="{{ $search }}"
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                    placeholder="Search by project name or creator..."
                                >
                            </div>
                        </div>

                        <!-- Sort Dropdown -->
                        <div class="flex items-center space-x-4">
                            <label for="projectSort" class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Sort by:</label>
                            <select
                                id="projectSort"
                                class="block w-auto pl-3 pr-10 py-2 text-base border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                            >
                                <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Newest First</option>
                                <option value="oldest" {{ $sort === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                                <option value="name_asc" {{ $sort === 'name_asc' ? 'selected' : '' }}>Name A-Z</option>
                                <option value="name_desc" {{ $sort === 'name_desc' ? 'selected' : '' }}>Name Z-A</option>
                            </select>

                            <!-- Clear Filters Button -->
                            <button
                                id="clearFilters"
                                class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                                style="{{ $hasFilters ? '' : 'display: none;' }}"
                            >
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Clear
                            </button>
                        </div>
                    </div>

                    <!-- Results Info -->
                    <div id="resultsInfo" class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                        @if(!empty($search))
                            Showing {{ $totalProjects }} result{{ $totalProjects !== 1 ? 's' : '' }} for "<span class="font-medium">{{ $search }}</span>"
                        @else
                            Showing {{ $totalProjects }} project{{ $totalProjects !== 1 ? 's' : '' }}
                        @endif
                    </div>
                </div>
            </div>

            <!-- Projects Content Container -->
            <div id="projectsContent">
                <!-- Project Sections - All users see all projects with relationship-based actions -->
            @if($user->role === 'member')
                <!-- Member View: All projects with relationship-based actions -->
                @include('dashboard.projects.partials.member-view', ['organizedProjects' => $organizedProjects, 'user' => $user])

            @elseif($user->role === 'assignee')
                <!-- Assignee View: All projects with relationship-based actions -->
                @include('dashboard.projects.partials.assignee-view', ['organizedProjects' => $organizedProjects, 'user' => $user])

            @elseif($user->role === 'creator')
                <!-- Creator View: All projects with relationship-based actions -->
                @include('dashboard.projects.partials.creator-view', ['organizedProjects' => $organizedProjects, 'user' => $user])

            @elseif($user->role === 'admin')
                <!-- Admin View: All projects with full control -->
                @include('dashboard.projects.partials.admin-view', ['organizedProjects' => $organizedProjects, 'user' => $user])
            @endif
            </div>
        </div>
    </div>

    <!-- Modals -->
    @include('dashboard.projects.partials.create-modal')
    @include('dashboard.projects.partials.read-modal')
    @include('dashboard.projects.partials.edit-modal')
    @include('dashboard.projects.partials.delete-modal')
    @include('dashboard.projects.partials.manage-team-modal')

        <!-- Search and Filter JavaScript -->
    <script>
        let searchTimeout;

        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('projectSearch');
            const sortSelect = document.getElementById('projectSort');
            const clearButton = document.getElementById('clearFilters');
            const projectsContent = document.getElementById('projectsContent');
            const resultsInfo = document.getElementById('resultsInfo');

            // Search input handler with debouncing
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    performAjaxSearch();
                }, 500); // 500ms delay for debouncing
            });

            // Sort change handler
            sortSelect.addEventListener('change', function() {
                performAjaxSearch();
            });

            // Clear filters handler
            clearButton.addEventListener('click', function() {
                searchInput.value = '';
                sortSelect.value = 'newest';
                performAjaxSearch();
            });

            function performAjaxSearch() {
                const search = searchInput.value.trim();
                const sort = sortSelect.value;

                // Show loading state
                showLoadingState();

                // Update URL without page reload
                const url = new URL(window.location.href);
                url.searchParams.set('search', search);
                url.searchParams.set('sort', sort);
                window.history.pushState({}, '', url.toString());

                // Make AJAX request
                fetch(`{{ route('projects.index') }}?search=${encodeURIComponent(search)}&sort=${encodeURIComponent(sort)}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateProjectsContent(data.organizedProjects, data.totalProjects, search);
                        updateResultsInfo(data.totalProjects, search);
                        updateClearButtonVisibility(search, sort);
                    } else {
                        showNotification('error', 'Failed to load projects');
                    }
                })
                .catch(error => {
                    console.error('Search error:', error);
                    showNotification('error', 'An error occurred while searching');
                })
                .finally(() => {
                    hideLoadingState();
                });
            }

            function showLoadingState() {
                projectsContent.style.opacity = '0.6';
                projectsContent.style.pointerEvents = 'none';

                // Add loading spinner if not exists
                if (!document.getElementById('searchLoadingSpinner')) {
                    const spinner = document.createElement('div');
                    spinner.id = 'searchLoadingSpinner';
                    spinner.className = 'fixed top-20 left-1/2 transform -translate-x-1/2 bg-white dark:bg-gray-800 px-4 py-2 rounded-lg shadow-lg z-50';
                    spinner.innerHTML = `
                        <div class="flex items-center space-x-2">
                            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600"></div>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Searching...</span>
                        </div>
                    `;
                    document.body.appendChild(spinner);
                }
            }

            function hideLoadingState() {
                projectsContent.style.opacity = '1';
                projectsContent.style.pointerEvents = 'auto';

                const spinner = document.getElementById('searchLoadingSpinner');
                if (spinner) {
                    spinner.remove();
                }
            }

            function updateProjectsContent(organizedProjects, totalProjects, search) {
                const userRole = '{{ $user->role }}';
                let contentHtml = '';

                // Generate content based on user role and organized projects
                if (userRole === 'member') {
                    contentHtml = generateMemberView(organizedProjects);
                } else if (userRole === 'assignee') {
                    contentHtml = generateAssigneeView(organizedProjects);
                } else if (userRole === 'creator') {
                    contentHtml = generateCreatorView(organizedProjects);
                } else if (userRole === 'admin') {
                    contentHtml = generateAdminView(organizedProjects);
                }

                projectsContent.innerHTML = contentHtml;

                // Update statistics cards
                updateStatisticsCards(organizedProjects, totalProjects);
            }

            function updateResultsInfo(totalProjects, search) {
                let infoText = '';
                if (search) {
                    infoText = `Showing ${totalProjects} result${totalProjects !== 1 ? 's' : ''} for "<span class="font-medium">${search}</span>"`;
                } else {
                    infoText = `Showing ${totalProjects} project${totalProjects !== 1 ? 's' : ''}`;
                }
                resultsInfo.innerHTML = infoText;
            }

            function updateClearButtonVisibility(search, sort) {
                const hasFilters = search !== '' || sort !== 'newest';
                clearButton.style.display = hasFilters ? 'inline-flex' : 'none';
            }

            function updateStatisticsCards(organizedProjects, totalProjects) {
                // Update total projects
                const totalProjectsElement = document.querySelector('[data-stat="total-projects"]');
                if (totalProjectsElement) {
                    totalProjectsElement.textContent = totalProjects;
                }

                // Update my projects (for creators and admins)
                const myProjectsElement = document.querySelector('[data-stat="my-projects"]');
                if (myProjectsElement) {
                    myProjectsElement.textContent = organizedProjects.ownProjects ? organizedProjects.ownProjects.length : 0;
                }

                // Update assigned projects (for assignees and creators)
                const assignedProjectsElement = document.querySelector('[data-stat="assigned-projects"]');
                if (assignedProjectsElement) {
                    assignedProjectsElement.textContent = organizedProjects.assignedProjects ? organizedProjects.assignedProjects.length : 0;
                }
            }

            function generateMemberView(organizedProjects) {
                return generateRoleView(organizedProjects, 'member', [
                    { key: 'ownProjects', title: 'My Created Projects', emptyMessage: 'You haven\'t created any projects yet.' },
                    { key: 'otherProjects', title: 'All Organization Projects', emptyMessage: 'No projects found in your organization.' }
                ]);
            }

            function generateAssigneeView(organizedProjects) {
                return generateRoleView(organizedProjects, 'assignee', [
                    { key: 'assignedProjects', title: 'Projects I\'m Assigned To', emptyMessage: 'You are not assigned to any projects yet.' },
                    { key: 'otherProjects', title: 'Other Projects', emptyMessage: 'No other projects available.' }
                ]);
            }

            function generateCreatorView(organizedProjects) {
                return generateRoleView(organizedProjects, 'creator', [
                    { key: 'ownProjects', title: 'My Created Projects', emptyMessage: 'You haven\'t created any projects yet.' },
                    { key: 'assignedProjects', title: 'Projects I\'m Assigned To', emptyMessage: 'You are not assigned to any projects.' },
                    { key: 'otherProjects', title: 'Other Projects', emptyMessage: 'No other projects available.' }
                ]);
            }

            function generateAdminView(organizedProjects) {
                return generateRoleView(organizedProjects, 'admin', [
                    { key: 'ownProjects', title: 'My Created Projects', emptyMessage: 'You haven\'t created any projects yet.' },
                    { key: 'otherProjects', title: 'All Organization Projects', emptyMessage: 'No projects found in your organization.' }
                ]);
            }

            function generateRoleView(organizedProjects, userRole, sections) {
                let html = '';

                sections.forEach(section => {
                    const projects = organizedProjects[section.key] || [];

                    html += `
                        <div class="mb-8">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">${section.title}</h2>
                                <span class="text-sm text-gray-500 dark:text-gray-400">${projects.length} project${projects.length !== 1 ? 's' : ''}</span>
                            </div>
                    `;

                    if (projects.length > 0) {
                        html += '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">';
                        projects.forEach(project => {
                            html += generateProjectCard(project, userRole, section.key);
                        });
                        html += '</div>';
                    } else {
                        html += `
                            <div class="text-center py-12 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No projects found</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">${section.emptyMessage}</p>
                            </div>
                        `;
                    }

                    html += '</div>';
                });

                return html;
            }

            function generateProjectCard(project, userRole, sectionKey) {
                const completedTasks = project.tasks ? project.tasks.filter(task => task.status === 'completed').length : 0;
                const totalTasks = project.tasks ? project.tasks.length : 0;
                const progressPercentage = totalTasks > 0 ? Math.round((completedTasks / totalTasks) * 100) : 0;

                // Determine available actions based on user role and relationship
                let actions = [];

                if (userRole === 'admin') {
                    actions = ['read', 'enter', 'edit', 'delete', 'manage_team'];
                } else if (sectionKey === 'ownProjects') {
                    actions = ['read', 'enter', 'edit', 'delete', 'manage_team'];
                } else if (sectionKey === 'assignedProjects') {
                    actions = ['read', 'enter'];
                } else {
                    actions = ['read'];
                }

                const statusColors = {
                    'active': 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200',
                    'completed': 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-200',
                    'archived': 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200'
                };

                let actionsHtml = '';
                actions.forEach(action => {
                    switch(action) {
                        case 'read':
                            actionsHtml += `<button onclick="openReadModal(${project.id})" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 p-1 rounded hover:bg-blue-50 dark:hover:bg-blue-900 transition-colors duration-200" title="View Details"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button>`;
                            break;
                        case 'enter':
                            actionsHtml += `<button onclick="enterProject(${project.id})" class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 p-1 rounded hover:bg-green-50 dark:hover:bg-green-900 transition-colors duration-200" title="Enter Project"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg></button>`;
                            break;
                        case 'edit':
                            actionsHtml += `<button onclick="openEditModal(${project.id})" class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300 p-1 rounded hover:bg-yellow-50 dark:hover:bg-yellow-900 transition-colors duration-200" title="Edit Project"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg></button>`;
                            break;
                        case 'delete':
                            actionsHtml += `<button onclick="openDeleteModal(${project.id})" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 p-1 rounded hover:bg-red-50 dark:hover:bg-red-900 transition-colors duration-200" title="Delete Project"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>`;
                            break;
                        case 'manage_team':
                            actionsHtml += `<button onclick="openManageTeamModal(${project.id}, '${project.name.replace(/'/g, "\\'")}' )" class="text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-300 p-1 rounded hover:bg-purple-50 dark:hover:bg-purple-900 transition-colors duration-200" title="Manage Team"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg></button>`;
                            break;
                    }
                });

                return `
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow duration-200">
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">${project.name}</h3>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusColors[project.status] || statusColors.active}">
                                        ${project.status.charAt(0).toUpperCase() + project.status.slice(1)}
                                    </span>
                                </div>
                                <div class="flex items-center space-x-1 ml-4">
                                    ${actionsHtml}
                                </div>
                            </div>

                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-2">
                                ${project.description || 'No description provided.'}
                            </p>

                            <div class="mb-4">
                                <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-1">
                                    <span>Progress</span>
                                    <span>${progressPercentage}%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: ${progressPercentage}%"></div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                                <div class="flex items-center space-x-4">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        ${totalTasks} task${totalTasks !== 1 ? 's' : ''}
                                    </span>
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        ${project.users ? project.users.length : 0} member${project.users && project.users.length !== 1 ? 's' : ''}
                                    </span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-6 h-6 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center mr-2">
                                        <span class="text-xs font-medium text-gray-600 dark:text-gray-400">${project.creator ? project.creator.name.charAt(0) : 'U'}</span>
                                    </div>
                                    <span class="text-xs">${project.creator ? project.creator.name : 'Unknown'}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            // Helper function for entering projects (placeholder)
            window.enterProject = function(projectId) {
                // TODO: Implement project entry functionality
                showNotification('info', 'Project entry functionality coming soon!');
            };
        });
    </script>
</x-app-layout>
