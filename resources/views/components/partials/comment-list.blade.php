@foreach($comments as $comment)
    <div class="comment-item bg-white dark:bg-gray-700 rounded-lg p-3 border border-gray-200 dark:border-gray-600">
        <div class="flex space-x-3">
            <div class="w-6 h-6 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0 shadow-sm">
                <span class="text-xs font-medium text-white">
                    {{ substr($comment->creator->name ?? 'U', 0, 1) }}
                </span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ $comment->creator->name ?? 'Unknown' }}
                    </span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $comment->created_at->diffForHumans() }}
                    </span>
                </div>
                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                    {{ $comment->comment }}
                </p>
            </div>
        </div>
    </div>
@endforeach
