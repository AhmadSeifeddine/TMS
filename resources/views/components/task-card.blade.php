@props(['task', 'showProject' => false])

@php
    $statusColors = [
        'pending' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/20 dark:text-amber-300 dark:border-amber-800',
        'in_progress' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/20 dark:text-blue-300 dark:border-blue-800',
        'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-300 dark:border-emerald-800'
    ];

    $statusIcons = [
        'pending' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        'in_progress' => 'M13 10V3L4 14h7v7l9-11h-7z',
        'completed' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
    ];

    $statusDots = [
        'pending' => 'bg-amber-400',
        'in_progress' => 'bg-blue-400',
        'completed' => 'bg-emerald-400'
    ];

    $isOverdue = $task->due_date && \Carbon\Carbon::parse($task->due_date)->isPast() && $task->status !== 'completed';
    $isDueSoon = $task->due_date && \Carbon\Carbon::parse($task->due_date)->diffInDays(now()) <= 2 && $task->status !== 'completed';
@endphp

<div class="group relative bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-lg hover:border-gray-300 dark:hover:border-gray-600 transition-all duration-300 overflow-hidden">
    <!-- Status indicator line -->
    <div class="absolute top-0 left-0 right-0 h-1 {{ $statusDots[$task->status] }}"></div>

    <!-- Task Header -->
    <div class="p-6">
        <div class="flex items-start justify-between mb-4">
            <div class="flex-1 min-w-0">
                <!-- Title -->
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-200 line-clamp-2">
                    {{ $task->title }}
                </h3>

                <!-- Project info if needed -->
                @if($showProject && $task->project)
                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-3">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        {{ $task->project->name }}
                    </div>
                @endif

                <!-- Status and Priority badges -->
                <div class="flex items-center flex-wrap gap-2 mb-3">
                    <!-- Status Badge -->
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border {{ $statusColors[$task->status] }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $statusDots[$task->status] }} mr-1.5"></span>
                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                    </span>

                    <!-- Priority Badge -->
                    <x-priority-badge :priority="$task->priority" />

                    <!-- Overdue/Due Soon badges -->
                    @if($isOverdue)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-200 dark:bg-red-900/20 dark:text-red-300 dark:border-red-800">
                            <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Overdue
                        </span>
                    @elseif($isDueSoon)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-orange-50 text-orange-700 border border-orange-200 dark:bg-orange-900/20 dark:text-orange-300 dark:border-orange-800">
                            <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Due Soon
                        </span>
                    @endif
                </div>
            </div>

            <!-- Task Actions -->
            <div class="flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200 ml-4">
                <button class="p-2 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-all duration-200" title="Edit Task">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </button>
                <button class="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all duration-200" title="Delete Task">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Task Description -->
        @if($task->description)
            <div class="mb-4">
                <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-3 leading-relaxed">
                    {{ $task->description }}
                </p>
            </div>
        @endif

        <!-- Task Meta Information -->
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <!-- Assigned User -->
                @if($task->assignedUser)
                    <div class="flex items-center space-x-2">
                        <div class="w-7 h-7 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center shadow-sm">
                            <span class="text-xs font-medium text-white">
                                {{ substr($task->assignedUser->name, 0, 1) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $task->assignedUser->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Assignee</p>
                        </div>
                    </div>
                @else
                    <div class="flex items-center space-x-2">
                        <div class="w-7 h-7 bg-gray-200 dark:bg-gray-600 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Unassigned</p>
                            <p class="text-xs text-gray-400">No assignee</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right side info -->
            <div class="flex items-center space-x-4">
                <!-- Comments Count -->
                @if($task->taskComments && $task->taskComments->count() > 0)
                    <div class="flex items-center space-x-1 text-gray-500 dark:text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <span class="text-sm font-medium">{{ $task->taskComments->count() }}</span>
                    </div>
                @endif

                <!-- Due Date -->
                @if($task->due_date)
                    <div class="flex items-center space-x-1 text-right">
                        <svg class="w-4 h-4 {{ $isOverdue ? 'text-red-500' : ($isDueSoon ? 'text-orange-500' : 'text-gray-400') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-medium {{ $isOverdue ? 'text-red-600 dark:text-red-400' : ($isDueSoon ? 'text-orange-600 dark:text-orange-400' : 'text-gray-700 dark:text-gray-300') }}">
                                {{ \Carbon\Carbon::parse($task->due_date)->format('M j, Y') }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ \Carbon\Carbon::parse($task->due_date)->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Comments Section (if comments exist) -->
    @if($task->taskComments && $task->taskComments->count() > 0)
        <div class="border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        Recent Comments
                    </h4>
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 bg-gray-200 dark:bg-gray-700 px-2 py-1 rounded-full">
                        {{ $task->taskComments->count() }}
                    </span>
                </div>

                <div id="comments-container-{{ $task->id }}" class="space-y-3">
                    @foreach($task->taskComments->take(3) as $comment)
                        <div class="comment-item bg-white dark:bg-gray-700 rounded-lg p-3 border border-gray-200 dark:border-gray-600">
                            <div class="flex space-x-3">
                                <div class="w-6 h-6 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0 shadow-sm">
                                    <span class="text-xs font-medium text-white">
                                        {{ substr($comment->creator->name ?? 'U', 0, 1) }}
                                    </span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $comment->creator->name ?? 'Unknown' }}
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $comment->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                        {{ $comment->comment }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($task->taskComments->count() > 3)
                    <div id="load-more-container-{{ $task->id }}" class="text-center mt-4">
                        <button
                            onclick="loadMoreComments({{ $task->id }}, 3)"
                            id="load-more-btn-{{ $task->id }}"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/40 border border-blue-200 dark:border-blue-800 rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                            @php
                                $remaining = $task->taskComments->count() - 3;
                            @endphp
                            @if($remaining <= 5)
                                View {{ $remaining }} more comment{{ $remaining > 1 ? 's' : '' }}
                            @else
                                Load more comments
                            @endif
                        </button>
                        <div id="load-more-spinner-{{ $task->id }}" class="hidden mt-2">
                            <div class="animate-spin rounded-full h-5 w-5 border-2 border-blue-300 border-t-blue-600 mx-auto"></div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

<script>
function loadMoreComments(taskId, currentOffset) {
    const button = document.getElementById(`load-more-btn-${taskId}`);
    const spinner = document.getElementById(`load-more-spinner-${taskId}`);
    const container = document.getElementById(`comments-container-${taskId}`);

    // Show loading state
    button.style.display = 'none';
    spinner.classList.remove('hidden');

    // Fetch more comments
    fetch(`/tasks/${taskId}/comments?offset=${currentOffset}&limit=5`, {
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
            // Add server-rendered HTML to the container
            container.insertAdjacentHTML('beforeend', data.comments_html);

            // Update the load more button
            if (data.has_more) {
                button.textContent = data.button_text;
                button.setAttribute('onclick', `loadMoreComments(${taskId}, ${data.new_offset})`);
                button.style.display = 'inline-block';
            } else {
                // Hide the button if no more comments
                document.getElementById(`load-more-container-${taskId}`).style.display = 'none';
            }
        } else {
            // Show error message
            button.textContent = 'Error loading comments';
            button.style.display = 'inline-block';
            button.disabled = true;
        }
    })
    .catch(error => {
        console.error('Error loading comments:', error);
        button.textContent = 'Error loading comments';
        button.style.display = 'inline-block';
        button.disabled = true;
    })
    .finally(() => {
        // Hide spinner
        spinner.classList.add('hidden');
    });
}
</script>
