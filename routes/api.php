<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\UserController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/user-profile/{id}', [UserProfileController::class, 'show']);
Route::put('/user-profile/{id}', [UserProfileController::class, 'update']);


Route::middleware('auth:sanctum')->group(function () {
    // Rutas de usuario
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/user/profile', [UserController::class, 'updateProfile']);

    // Rutas de posts
    Route::post('/posts', [PostController::class, 'store']);
    Route::get('/posts', [PostController::class, 'index']);
    Route::get('/my-posts', [PostController::class, 'myPosts']);

    // Rutas de likes
    Route::post('/posts/{postId}/like', [LikeController::class, 'likePost']);
    Route::delete('/posts/{postId}/unlike', [LikeController::class, 'unlikePost']);
    Route::get('/posts/{postId}/likes', [LikeController::class, 'getPostLikes']);

    // Rutas de comentarios
    Route::post('/posts/{postId}/comments', [CommentController::class, 'store']);
    Route::get('/posts/{postId}/comments', [CommentController::class, 'getComments']);
});


Route::get('/storage/{filename}', function ($filename) {
    $path = storage_path('app/public/' . $filename);

    if (!file_exists($path)) {
        return response()->json(['error' => 'File not found'], 404);
    }

    return response()->file($path);
})->where('filename', '.*');
