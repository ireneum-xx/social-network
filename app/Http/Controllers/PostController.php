<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Mostrar una lista de todas las publicaciones.
     */
    public function index()
    {
        $posts = Post::with('user')->orderBy('created_at', 'desc')->get();
        return response()->json($posts);
    }

    /**
     * Almacenar una nueva publicación.
     */
    public function store(Request $request)
    {
        // Validar los datos de entrada
        $request->validate([
            'image' => 'required|image|mimes:jpg,png,jpeg,gif|max:2048', // Validación de imagen
            'caption' => 'nullable|string',
        ]);

        // Guardar la imagen en `storage/app/public/posts`
        $path = $request->file('image')->store('posts', 'public');

        // Crear la publicación
        $post = Post::create([
            'user_id' => Auth::id(),
            'image_path' => $path,
            'caption' => $request->caption,
        ]);

        return response()->json(['message' => 'Publicación creada exitosamente', 'post' => $post], 201);
    }

    /**
     * Mostrar una publicación específica.
     */
    public function show(string $id)
    {
        $post = Post::with('user')->findOrFail($id); // Incluir la relación con el usuario
        return response()->json($post);
    }

    /**
     * Actualizar una publicación existente.
     */
    public function update(Request $request, string $id)
    {
        // Buscar la publicación
        $post = Post::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        // Validar los datos de entrada
        $request->validate([
            'caption' => 'nullable|string',
        ]);

        // Actualizar la publicación
        $post->update([
            'caption' => $request->caption ?? $post->caption,
        ]);

        return response()->json(['message' => 'Publicación actualizada exitosamente', 'post' => $post]);
    }

    /**
     * Eliminar una publicación.
     */
    public function destroy(string $id)
    {
        // Buscar la publicación
        $post = Post::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        // Eliminar la imagen asociada si existe
        if ($post->image_path && Storage::disk('public')->exists($post->image_path)) {
            Storage::disk('public')->delete($post->image_path);
        }

        // Eliminar la publicación
        $post->delete();

        return response()->json(['message' => 'Publicación eliminada exitosamente']);
    }

    /**
     * Obtener las publicaciones del usuario autenticado.
     */
    public function myPosts()
    {
        $posts = Post::where('user_id', Auth::id())->orderBy('created_at', 'desc')->get();
        return response()->json($posts);
    }
}