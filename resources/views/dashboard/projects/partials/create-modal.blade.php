<!-- Create Project Modal -->
<div id="createModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-80 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-700">
            <form id="createProjectForm">
                <div class="bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-900 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-100" id="modal-title">
                                Create New Project
                            </h3>
                            <div class="mt-4">
                                <!-- Error Messages -->
                                <div id="createErrorMessages" class="hidden mb-4 p-4 bg-red-900 border border-red-700 rounded-md">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-red-200">There were errors with your submission:</h3>
                                            <div class="mt-2 text-sm text-red-300" id="createErrorList"></div>
                                        </div>
                                    </div>
                                </div>

                                                                <!-- Form Fields -->
                                <div class="space-y-5">
                                    <div>
                                        <label for="createProjectName" class="block text-sm font-medium text-gray-300 mb-2">Project Name *</label>
                                        <input type="text" id="createProjectName" name="name" required
                                               class="mt-1 block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-gray-100 placeholder-gray-400"
                                               placeholder="Enter project name">
                                        <div class="text-red-400 text-sm mt-1 hidden" id="createProjectNameError"></div>
                                    </div>

                                    <div>
                                        <label for="createProjectDescription" class="block text-sm font-medium text-gray-300 mb-2">Description *</label>
                                        <textarea id="createProjectDescription" name="description" rows="4" required
                                                  class="mt-1 block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-gray-100 placeholder-gray-400"
                                                  placeholder="Describe your project goals, requirements, and key features..."></textarea>
                                        <div class="text-red-400 text-sm mt-1 hidden" id="createProjectDescriptionError"></div>
                                    </div>

                                    <div>
                                        <label for="createProjectStatus" class="block text-sm font-medium text-gray-300 mb-2">Initial Status</label>
                                        <select id="createProjectStatus" name="status"
                                                class="mt-1 block w-full bg-gray-700 border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-gray-100">
                                            <option value="active" selected>Active</option>
                                            <option value="completed">Completed</option>
                                            <option value="archived">Archived</option>
                                        </select>
                                        <div class="text-red-400 text-sm mt-1 hidden" id="createProjectStatusError"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                                </div>
                <div class="bg-gray-900 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-700">
                    <button type="submit" id="createProjectBtn"
                            class="w-full inline-flex justify-center items-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 focus:ring-offset-gray-800 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                        <svg class="create-btn-spinner animate-spin -ml-1 mr-2 h-4 w-4 text-white hidden" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="create-btn-text">Create Project</span>
                    </button>
                    <button type="button" onclick="closeCreateModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-600 shadow-sm px-4 py-2 bg-gray-700 text-base font-medium text-gray-300 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 focus:ring-offset-gray-800 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors duration-200">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openCreateModal() {
    document.getElementById('createModal').classList.remove('hidden');
    resetCreateForm();
}

function closeCreateModal() {
    document.getElementById('createModal').classList.add('hidden');
    resetCreateForm();
}

function resetCreateForm() {
    document.getElementById('createProjectForm').reset();
    clearCreateErrors();
    document.getElementById('createProjectStatus').value = 'active';
}

function clearCreateErrors() {
    document.getElementById('createErrorMessages').classList.add('hidden');
    const errorElements = document.querySelectorAll('[id$="Error"]');
    errorElements.forEach(element => {
        element.classList.add('hidden');
        element.textContent = '';
    });
}

document.getElementById('createProjectForm').addEventListener('submit', function(e) {
    e.preventDefault();
    submitCreateForm();
});

function submitCreateForm() {
    const btn = document.getElementById('createProjectBtn');
    const btnText = btn.querySelector('.create-btn-text');
    const btnSpinner = btn.querySelector('.create-btn-spinner');

    // Show loading state
    btn.disabled = true;
    btnText.textContent = 'Creating...';
    btnSpinner.classList.remove('hidden');
    clearCreateErrors();

    // Collect form data
    const formData = new FormData();
    formData.append('name', document.getElementById('createProjectName').value);
    formData.append('description', document.getElementById('createProjectDescription').value);
    formData.append('status', document.getElementById('createProjectStatus').value);

    // Add CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        formData.append('_token', csrfToken.getAttribute('content'));
    }

    fetch('/projects', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeCreateModal();
            // Show success notification using our flash message system
            showNotification('success', data.message || 'Project created successfully!');
            // Reload the page to show the new project
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            displayCreateErrors(data.errors || {});
        }
    })
    .catch(error => {
        console.error('Error creating project:', error);
        showNotification('error', 'An error occurred while creating the project. Please try again.');
        displayCreateErrors({ general: ['An error occurred while creating the project. Please try again.'] });
    })
    .finally(() => {
        // Reset loading state
        btn.disabled = false;
        btnText.textContent = 'Create Project';
        btnSpinner.classList.add('hidden');
    });
}

function displayCreateErrors(errors) {
    const errorContainer = document.getElementById('createErrorMessages');
    const errorList = document.getElementById('createErrorList');

    let hasErrors = false;
    let errorMessages = [];

    // Display field-specific errors
    Object.keys(errors).forEach(field => {
        const errorElement = document.getElementById(`create${field.charAt(0).toUpperCase() + field.slice(1)}Error`);
        if (errorElement && errors[field].length > 0) {
            errorElement.textContent = errors[field][0];
            errorElement.classList.remove('hidden');
            hasErrors = true;
        }
        errorMessages = errorMessages.concat(errors[field]);
    });

    // Show general error container
    if (hasErrors) {
        errorList.innerHTML = '<ul class="list-disc list-inside">' +
            errorMessages.map(msg => `<li>${msg}</li>`).join('') +
            '</ul>';
        errorContainer.classList.remove('hidden');
    }
}

// Function to show notifications (integrates with our notification system)
function showNotification(type, message) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden transform transition-all duration-300 ease-in-out`;

    const iconColors = {
        success: 'text-green-400',
        error: 'text-red-400',
        warning: 'text-yellow-400',
        info: 'text-blue-400'
    };

    const icons = {
        success: `<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>`,
        error: `<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>`,
        warning: `<path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>`,
        info: `<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>`
    };

    notification.innerHTML = `
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 ${iconColors[type]}" fill="currentColor" viewBox="0 0 20 20">
                        ${icons[type]}
                    </svg>
                </div>
                <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p class="text-sm font-medium text-gray-900">${message}</p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" onclick="this.parentElement.parentElement.parentElement.parentElement.remove()">
                        <span class="sr-only">Close</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    `;

    // Add to DOM
    document.body.appendChild(notification);

    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);

    // Auto remove after 5 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 300);
    }, 5000);
}
</script>
