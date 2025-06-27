<script>
let editTaskProjectMembers = [];

function openEditTaskModal(taskId) {
    fetch(`/tasks/${taskId}/edit`, {
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
            document.getElementById('edit_task_id').value = data.task.id;
            document.getElementById('edit_task_title').value = data.task.title;
            document.getElementById('edit_task_description').value = data.task.description || '';
            document.getElementById('edit_task_priority').value = data.task.priority;
            document.getElementById('edit_task_status').value = data.task.status;
            document.getElementById('edit_task_due_date').value = data.task.due_date || '';

            updateEditDescriptionCount();
            editTaskProjectMembers = data.project_members || [];
            populateEditAssigneeDropdown(data.task.assigned_to);
            resetEditTaskForm();

            document.getElementById('editTaskModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            setTimeout(() => {
                document.getElementById('edit_task_title').focus();
            }, 100);
        } else {
            showNotification('error', data.message || 'Failed to load task data.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'An error occurred while loading the task.');
    });
}

function closeEditTaskModal() {
    document.getElementById('editTaskModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    resetEditTaskForm();
}

function populateEditAssigneeDropdown(currentAssignedTo) {
    const select = document.getElementById('edit_task_assigned_to');

    while (select.children.length > 1) {
        select.removeChild(select.lastChild);
    }

    editTaskProjectMembers.forEach(member => {
        const option = document.createElement('option');
        option.value = member.id;
        option.textContent = `${member.name} (${member.role})`;
        if (member.id == currentAssignedTo) {
            option.selected = true;
        }
        select.appendChild(option);
    });
}

function resetEditTaskForm() {
    document.querySelectorAll('[id^="edit_"][id$="_error"]').forEach(error => {
        error.classList.add('hidden');
        error.textContent = '';
    });

    const submitBtn = document.getElementById('editTaskSubmitBtn');
    if (submitBtn) {
        submitBtn.disabled = false;
        const submitText = submitBtn.querySelector('.submit-text');
        const loadingText = submitBtn.querySelector('.loading-text');
        if (submitText) submitText.classList.remove('hidden');
        if (loadingText) loadingText.classList.add('hidden');
    }
}

function updateEditDescriptionCount() {
    const textarea = document.getElementById('edit_task_description');
    if (textarea) {
        const count = textarea.value.length;
        const countElement = document.getElementById('edit_description_count');
        if (countElement) {
            countElement.textContent = count;
        }

        if (count > 1000) {
            textarea.value = textarea.value.substring(0, 1000);
            if (countElement) {
                countElement.textContent = '1000';
            }
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const editDescriptionTextarea = document.getElementById('edit_task_description');
    if (editDescriptionTextarea) {
        editDescriptionTextarea.addEventListener('input', updateEditDescriptionCount);
    }

    const editTaskForm = document.getElementById('editTaskForm');
    if (editTaskForm) {
        editTaskForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const submitBtn = document.getElementById('editTaskSubmitBtn');
            const formData = new FormData(this);
            const taskId = document.getElementById('edit_task_id').value;

            submitBtn.disabled = true;
            const submitText = submitBtn.querySelector('.submit-text');
            const loadingText = submitBtn.querySelector('.loading-text');
            if (submitText) submitText.classList.add('hidden');
            if (loadingText) loadingText.classList.remove('hidden');

            document.querySelectorAll('[id^="edit_"][id$="_error"]').forEach(error => {
                error.classList.add('hidden');
                error.textContent = '';
            });

            fetch(`/tasks/${taskId}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-HTTP-Method-Override': 'PUT'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeEditTaskModal();
                    showNotification('success', data.message);

                    if (data.redirect_url) {
                        setTimeout(() => {
                            window.location.href = data.redirect_url;
                        }, 1000);
                    } else {
                        location.reload();
                    }
                } else {
                    if (data.errors) {
                        Object.keys(data.errors).forEach(field => {
                            const errorElement = document.getElementById('edit_' + field + '_error');
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
                showNotification('error', 'An error occurred while updating the task.');
            })
            .finally(() => {
                submitBtn.disabled = false;
                if (submitText) submitText.classList.remove('hidden');
                if (loadingText) loadingText.classList.add('hidden');
            });
        });
    }

    const editTaskModal = document.getElementById('editTaskModal');
    if (editTaskModal) {
        editTaskModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditTaskModal();
            }
        });
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !document.getElementById('editTaskModal').classList.contains('hidden')) {
            closeEditTaskModal();
        }
    });
});
</script>
