<!-- Create Task Modal -->
<div id="createTaskModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 xl:w-2/5 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Create New Task</h3>
                <button onclick="closeCreateTaskModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="createTaskForm" class="mt-6">
                <input type="hidden" id="task_project_id" name="project_id" value="">

                <!-- Task Title -->
                <div class="mb-4">
                    <label for="task_title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Task Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="task_title" name="title" required maxlength="255"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100"
                           placeholder="Enter task title">
                    <div id="title_error" class="text-red-500 text-sm mt-1 hidden"></div>
                </div>

                <!-- Task Description -->
                <div class="mb-4">
                    <label for="task_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Description
                    </label>
                    <textarea id="task_description" name="description" rows="3" maxlength="1000"
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100"
                              placeholder="Enter task description (optional)"></textarea>
                    <div id="description_error" class="text-red-500 text-sm mt-1 hidden"></div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        <span id="description_count">0</span>/1000 characters
                    </p>
                </div>

                <!-- Priority and Due Date Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <!-- Priority -->
                    <div>
                        <label for="task_priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Priority <span class="text-red-500">*</span>
                        </label>
                        <select id="task_priority" name="priority" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100">
                            <option value="">Select Priority</option>
                            <option value="low">Low Priority</option>
                            <option value="medium">Medium Priority</option>
                            <option value="high">High Priority</option>
                        </select>
                        <div id="priority_error" class="text-red-500 text-sm mt-1 hidden"></div>
                    </div>

                    <!-- Due Date -->
                    <div>
                        <label for="task_due_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Due Date
                        </label>
                        <input type="date" id="task_due_date" name="due_date"
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100">
                        <div id="due_date_error" class="text-red-500 text-sm mt-1 hidden"></div>
                    </div>
                </div>

                <!-- Assign To -->
                <div class="mb-6">
                    <label for="task_assigned_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Assign To
                    </label>
                    <select id="task_assigned_to" name="assigned_to"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100">
                        <option value="">Unassigned</option>
                        <!-- Options will be populated by JavaScript -->
                    </select>
                    <div id="assigned_to_error" class="text-red-500 text-sm mt-1 hidden"></div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Only project members can be assigned to tasks
                    </p>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end pt-4 border-t border-gray-200 dark:border-gray-700 space-x-3">
                    <button type="button" onclick="closeCreateTaskModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit" id="createTaskSubmitBtn"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        <span class="submit-text">Create Task</span>
                        <span class="loading-text hidden">Creating...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let projectMembers = [];

function openCreateTaskModal(projectId, projectName, members) {
    // Set project data
    document.getElementById('task_project_id').value = projectId;
    projectMembers = members || [];

    // Populate assignee dropdown
    populateAssigneeDropdown();

    // Reset form
    resetCreateTaskForm();

    // Show modal
    document.getElementById('createTaskModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    // Focus on title input
    setTimeout(() => {
        document.getElementById('task_title').focus();
    }, 100);
}

function closeCreateTaskModal() {
    document.getElementById('createTaskModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    resetCreateTaskForm();
}

function populateAssigneeDropdown() {
    const select = document.getElementById('task_assigned_to');

    // Clear existing options except the first one
    while (select.children.length > 1) {
        select.removeChild(select.lastChild);
    }

    // Add project members
    projectMembers.forEach(member => {
        const option = document.createElement('option');
        option.value = member.id;
        option.textContent = `${member.name} (${member.role})`;
        select.appendChild(option);
    });
}

function resetCreateTaskForm() {
    const form = document.getElementById('createTaskForm');
    form.reset();

    // Clear all error messages
    document.querySelectorAll('[id$="_error"]').forEach(error => {
        error.classList.add('hidden');
        error.textContent = '';
    });

    // Reset character count
    document.getElementById('description_count').textContent = '0';

    // Reset submit button
    const submitBtn = document.getElementById('createTaskSubmitBtn');
    submitBtn.disabled = false;
    submitBtn.querySelector('.submit-text').classList.remove('hidden');
    submitBtn.querySelector('.loading-text').classList.add('hidden');
}

// Character count for description
document.getElementById('task_description').addEventListener('input', function() {
    const count = this.value.length;
    document.getElementById('description_count').textContent = count;

    if (count > 1000) {
        this.value = this.value.substring(0, 1000);
        document.getElementById('description_count').textContent = '1000';
    }
});

// Form submission (prevent duplicate listeners)
const createTaskForm = document.getElementById('createTaskForm');
if (createTaskForm && !createTaskForm.hasAttribute('data-listener-attached')) {
    createTaskForm.setAttribute('data-listener-attached', 'true');
    createTaskForm.addEventListener('submit', function(e) {
    e.preventDefault();

    const submitBtn = document.getElementById('createTaskSubmitBtn');
    const formData = new FormData(this);

    // Show loading state
    submitBtn.disabled = true;
    submitBtn.querySelector('.submit-text').classList.add('hidden');
    submitBtn.querySelector('.loading-text').classList.remove('hidden');

    // Clear previous errors
    document.querySelectorAll('[id$="_error"]').forEach(error => {
        error.classList.add('hidden');
        error.textContent = '';
    });

    fetch('{{ route("tasks.store") }}', {
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
            closeCreateTaskModal();
            showNotification('success', data.message);

            // Reload the page to show the new task
            if (data.redirect_url) {
                setTimeout(() => {
                    window.location.href = data.redirect_url;
                }, 1000);
            } else {
                location.reload();
            }
        } else {
            // Handle validation errors
            if (data.errors) {
                Object.keys(data.errors).forEach(field => {
                    const errorElement = document.getElementById(field + '_error');
                    if (errorElement) {
                        errorElement.textContent = data.errors[field][0];
                        errorElement.classList.remove('hidden');
                    }
                });
            }

            showNotification('error', data.message || 'Please check your input and try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'An error occurred while creating the task.');
    })
    .finally(() => {
        // Reset submit button
        submitBtn.disabled = false;
        submitBtn.querySelector('.submit-text').classList.remove('hidden');
        submitBtn.querySelector('.loading-text').classList.add('hidden');
                });
        });
    }

// Close modal when clicking outside (prevent duplicate listeners)
const createTaskModal = document.getElementById('createTaskModal');
if (createTaskModal && !createTaskModal.hasAttribute('data-click-listener-attached')) {
    createTaskModal.setAttribute('data-click-listener-attached', 'true');
    createTaskModal.addEventListener('click', function(e) {
        if (e.target === this) {
            closeCreateTaskModal();
        }
    });
}

// Close modal with Escape key (prevent duplicate listeners)
if (!document.body.hasAttribute('data-escape-listener-attached')) {
    document.body.setAttribute('data-escape-listener-attached', 'true');
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !document.getElementById('createTaskModal').classList.contains('hidden')) {
            closeCreateTaskModal();
        }
    });
}
</script>
