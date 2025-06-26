<!-- Admin View: Full control over all projects regardless of relationship -->
<div class="space-y-8">
    <!-- Own Projects Section -->
    @if($organizedProjects['ownProjects']->count() > 0)
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                        </svg>
                        My Created Projects
                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            {{ $organizedProjects['ownProjects']->count() }} projects
                        </span>
                    </h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Projects you created and manage as an administrator.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($organizedProjects['ownProjects'] as $project)
                    <x-project-card
                        :project="$project"
                        :actions="['read', 'enter', 'edit', 'delete', 'manage_team']"
                        :user="$user"
                    />
                @endforeach
            </div>
        </div>
    @endif



    <!-- All Other Projects Section -->
    @if($organizedProjects['otherProjects']->count() > 0)
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.5-1.5a2.5 2.5 0 00-5 0l-.5 8.5a.5.5 0 001 0L12.5 11a2.5 2.5 0 000-5z"></path>
                        </svg>
                        All Organization Projects
                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            {{ $organizedProjects['otherProjects']->count() }} projects
                        </span>
                    </h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        All projects in the organization. As an admin, you have full control over these projects.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($organizedProjects['otherProjects'] as $project)
                    <x-project-card
                        :project="$project"
                        :actions="['read', 'enter', 'edit', 'delete', 'manage_team']"
                        :user="$user"
                    />
                @endforeach
            </div>
        </div>
    @endif

    <!-- Empty State for New Admins -->
    @if($organizedProjects['ownProjects']->count() === 0 && $organizedProjects['otherProjects']->count() === 0)
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No projects found</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating the first project for your organization.</p>
            @can('create', App\Models\Project::class)
                <div class="mt-4">
                    <button onclick="openCreateModal()" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Create First Project
                    </button>
                </div>
            @endcan
        </div>
    @endif
</div>
