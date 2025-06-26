<!-- Manage Team Modal -->
<div id="manageTeamModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-2xl shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100" id="manageTeamModalTitle">
                    Manage Team Members
                </h3>
                <button type="button" onclick="closeManageTeamModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="mt-6">
                <!-- Current Team Members -->
                <div class="mb-6">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-3">Current Team Members</h4>
                    <div id="currentTeamMembers" class="space-y-2">
                        <!-- Current members will be loaded here -->
                    </div>
                </div>

                <!-- Add New Members Section -->
                <div class="mb-6">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-3">Add New Members</h4>

                    <!-- Search Input -->
                    <div class="relative mb-4">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input
                            type="text"
                            id="assigneeSearch"
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            placeholder="Search users by name or email..."
                        >
                    </div>

                    <!-- Available Users -->
                    <div class="border border-gray-200 dark:border-gray-600 rounded-md max-h-60 overflow-y-auto">
                        <div id="availableUsers" class="divide-y divide-gray-200 dark:divide-gray-600">
                            <!-- Available users will be loaded here -->
                        </div>
                    </div>
                </div>

                <!-- Selected Members to Add -->
                <div id="selectedMembersSection" class="mb-6 hidden">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-3">Selected Members to Add</h4>
                    <div id="selectedMembers" class="flex flex-wrap gap-2">
                        <!-- Selected members will appear here -->
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end pt-4 border-t border-gray-200 dark:border-gray-700 space-x-3">
                <button
                    type="button"
                    onclick="closeManageTeamModal()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    Cancel
                </button>
                <button
                    type="button"
                    id="assignMembersBtn"
                    onclick="assignSelectedMembers()"
                    class="px-4 py-2 text-sm font-medium text-white bg-purple-600 border border-transparent rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span id="assignBtnText">Assign Members</span>
                    <svg id="assignBtnSpinner" class="hidden animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Remove Member Confirmation Modal -->
<div id="removeMemberModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-md shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3 text-center">
            <!-- Warning Icon -->
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900">
                <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>

            <!-- Modal Content -->
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mt-4">Remove Team Member</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Are you sure you want to remove <span id="memberToRemoveName" class="font-medium text-gray-900 dark:text-gray-100"></span> from this project? This action cannot be undone.
                </p>
            </div>

            <!-- Modal Actions -->
            <div class="flex items-center justify-center gap-3 mt-4">
                <button
                    type="button"
                    onclick="closeRemoveMemberModal()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    Cancel
                </button>
                <button
                    type="button"
                    id="confirmRemoveBtn"
                    onclick="confirmRemoveMember()"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span id="removeBtnText">Remove Member</span>
                    <svg id="removeBtnSpinner" class="hidden animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentProjectId = null;
let selectedAssignees = new Set();
let availableAssignees = [];
let memberToRemove = null;

function openManageTeamModal(projectId, projectName) {
    currentProjectId = projectId;
    document.getElementById('manageTeamModalTitle').textContent = `Manage Team - ${projectName}`;
    document.getElementById('manageTeamModal').classList.remove('hidden');

    // Reset state
    selectedAssignees.clear();
    updateSelectedMembersDisplay();

    // Load current team members and available assignees
    loadTeamData();
}

function closeManageTeamModal() {
    document.getElementById('manageTeamModal').classList.add('hidden');
    currentProjectId = null;
    selectedAssignees.clear();
    document.getElementById('assigneeSearch').value = '';
}

function loadTeamData() {
    if (!currentProjectId) return;

    // Show loading state
    document.getElementById('currentTeamMembers').innerHTML = '<div class="text-center py-4"><div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 mx-auto"></div></div>';
    document.getElementById('availableUsers').innerHTML = '<div class="text-center py-4"><div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 mx-auto"></div></div>';

    fetch(`/projects/${currentProjectId}/team-data`, {
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
            displayCurrentTeamMembers(data.currentMembers);
            availableAssignees = data.availableUsers;
            displayAvailableUsers(availableAssignees);
        } else {
            showNotification('error', 'Failed to load team data');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'An error occurred while loading team data');
    });
}

function displayCurrentTeamMembers(members) {
    const container = document.getElementById('currentTeamMembers');

    if (members.length === 0) {
        container.innerHTML = '<p class="text-sm text-gray-500 dark:text-gray-400">No team members assigned yet.</p>';
        return;
    }

    const membersHtml = members.map(member => {
        // Role badge colors
        const roleColors = {
            'creator': 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-200',
            'assignee': 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200'
        };

        const roleColor = roleColors[member.role] || 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200';

        return `
        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">${member.name.charAt(0)}</span>
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">${member.name}</p>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${roleColor}">
                            ${member.role.charAt(0).toUpperCase() + member.role.slice(1)}
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">${member.email}</p>
                </div>
            </div>
            <button
                onclick="openRemoveMemberModal(${member.id}, '${member.name.replace(/'/g, "\\'")}')"
                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 p-1 rounded hover:bg-red-50 dark:hover:bg-red-900 transition-colors duration-200"
                title="Remove ${member.name} from project">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;
    }).join('');

    container.innerHTML = membersHtml;
}

function displayAvailableUsers(users) {
    const container = document.getElementById('availableUsers');

    if (users.length === 0) {
        container.innerHTML = '<p class="text-sm text-gray-500 dark:text-gray-400 p-4 text-center">No available users found.</p>';
        return;
    }

    const usersHtml = users.map(user => {
        // Role badge colors
        const roleColors = {
            'creator': 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-200',
            'assignee': 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200'
        };

        const roleColor = roleColors[user.role] || 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200';

        return `
        <div class="p-3 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" onclick="toggleAssigneeFromDiv(event, ${user.id})">
            <div class="flex items-center space-x-3">
                <input
                    type="checkbox"
                    id="assignee_${user.id}"
                    onclick="toggleAssigneeFromCheckbox(event, ${user.id})"
                    class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded cursor-pointer">
                <div class="w-8 h-8 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">${user.name.charAt(0)}</span>
                </div>
                <div class="flex-1">
                    <div class="flex items-center space-x-2">
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">${user.name}</p>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${roleColor}">
                            ${user.role.charAt(0).toUpperCase() + user.role.slice(1)}
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">${user.email}</p>
                </div>
            </div>
        </div>
    `;
    }).join('');

    container.innerHTML = usersHtml;
}

function toggleAssigneeFromDiv(event, assigneeId) {
    // Prevent this from firing if the checkbox itself was clicked
    if (event.target.type === 'checkbox') {
        return;
    }

    const checkbox = document.getElementById(`assignee_${assigneeId}`);
    checkbox.checked = !checkbox.checked;

    if (checkbox.checked) {
        selectedAssignees.add(assigneeId);
    } else {
        selectedAssignees.delete(assigneeId);
    }

    updateSelectedMembersDisplay();
}

function toggleAssigneeFromCheckbox(event, assigneeId) {
    // Stop the event from bubbling up to the parent div
    event.stopPropagation();

    const checkbox = event.target;

    if (checkbox.checked) {
        selectedAssignees.add(assigneeId);
    } else {
        selectedAssignees.delete(assigneeId);
    }

    updateSelectedMembersDisplay();
}

function updateSelectedMembersDisplay() {
    const section = document.getElementById('selectedMembersSection');
    const container = document.getElementById('selectedMembers');

    if (selectedAssignees.size === 0) {
        section.classList.add('hidden');
        return;
    }

    section.classList.remove('hidden');

    const selectedHtml = Array.from(selectedAssignees).map(assigneeId => {
        const assignee = availableAssignees.find(a => a.id === assigneeId);
        if (!assignee) return '';

        return `
            <span class="inline-flex items-center pl-3 pr-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-200">
                ${assignee.name}
                <button type="button" onclick="removeSelectedAssignee(${assigneeId})" class="flex-shrink-0 ml-1 h-4 w-4 rounded-full inline-flex items-center justify-center text-purple-400 hover:bg-purple-200 hover:text-purple-500 focus:outline-none focus:bg-purple-500 focus:text-white">
                    <svg class="h-2 w-2" stroke="currentColor" fill="none" viewBox="0 0 8 8">
                        <path stroke-linecap="round" stroke-width="1.5" d="m1 1 6 6m0-6-6 6"/>
                    </svg>
                </button>
            </span>
        `;
    }).join('');

    container.innerHTML = selectedHtml;
}

function removeSelectedAssignee(assigneeId) {
    selectedAssignees.delete(assigneeId);
    const checkbox = document.getElementById(`assignee_${assigneeId}`);
    if (checkbox) checkbox.checked = false;
    updateSelectedMembersDisplay();
}

function assignSelectedMembers() {
    if (selectedAssignees.size === 0) {
        showNotification('warning', 'Please select at least one member to assign');
        return;
    }

    const btn = document.getElementById('assignMembersBtn');
    const btnText = document.getElementById('assignBtnText');
    const btnSpinner = document.getElementById('assignBtnSpinner');

    // Show loading state
    btn.disabled = true;
    btnText.textContent = 'Assigning...';
    btnSpinner.classList.remove('hidden');

    fetch(`/projects/${currentProjectId}/assign-members`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            assignee_ids: Array.from(selectedAssignees)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('success', data.message);
            // Reset selection and reload team data instead of full page reload
            selectedAssignees.clear();
            updateSelectedMembersDisplay();
            loadTeamData();
        } else {
            showNotification('error', data.message || 'Failed to assign members');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'An error occurred while assigning members');
    })
    .finally(() => {
        // Reset button state
        btn.disabled = false;
        btnText.textContent = 'Assign Members';
        btnSpinner.classList.add('hidden');
    });
}

// Remove Member Modal Functions
function openRemoveMemberModal(memberId, memberName) {
    memberToRemove = { id: memberId, name: memberName };
    document.getElementById('memberToRemoveName').textContent = memberName;
    document.getElementById('removeMemberModal').classList.remove('hidden');
}

function closeRemoveMemberModal() {
    document.getElementById('removeMemberModal').classList.add('hidden');
    memberToRemove = null;
}

function confirmRemoveMember() {
    if (!memberToRemove) return;

    const btn = document.getElementById('confirmRemoveBtn');
    const btnText = document.getElementById('removeBtnText');
    const btnSpinner = document.getElementById('removeBtnSpinner');

    // Show loading state
    btn.disabled = true;
    btnText.textContent = 'Removing...';
    btnSpinner.classList.remove('hidden');

    fetch(`/projects/${currentProjectId}/remove-member`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            member_id: memberToRemove.id
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('success', data.message);
            closeRemoveMemberModal();
            loadTeamData(); // Reload team data
        } else {
            showNotification('error', data.message || 'Failed to remove member');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'An error occurred while removing member');
    })
    .finally(() => {
        // Reset button state
        btn.disabled = false;
        btnText.textContent = 'Remove Member';
        btnSpinner.classList.add('hidden');
    });
}

// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('assigneeSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const filtered = availableAssignees.filter(user =>
                user.name.toLowerCase().includes(searchTerm) ||
                user.email.toLowerCase().includes(searchTerm)
            );
            displayAvailableUsers(filtered);
        });
    }
});
</script>
