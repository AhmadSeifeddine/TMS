<script>
let createTaskProjectMembers = [];

function openCreateTaskModal(projectId, projectName, members) {
    // Set project data
    document.getElementById('task_project_id').value = projectId;
    createTaskProjectMembers = members || [];

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
    createTaskProjectMembers.forEach(member => {
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
document.addEventListener('DOMContentLoaded', function() {
    const descriptionTextarea = document.getElementById('task_description');
    if (descriptionTextarea) {
        descriptionTextarea.addEventListener('input', function() {
            const count = this.value.length;
            document.getElementById('description_count').textContent = count;

            if (count > 1000) {
                this.value = this.value.substring(0, 1000);
                document.getElementById('description_count').textContent = '1000';
            }
        });
    }

    // Form submission
    const createTaskForm = document.getElementById('createTaskForm');
    if (createTaskForm) {
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

    // Close modal when clicking outside
    const createTaskModal = document.getElementById('createTaskModal');
    if (createTaskModal) {
        createTaskModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeCreateTaskModal();
            }
        });
    }

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !document.getElementById('createTaskModal').classList.contains('hidden')) {
            closeCreateTaskModal();
        }
    });
});
</script>
