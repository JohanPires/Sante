<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function getUsers() {
        $users = User::all();

        return $users;
    }

    public function getOneUser($id) {
        $user = User::find($id);

        return $user;
    }

    public function deleteUser($id) {
        $user = User::find($id);
        $user->delete();

        return response()->json(['status' => 200, 'content' => 'Utilisateur supprimé avec succées']);
    }

    public function editUser($id, Request $request) {
        $user = User::find($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json(['status' => 200, 'content' => 'Utilisateur modifier avec succées', 'user' => $user]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ],
        [
            'email.unique' => 'Cette adresse e-mail est déjà utilisée',
            'password.min' => 'Minimum 8 caractères dans le mot de passe'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'User created successfully', 'user' => $user]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);


        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'authentification' => ['Vos identifiants sont incorrect'],
            ]);
        }
        $token = $user->createToken('auth-token')->plainTextToken;

        $id = $user->id;

        return response()->json(['user' => $user,'token' => $token, 'id' => $id]);
    }

    public function logout()
    {

        Auth::user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Déconnexion']);
    }
}


