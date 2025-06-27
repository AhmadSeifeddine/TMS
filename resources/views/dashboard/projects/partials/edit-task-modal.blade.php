<!-- Edit Task Modal -->
<div id="editTaskModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 xl:w-2/5 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Edit Task</h3>
                <button onclick="closeEditTaskModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="editTaskForm" class="mt-6">
                <input type="hidden" id="edit_task_id" name="task_id" value="">

                <!-- Task Title -->
                <div class="mb-4">
                    <label for="edit_task_title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Task Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="edit_task_title" name="title" required maxlength="255"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100"
                           placeholder="Enter task title">
                    <div id="edit_title_error" class="text-red-500 text-sm mt-1 hidden"></div>
                </div>

                <!-- Task Description -->
                <div class="mb-4">
                    <label for="edit_task_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Description
                    </label>
                    <textarea id="edit_task_description" name="description" rows="3" maxlength="1000"
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100"
                              placeholder="Enter task description (optional)"></textarea>
                    <div id="edit_description_error" class="text-red-500 text-sm mt-1 hidden"></div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        <span id="edit_description_count">0</span>/1000 characters
                    </p>
                </div>

                <!-- Priority, Status, and Due Date Row -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <!-- Priority -->
                    <div>
                        <label for="edit_task_priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Priority <span class="text-red-500">*</span>
                        </label>
                        <select id="edit_task_priority" name="priority" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100">
                            <option value="">Select Priority</option>
                            <option value="low">Low Priority</option>
                            <option value="medium">Medium Priority</option>
                            <option value="high">High Priority</option>
                        </select>
                        <div id="edit_priority_error" class="text-red-500 text-sm mt-1 hidden"></div>
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="edit_task_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select id="edit_task_status" name="status" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100">
                            <option value="">Select Status</option>
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>
                        <div id="edit_status_error" class="text-red-500 text-sm mt-1 hidden"></div>
                    </div>

                    <!-- Due Date -->
                    <div>
                        <label for="edit_task_due_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Due Date
                        </label>
                        <input type="date" id="edit_task_due_date" name="due_date"
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100">
                        <div id="edit_due_date_error" class="text-red-500 text-sm mt-1 hidden"></div>
                    </div>
                </div>

                <!-- Assign To -->
                <div class="mb-6">
                    <label for="edit_task_assigned_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Assign To
                    </label>
                    <select id="edit_task_assigned_to" name="assigned_to"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100">
                        <option value="">Unassigned</option>
                        <!-- Options will be populated by JavaScript -->
                    </select>
                    <div id="edit_assigned_to_error" class="text-red-500 text-sm mt-1 hidden"></div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Only project members can be assigned to tasks
                    </p>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end pt-4 border-t border-gray-200 dark:border-gray-700 space-x-3">
                    <button type="button" onclick="closeEditTaskModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit" id="editTaskSubmitBtn"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        <span class="submit-text">Update Task</span>
                        <span class="loading-text hidden">Updating...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
