<!-- Member View: All organization projects (members cannot create or be assigned to projects) -->
<div class="space-y-8">
    <!-- All Organization Projects -->
    @if($organizedProjects['otherProjects']->count() > 0)
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        All Organization Projects
                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $organizedProjects['otherProjects']->count() }} projects
                        </span>
                    </h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        All projects in the organization. You can view their details.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($organizedProjects['otherProjects'] as $project)
                    <x-project-card
                        :project="$project"
                        :actions="['read']"
                        :user="$user"
                    />
                @endforeach
            </div>
        </div>
    @endif

    <!-- Empty State -->
    @if($organizedProjects['otherProjects']->count() === 0)
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No projects available</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">There are currently no projects to display.</p>
        </div>
    @endif
</div>
