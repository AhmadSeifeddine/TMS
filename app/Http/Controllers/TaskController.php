<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    /**
     * Get comments for a task with pagination
     */
    public function getComments(Request $request, Task $task): JsonResponse
    {
        try {
            // Validate request parameters
            $validated = $request->validate([
                'offset' => 'integer|min:0',
                'limit' => 'integer|min:1|max:10'
            ]);

            $offset = $validated['offset'] ?? 0;
            $limit = $validated['limit'] ?? 5;

            // Get comments with creator information
            $comments = $task->taskComments()
                ->with('creator:id,name')
                ->orderBy('created_at', 'asc')
                ->offset($offset)
                ->limit($limit)
                ->get();

            // Get total comments count
            $totalComments = $task->taskComments()->count();

            // Render the comments HTML server-side
            $commentsHtml = view('components.partials.comment-list', [
                'comments' => $comments
            ])->render();

            // Calculate remaining comments and button text
            $newOffset = $offset + $comments->count();
            $remainingComments = $totalComments - $newOffset;

            $buttonText = '';
            if ($remainingComments > 0) {
                if ($remainingComments <= 5) {
                    $buttonText = "View {$remainingComments} more comment" . ($remainingComments > 1 ? 's' : '');
                } else {
                    $buttonText = 'Load more comments';
                }
            }

            return response()->json([
                'success' => true,
                'comments_html' => $commentsHtml,
                'total_comments' => $totalComments,
                'new_offset' => $newOffset,
                'remaining_comments' => $remainingComments,
                'button_text' => $buttonText,
                'has_more' => $remainingComments > 0
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request parameters.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load comments.',
                'error' => 'An error occurred while loading comments.'
            ], 500);
        }
    }
}
