<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserProfileController extends Controller
{
    /**
     * Mostrar el perfil del usuario autenticado.
     */
    public function index()
    {
        $user = Auth::user();
        return response()->json($user->profile);
    }

    /**
     * Crear un nuevo perfil de usuario.
     */
    public function store(Request $request)
    {
        $request->validate([
            'bio' => 'nullable|string',
            'profile_picture' => 'nullable|image|mimes:jpg,png,jpeg,gif|max:2048',
        ]);

        $user = Auth::user();

        // Guardar la imagen si se proporciona
        $path = null;
        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        // Crear el perfil
        $profile = UserProfile::create([
            'user_id' => $user->id,
            'bio' => $request->bio,
            'profile_picture' => $path,
        ]);

        return response()->json(['message' => 'Perfil creado exitosamente', 'profile' => $profile], 201);
    }

    /**
     * Mostrar el perfil de un usuario específico.
     */
    public function show(string $id)
    {
        $profile = UserProfile::where('user_id', $id)->firstOrFail();
        return response()->json($profile);
    }

    /**
     * Actualizar el perfil del usuario autenticado.
     */
    public function update(Request $request)
    {
        $request->validate([
            'bio' => 'nullable|string',
            'profile_picture' => 'nullable|image|mimes:jpg,png,jpeg,gif|max:2048',
        ]);

        $user = Auth::user();
        $profile = $user->profile;

        if (!$profile) {
            return response()->json(['message' => 'El perfil no existe'], 404);
        }

        // Actualizar la imagen si se proporciona
        if ($request->hasFile('profile_picture')) {
            // Eliminar la imagen anterior si existe
            if ($profile->profile_picture && Storage::disk('public')->exists($profile->profile_picture)) {
                Storage::disk('public')->delete($profile->profile_picture);
            }

            // Guardar la nueva imagen
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $profile->profile_picture = $path;
        }

        // Actualizar otros campos
        $profile->bio = $request->bio ?? $profile->bio;

        // Guardar los cambios
        $profile->save();

        return response()->json(['message' => 'Perfil actualizado exitosamente', 'profile' => $profile]);
    }

    /**
     * Eliminar el perfil del usuario autenticado.
     */
    public function destroy()
    {
        $user = Auth::user();
        $profile = $user->profile;

        if (!$profile) {
            return response()->json(['message' => 'El perfil no existe'], 404);
        }

        // Eliminar la imagen asociada si existe
        if ($profile->profile_picture && Storage::disk('public')->exists($profile->profile_picture)) {
            Storage::disk('public')->delete($profile->profile_picture);
        }

        // Eliminar el perfil
        $profile->delete();

        return response()->json(['message' => 'Perfil eliminado exitosamente']);
    }
}
