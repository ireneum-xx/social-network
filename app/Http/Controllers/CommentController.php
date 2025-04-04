<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        Comment::create([
            'user_id' => Auth::id(),
            'post_id' => $postId,
            'content' => $request->content,
        ]);

        return response()->json(['message' => 'Comentario agregado'], 201);
    }

    public function getComments($postId)
    {
        $comments = Comment::where('post_id', $postId)->with('user')->get();
        return response()->json($comments);
    }
}

