<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function index()
    {
        $user = Auth::user();
        $totalNotes = \App\Models\Note::where('user_id', $user->id)->count();
        $totalCategories = \App\Models\Category::where('user_id', $user->id)->count();
        $daysJoined = now()->diffInDays($user->created_at);

        return view('profile.index', compact('user', 'totalNotes', 'totalCategories', 'daysJoined'));
    }

    /**
     * Update the user's profile.
     */
    public function update(Request $request)
    {
        // Ensure we have an Eloquent User model instance so the ->update() method exists.
        $user = \App\Models\User::findOrFail(Auth::id());

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'bio' => ['nullable', 'string', 'max:500'],
            'avatar' => ['nullable', 'image', 'max:2048'], // 2MB max
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        $user->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}
