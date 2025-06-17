<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Validator;
use App\Models\Order;
use App\Models\Comment;
use App\Models\Meal;
use Carbon\Carbon;

class CommentController extends Controller
{

public function addCommentToMeal(Request $request, $mealId)
{
    $request->validate([
        'comment' => 'required|string',
    ]);

    $userId = auth()->user()->id;

    $order = Order::where('user_id', $userId)
        ->whereHas('meals', function ($query) use ($mealId) {
            $query->where('meal_id', $mealId);
        })
        ->first();

    if (!$order) {
        return response()->json('You are not authorized to comment on this meal', 403);
    }

    $comment = new Comment;
    $comment->comment = $request->input('comment');
    $comment->user_id = $userId;
    $comment->comment_date=Carbon::now()->tz('Europe/London')->addHours(3)->format('Y-m-d H:i A');
    $comment->meal_id = $mealId;
    $comment->save();

    return response()->json('Comment added successfully', 200);
}

public function updateComment(Request $request, $commentId)
{
    $request->validate([
        'comment' => 'required|string',
    ]);

    $userId = auth()->user()->id;

    $comment = Comment::where('id', $commentId)
        ->whereHas('user', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->first();

    if (!$comment) {
        return response()->json('You are not authorized to update this comment', 403);
    }

    $comment->comment = $request->input('comment');
    $comment->comment_date=Carbon::now()->tz('Europe/London')->addHours(3)->format('Y-m-d H:i A');
    $comment->save();

    return response()->json('Comment updated successfully', 200);
}

public function deleteComment($commentId)
{
    $comment = Comment::where('id', $commentId)->delete();
        

    if (!$comment) {
        return response()->json('Comment not found or you are not authorized to delete it', 404);
    }

    return response()->json('Comment deleted successfully', 200);
}
public function showMealComments($mealId)
{
    $comments = Comment::where('meal_id', $mealId)
        ->with([
            'user' => function ($query) {
                $query->select('id', 'name');
            },
            'user.stars' => function ($query) use ($mealId) {
                $query->where('meal_id', $mealId);
            }
        ])
        ->get();

    return response()->json($comments, 200);
}
}
