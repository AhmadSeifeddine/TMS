<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="flex-shrink-0">

                            @if(auth()->user()->image)
                                <img class="h-16 w-16 rounded-full object-cover border-2 border-gray-300 dark:border-gray-600"
                                    src="{{ Auth::user()->image }}">
                                <div class="h-16 w-16 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center border-2 border-gray-400 dark:border-gray-500" style="display: none;">
                                    <span class="text-gray-600 dark:text-gray-400 font-medium text-lg">
                                        {{ substr(auth()->user()->name, 0, 1) }}
                                    </span>
                                </div>
                            @else
                                <div class="h-16 w-16 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center border-2 border-gray-400 dark:border-gray-500">
                                    <span class="text-gray-600 dark:text-gray-400 font-medium text-lg">
                                        {{ substr(auth()->user()->name, 0, 1) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                                Welcome back, {{ auth()->user()->name }}!
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400">
                                You're successfully logged in to your dashboard.
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                            <h4 class="font-semibold text-blue-900 dark:text-blue-100 mb-2">Projects</h4>
                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">0</p>
                            <p class="text-sm text-blue-700 dark:text-blue-300">Total projects</p>
                        </div>

                        <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                            <h4 class="font-semibold text-green-900 dark:text-green-100 mb-2">Tasks</h4>
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400">0</p>
                            <p class="text-sm text-green-700 dark:text-green-300">Assigned tasks</p>
                        </div>

                        <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg">
                            <h4 class="font-semibold text-purple-900 dark:text-purple-100 mb-2">Comments</h4>
                            <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">0</p>
                            <p class="text-sm text-purple-700 dark:text-purple-300">Total comments</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
