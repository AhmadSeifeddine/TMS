@props([
    'currentStatus' => 'all',
    'taskCounts' => [],
    'projectId' => null
])

@php
    $statusOptions = [
        'all' => [
            'label' => 'All Tasks',
            'icon' => 'M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
            'color' => 'text-gray-600 dark:text-gray-400',
            'bgColor' => 'bg-gray-50 dark:bg-gray-700'
        ],
        'pending' => [
            'label' => 'Pending',
            'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
            'color' => 'text-amber-600 dark:text-amber-400',
            'bgColor' => 'bg-amber-50 dark:bg-amber-900/20'
        ],
        'in_progress' => [
            'label' => 'In Progress',
            'icon' => 'M13 10V3L4 14h7v7l9-11h-7z',
            'color' => 'text-blue-600 dark:text-blue-400',
            'bgColor' => 'bg-blue-50 dark:bg-blue-900/20'
        ],
        'completed' => [
            'label' => 'Completed',
            'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            'color' => 'text-emerald-600 dark:text-emerald-400',
            'bgColor' => 'bg-emerald-50 dark:bg-emerald-900/20'
        ]
    ];

    $currentOption = $statusOptions[$currentStatus] ?? $statusOptions['all'];
@endphp

<div class="relative inline-block text-left" x-data="{ open: false }">
    <!-- Dropdown Button -->
    <div>
        <button
            type="button"
            @click="open = !open"
            @keydown.escape="open = false"
            class="inline-flex items-center justify-between w-full px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 min-w-[180px]"
            aria-expanded="true"
            aria-haspopup="true">

            <div class="flex items-center">
                <div class="flex-shrink-0 w-8 h-8 {{ $currentOption['bgColor'] }} rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-4 h-4 {{ $currentOption['color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currentOption['icon'] }}"></path>
                    </svg>
                </div>
                <div class="text-left">
                    <div class="font-medium text-gray-900 dark:text-gray-100">
                        {{ $currentOption['label'] }}
                    </div>
                    @if(isset($taskCounts[$currentStatus]))
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $taskCounts[$currentStatus] }} task{{ $taskCounts[$currentStatus] != 1 ? 's' : '' }}
                        </div>
                    @endif
                </div>
            </div>

            <svg class="w-5 h-5 ml-2 -mr-1 text-gray-400 transition-transform duration-200" :class="{'rotate-180': open}" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>
    </div>

    <!-- Dropdown Menu -->
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        @click.outside="open = false"
        class="absolute right-0 z-10 mt-2 w-72 origin-top-right bg-white dark:bg-gray-800 rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 border border-gray-200 dark:border-gray-700 focus:outline-none"
        role="menu"
        aria-orientation="vertical"
        aria-labelledby="menu-button"
        tabindex="-1">

        <div class="py-1" role="none">
            @foreach($statusOptions as $status => $option)
                <a
                    href="{{ $projectId ? route('projects.show', ['project' => $projectId, 'status' => $status]) : '#' }}"
                    @click="open = false"
                    class="group flex items-center w-full px-4 py-3 text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150 {{ $currentStatus === $status ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}"
                    role="menuitem">

                    <div class="flex-shrink-0 w-8 h-8 {{ $option['bgColor'] }} rounded-lg flex items-center justify-center mr-3 group-hover:scale-105 transition-transform duration-150">
                        <svg class="w-4 h-4 {{ $option['color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $option['icon'] }}"></path>
                        </svg>
                    </div>

                    <div class="flex-1 text-left">
                        <div class="font-medium text-gray-900 dark:text-gray-100 {{ $currentStatus === $status ? 'text-blue-600 dark:text-blue-400' : '' }}">
                            {{ $option['label'] }}
                        </div>
                        @if(isset($taskCounts[$status]))
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $taskCounts[$status] }} task{{ $taskCounts[$status] != 1 ? 's' : '' }}
                            </div>
                        @endif
                    </div>

                    @if($currentStatus === $status)
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 ml-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</div>
