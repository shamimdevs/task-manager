<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        try {
            $user = Auth::user();
            return view('profile.show', compact('user'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load profile: ' . $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'          => 'required|string|max:100',
            'email'         => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'designation'   => 'nullable|string|max:100',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        try {
            DB::transaction(function () use ($request, $user) {
                $data = [
                    'name'        => $request->name,
                    'email'       => $request->email,
                    'designation' => $request->designation,
                ];

                if ($request->filled('password')) {
                    $request->validate([
                        'current_password' => 'required',
                        'password'         => 'min:6|confirmed',
                    ]);

                    if (!password_verify($request->current_password, $user->password)) {
                        throw new \Exception('Current password is incorrect.');
                    }

                    $data['password'] = $request->password;
                }

                if ($request->hasFile('profile_image')) {
                    if ($user->profile_image) {
                        Storage::disk('public')->delete($user->profile_image);
                    }
                    $data['profile_image'] = $request->file('profile_image')->store('profile-images', 'public');
                }

                $user->update($data);
            });

            return back()->with('success', 'Profile updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
