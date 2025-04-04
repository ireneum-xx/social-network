<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    /**
     * Dar "like" a una publicación.
     */
    public function likePost($postId)
    {
        // Verificar que la publicación exista
        $post = Post::findOrFail($postId);

        // Verificar si el usuario ya dio "like"
        if (Like::where('user_id', Auth::id())->where('post_id', $postId)->exists()) {
            return response()->json(['message' => 'Ya diste like a esta publicación'], 400);
        }

        // Crear el "like"
        Like::create([
            'user_id' => Auth::id(),
            'post_id' => $postId,
        ]);

        return response()->json(['message' => 'Like agregado'], 201);
    }

    /**
     * Quitar "like" de una publicación.
     */
    public function unlikePost($postId)
    {
        // Buscar el "like" del usuario en la publicación
        $like = Like::where('user_id', Auth::id())->where('post_id', $postId)->first();

        // Si no existe, devolver un error
        if (!$like) {
            return response()->json(['message' => 'No has dado like a esta publicación'], 400);
        }

        // Eliminar el "like"
        $like->delete();

        return response()->json(['message' => 'Like eliminado'], 200);
    }

    /**
     * Obtener el número total de "likes" de una publicación.
     */
    public function getPostLikes($postId)
    {
        // Verificar que la publicación exista
        $post = Post::findOrFail($postId);

        // Contar los "likes"
        $likesCount = Like::where('post_id', $postId)->count();

        return response()->json(['likes' => $likesCount]);
    }
}
