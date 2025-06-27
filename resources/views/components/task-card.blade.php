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
                                @php
                    $user = auth()->user();
                    $canUpdateStatus = $user && ($user->role === 'admin' || ($task->assigned_to && $task->assigned_to == $user->id));
                    $nextStatus = null;
                    $buttonText = '';
                    $buttonColor = '';

                    if ($canUpdateStatus && $task->status !== 'completed') {
                        if ($task->status === 'pending') {
                            $nextStatus = 'in_progress';
                            $buttonText = 'Start Task';
                            $buttonColor = 'text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20';
                        } elseif ($task->status === 'in_progress') {
                            $nextStatus = 'completed';
                            $buttonText = 'Complete Task';
                            $buttonColor = 'text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20';
                        }
                    }
                @endphp

                @if($nextStatus)
                    <button
                        onclick="updateTaskStatus({{ $task->id }}, '{{ $nextStatus }}')"
                        class="p-2 text-gray-400 {{ $buttonColor }} rounded-lg transition-all duration-200"
                        title="{{ $buttonText }}">
                        @if($task->status === 'pending')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m-10 2.5v-13c0-1.414 1.414-2.5 3-2.5h8c1.586 0 3 1.086 3 2.5v13c-1.414 0-3-1.414-3-2.5s-1.586-2.5-3-2.5-3 1.086-3 2.5z"></path>
                            </svg>
                        @else
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        @endif
                    </button>
                @endif

                @can('update', $task)
                    <button onclick="openEditTaskModal({{ $task->id }})" class="p-2 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-all duration-200" title="Edit Task">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </button>
                @endcan

                @can('delete', $task)
                    <button
                        onclick="openDeleteTaskModal({{ $task->id }}, '{{ addslashes($task->title) }}')"
                        class="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all duration-200"
                        title="Delete Task">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                @endcan
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
        <div class="flex items-center justify-between mb-4">
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
                <!-- Due Date -->
                @if($task->due_date)
                    <div class="flex items-center space-x-1 text-right">
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

        <!-- Comments Toggle Button -->
        <div class="flex items-center justify-between pt-3 border-t border-gray-200 dark:border-gray-700">
            <button
                onclick="toggleComments({{ $task->id }})"
                class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <span id="comments-toggle-text-{{ $task->id }}">
                    @if($task->taskComments && $task->taskComments->count() > 0)
                        View Comments ({{ $task->taskComments->count() }})
                    @else
                        Add Comment
                    @endif
                </span>
                <svg id="comments-chevron-{{ $task->id }}" class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Comments Section (Collapsible) -->
    @php
        $user = auth()->user();
        $canComment = $user && ($user->role === 'admin' || $task->project->isMember($user));
    @endphp

    <div id="comments-section-{{ $task->id }}" class="hidden border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
        <div class="p-4">
            <!-- Simple Comment Input -->
            @if($canComment)
                <form id="add-comment-form-{{ $task->id }}" onsubmit="addComment(event, {{ $task->id }})" class="mb-4">
                    <div class="flex space-x-2">
                        <input
                            type="text"
                                    id="comment-input-{{ $task->id }}"
                                    name="comment"
                                    maxlength="500"
                            placeholder="Add a comment..."
                            class="flex-1 px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100"
                            required>
                                        <button
                                            type="submit"
                                            id="comment-submit-{{ $task->id }}"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <span class="submit-text">Post</span>
                            <span class="loading-text hidden">...</span>
                                        </button>
                        </div>
                    </form>
            @endif

            <!-- Existing Comments -->
            @if($task->taskComments && $task->taskComments->count() > 0)
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
                            class="inline-flex items-center px-3 py-1 text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                            @php
                                $remaining = $task->taskComments->count() - 3;
                            @endphp
                            @if($remaining <= 5)
                                View {{ $remaining }} more
                            @else
                                Load more
                            @endif
                        </button>
                        <div id="load-more-spinner-{{ $task->id }}" class="hidden mt-2">
                            <div class="animate-spin rounded-full h-4 w-4 border-2 border-blue-300 border-t-blue-600 mx-auto"></div>
                        </div>
                    </div>
                @endif
            @else
                @if($canComment)
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No comments yet. Be the first to comment!</p>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Only project members can comment on tasks.</p>
                @endif
            @endif
        </div>
    </div>
</div>

<script>
// Toggle comments section
function toggleComments(taskId) {
    const commentsSection = document.getElementById(`comments-section-${taskId}`);
    const toggleText = document.getElementById(`comments-toggle-text-${taskId}`);
    const chevron = document.getElementById(`comments-chevron-${taskId}`);

    if (commentsSection.classList.contains('hidden')) {
        // Show comments
        commentsSection.classList.remove('hidden');
        chevron.classList.add('rotate-180');

        // Focus on input if available
        const input = document.getElementById(`comment-input-${taskId}`);
        if (input) {
            setTimeout(() => input.focus(), 100);
        }
    } else {
        // Hide comments
        commentsSection.classList.add('hidden');
        chevron.classList.remove('rotate-180');
    }
}

// Add comment functionality
function addComment(event, taskId) {
    event.preventDefault();

    const form = document.getElementById(`add-comment-form-${taskId}`);
    const input = document.getElementById(`comment-input-${taskId}`);
    const submitBtn = document.getElementById(`comment-submit-${taskId}`);
    const formData = new FormData(form);

    // Show loading state
    submitBtn.disabled = true;
    submitBtn.querySelector('.submit-text').classList.add('hidden');
    submitBtn.querySelector('.loading-text').classList.remove('hidden');

    // Add task ID to form data
    formData.append('task_id', taskId);

    fetch('/task-comments', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Clear the form
            input.value = '';

            // Show success notification
            if (typeof showNotification === 'function') {
            showNotification('success', 'Comment added successfully!');
            }

            // Reload the page to show the new comment
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            if (typeof showNotification === 'function') {
            showNotification('error', data.message || 'Failed to add comment.');
            }
        }
    })
    .catch(error => {
        console.error('Error adding comment:', error);
        if (typeof showNotification === 'function') {
        showNotification('error', 'An error occurred while adding the comment.');
        }
    })
    .finally(() => {
        // Reset submit button
        submitBtn.disabled = false;
        submitBtn.querySelector('.submit-text').classList.remove('hidden');
        submitBtn.querySelector('.loading-text').classList.add('hidden');
    });
}

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

// Update task status functionality
function updateTaskStatus(taskId, newStatus) {
    // Store the button reference for later use
    window.statusUpdateButton = event.target.closest('button');
    window.statusUpdateData = { taskId, newStatus };

    // Configure modal based on status
    const isStarting = newStatus === 'in_progress';
    const statusText = isStarting ? 'start working on' : 'complete';
    const actionText = isStarting ? 'Start Task' : 'Complete Task';

    // Set modal content
    document.getElementById('statusModalMessage').textContent = `Are you sure you want to ${statusText} this task?`;

    // Set modal icon
    const iconContainer = document.getElementById('statusModalIcon');
    const confirmButton = document.getElementById('confirmStatusUpdate');

    if (isStarting) {
        // Blue styling for start task
        iconContainer.className = 'mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900 sm:mx-0 sm:h-10 sm:w-10';
        iconContainer.innerHTML = `
            <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m-10 2.5v-13c0-1.414 1.414-2.5 3-2.5h8c1.586 0 3 1.086 3 2.5v13c-1.414 0-3-1.414-3-2.5s-1.586-2.5-3-2.5-3 1.086-3 2.5z"></path>
            </svg>
        `;
        confirmButton.className = 'w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 hover:bg-blue-700 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm';
    } else {
        // Green styling for complete task
        iconContainer.className = 'mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-900 sm:mx-0 sm:h-10 sm:w-10';
        iconContainer.innerHTML = `
            <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        `;
        confirmButton.className = 'w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 hover:bg-green-700 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm';
    }

    confirmButton.textContent = actionText;

    // Show modal
    document.getElementById('statusUpdateModal').classList.remove('hidden');
}

// Handle modal confirmation
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('confirmStatusUpdate').addEventListener('click', function() {
        const { taskId, newStatus } = window.statusUpdateData;
        const button = window.statusUpdateButton;

        // Close modal
        closeStatusUpdateModal();

        // Show loading state on button
        const originalContent = button.innerHTML;
        button.disabled = true;
        button.innerHTML = `
            <div class="animate-spin rounded-full h-4 w-4 border-2 border-current border-t-transparent"></div>
        `;

        // Send update request
        fetch(`/tasks/${taskId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                status: newStatus
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success notification
                if (typeof showNotification === 'function') {
                    showNotification('success', data.message);
                }

                // Reload the page to show updated status
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                // Show error notification
                if (typeof showNotification === 'function') {
                    showNotification('error', data.message || 'Failed to update task status.');
                }

                // Reset button
                button.disabled = false;
                button.innerHTML = originalContent;
            }
        })
        .catch(error => {
            console.error('Error updating task status:', error);

            // Show error notification
            if (typeof showNotification === 'function') {
                showNotification('error', 'An error occurred while updating task status.');
            }

            // Reset button
            button.disabled = false;
            button.innerHTML = originalContent;
        });
    });
});

// Close status update modal
function closeStatusUpdateModal() {
    document.getElementById('statusUpdateModal').classList.add('hidden');
    // Clean up global variables
    delete window.statusUpdateButton;
    delete window.statusUpdateData;
}
</script>
