<!-- Delete Task Confirmation Modal -->
<div id="deleteTaskModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/20">
                        <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Delete Task</h3>
                    </div>
                </div>
                <button onclick="closeDeleteTaskModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="mb-6">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    Are you sure you want to delete this task? This action cannot be undone.
                </p>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100" id="deleteTaskTitle">
                        <!-- Task title will be inserted here -->
                    </p>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end space-x-3">
                <button
                    onclick="closeDeleteTaskModal()"
                    class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md hover:bg-gray-400 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors duration-200">
                    Cancel
                </button>
                <button
                    id="confirmDeleteTaskBtn"
                    onclick="confirmDeleteTask()"
                    class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors duration-200 flex items-center">
                    <span id="deleteTaskBtnText">Delete Task</span>
                    <svg id="deleteTaskSpinner" class="animate-spin -mr-1 ml-2 h-4 w-4 text-white hidden" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let taskToDelete = null;

function openDeleteTaskModal(taskId, taskTitle) {
    taskToDelete = taskId;
    document.getElementById('deleteTaskTitle').textContent = taskTitle;
    document.getElementById('deleteTaskModal').classList.remove('hidden');

    // Focus management for accessibility
    document.getElementById('confirmDeleteTaskBtn').focus();
}

function closeDeleteTaskModal() {
    taskToDelete = null;
    document.getElementById('deleteTaskModal').classList.add('hidden');

    // Reset button state
    const btn = document.getElementById('confirmDeleteTaskBtn');
    const btnText = document.getElementById('deleteTaskBtnText');
    const spinner = document.getElementById('deleteTaskSpinner');

    btn.disabled = false;
    btnText.textContent = 'Delete Task';
    spinner.classList.add('hidden');
}

function confirmDeleteTask() {
    if (!taskToDelete) return;

    const btn = document.getElementById('confirmDeleteTaskBtn');
    const btnText = document.getElementById('deleteTaskBtnText');
    const spinner = document.getElementById('deleteTaskSpinner');

    // Show loading state
    btn.disabled = true;
    btnText.textContent = 'Deleting...';
    spinner.classList.remove('hidden');

    // Send delete request
    fetch(`/tasks/${taskToDelete}`, {
        method: 'DELETE',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal first
            closeDeleteTaskModal();

            // Show success notification
            if (typeof showNotification === 'function') {
                showNotification('success', data.message);
                console.log('Notification shown, will reload in 1.5 seconds...');
            } else {
                // Fallback to alert if showNotification is not available
                alert(data.message);
                console.log('Alert shown, will reload in 1.5 seconds...');
            }

            // Reload the page after a short delay to show the notification
            setTimeout(() => {
                console.log('Reloading page now...');
                window.location.reload();
            }, 1500); // 1.5 second delay to see the notification

        } else {
            // Show error notification
            if (typeof showNotification === 'function') {
                showNotification('error', data.message || 'Failed to delete task.');
            } else {
                alert('Error: ' + (data.message || 'Failed to delete task.'));
            }

            // Reset button state
            btn.disabled = false;
            btnText.textContent = 'Delete Task';
            spinner.classList.add('hidden');
        }
    })
    .catch(error => {
        console.error('Error deleting task:', error);

        // Show error notification
        if (typeof showNotification === 'function') {
            showNotification('error', 'An error occurred while deleting the task.');
        } else {
            alert('An error occurred while deleting the task.');
        }

        // Reset button state
        btn.disabled = false;
        btnText.textContent = 'Delete Task';
        spinner.classList.add('hidden');
    });
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('deleteTaskModal');
    if (event.target === modal) {
        closeDeleteTaskModal();
    }
});

// Close modal on Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modal = document.getElementById('deleteTaskModal');
        if (!modal.classList.contains('hidden')) {
            closeDeleteTaskModal();
        }
    }
});
</script>
