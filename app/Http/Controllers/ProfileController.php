<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        if ($user->role === 'admin') {
            return view('admin.profile', compact('user'));
        }
        return view('user.profile', compact('user'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ]);

        $user->name = $request->input('name');
        $user->email = $request->input('email');

        if ($request->hasFile('photo')) {
            // Delete old photo if it exists
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            // Store new photo
            $path = $request->file('photo')->store('avatars', 'public');
            $user->photo = $path;
        }

        $user->save();

        $route = $user->role === 'admin' ? 'admin.profile.edit' : 'profile.edit';
        return redirect()->route($route)->with('success', 'Profile updated successfully.');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user->update([
            'password' => Hash::make($request->input('password')),
        ]);

        $route = $user->role === 'admin' ? 'admin.profile.edit' : 'profile.edit';
        return redirect()->route($route)->with('success', 'Password updated successfully.');
    }
}
