@props(['priority'])

@php
    $priorityConfig = [
        'low' => [
            'color' => 'bg-slate-100 text-slate-600 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
            'icon' => 'M19 14l-7 7m0 0l-7-7m7 7V3',
            'text' => 'Low',
            'dot' => 'bg-slate-400'
        ],
        'medium' => [
            'color' => 'bg-yellow-100 text-yellow-700 border-yellow-200 dark:bg-yellow-900/20 dark:text-yellow-300 dark:border-yellow-800',
            'icon' => 'M5 12h14',
            'text' => 'Medium',
            'dot' => 'bg-yellow-500'
        ],
        'high' => [
            'color' => 'bg-red-100 text-red-700 border-red-200 dark:bg-red-900/20 dark:text-red-300 dark:border-red-800',
            'icon' => 'M5 10l7-7m0 0l7 7m-7-7v18',
            'text' => 'High',
            'dot' => 'bg-red-500'
        ]
    ];

    $config = $priorityConfig[$priority] ?? $priorityConfig['medium'];
@endphp

<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border {{ $config['color'] }}">
    <span class="w-1.5 h-1.5 rounded-full {{ $config['dot'] }} mr-1.5"></span>
    {{ $config['text'] }}
</span>
