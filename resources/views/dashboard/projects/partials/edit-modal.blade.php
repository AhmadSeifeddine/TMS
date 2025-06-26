<!-- Edit Project Modal -->
<div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeEditModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="editProjectForm">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 dark:bg-yellow-900 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">
                                Edit Project
                            </h3>
                            <div class="mt-4" id="editModalContent">
                                <!-- Edit form will be loaded here via JavaScript -->
                                <div class="flex items-center justify-center py-8">
                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-yellow-600"></div>
                                    <span class="ml-3 text-sm text-gray-500 dark:text-gray-400">Loading edit form...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" id="updateButton" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-yellow-600 text-base font-medium text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        <span class="update-text">Update Project</span>
                        <span class="update-loading hidden">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Updating...
                        </span>
                    </button>
                    <button type="button" onclick="closeEditModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentEditProjectId = null;

function openEditModal(projectId) {
    currentEditProjectId = projectId;
    document.getElementById('editModal').classList.remove('hidden');

    // Fetch project details for editing
    fetch(`/projects/${projectId}/edit`, {
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
            const project = data.project;
            document.getElementById('editModalContent').innerHTML = `
                <div class="space-y-4">
                    <div>
                        <label for="editProjectName" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Project Name *</label>
                        <input type="text" id="editProjectName" name="name" value="${project.name}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm" required>
                        <div id="nameError" class="mt-1 text-sm text-red-600 hidden"></div>
                    </div>

                    <div>
                        <label for="editProjectDescription" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description *</label>
                        <textarea id="editProjectDescription" name="description" rows="4" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm" required>${project.description}</textarea>
                        <div id="descriptionError" class="mt-1 text-sm text-red-600 hidden"></div>
                    </div>

                    <div>
                        <label for="editProjectStatus" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status *</label>
                        <select id="editProjectStatus" name="status" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm" required>
                            <option value="active" ${project.status === 'active' ? 'selected' : ''}>Active</option>
                            <option value="completed" ${project.status === 'completed' ? 'selected' : ''}>Completed</option>
                            <option value="archived" ${project.status === 'archived' ? 'selected' : ''}>Archived</option>
                        </select>
                        <div id="statusError" class="mt-1 text-sm text-red-600 hidden"></div>
                    </div>
                </div>
            `;
        } else {
            document.getElementById('editModalContent').innerHTML = `
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Error loading project</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Unable to load project for editing. Please try again.</p>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('editModalContent').innerHTML = `
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Connection error</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Unable to connect to server. Please check your connection and try again.</p>
            </div>
        `;
    });
}

function closeEditModal() {
    currentEditProjectId = null;
    document.getElementById('editModal').classList.add('hidden');
    clearValidationErrors();
}

function clearValidationErrors() {
    const errorElements = ['nameError', 'descriptionError', 'statusError'];
    errorElements.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.classList.add('hidden');
            element.textContent = '';
        }
    });
}

// Handle form submission
document.getElementById('editProjectForm').addEventListener('submit', function(e) {
    e.preventDefault();

    if (!currentEditProjectId) return;

    const updateButton = document.getElementById('updateButton');
    const updateText = updateButton.querySelector('.update-text');
    const updateLoading = updateButton.querySelector('.update-loading');

    // Show loading state
    updateButton.disabled = true;
    updateText.classList.add('hidden');
    updateLoading.classList.remove('hidden');

    clearValidationErrors();

    const formData = new FormData();
    formData.append('name', document.getElementById('editProjectName').value);
    formData.append('description', document.getElementById('editProjectDescription').value);
    formData.append('status', document.getElementById('editProjectStatus').value);
    formData.append('_method', 'PUT');

    fetch(`/projects/${currentEditProjectId}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success notification
            showNotification('success', data.message);
            closeEditModal();
            // Reload the page to show updated data
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            // Handle validation errors
            if (data.errors) {
                Object.keys(data.errors).forEach(field => {
                    const errorElement = document.getElementById(`${field}Error`);
                    if (errorElement) {
                        errorElement.textContent = data.errors[field][0];
                        errorElement.classList.remove('hidden');
                    }
                });
            }
            if (data.message) {
                showNotification('error', data.message);
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'An error occurred while updating the project. Please try again.');
    })
    .finally(() => {
        // Reset loading state
        updateButton.disabled = false;
        updateText.classList.remove('hidden');
        updateLoading.classList.add('hidden');
    });
});
</script>
