<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        try {
            $users = User::orderBy('created_at', 'desc')->get();
            return view('admin.users.index', compact('users'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load users: ' . $e->getMessage());
        }
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:100',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|string|min:6|confirmed',
            'role'          => 'required|in:admin,user',
            'status'        => 'required|in:active,inactive',
            'designation'   => 'nullable|string|max:100',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);


        try {
            DB::transaction(function () use ($request) {
                $imagePath = null;
                if ($request->hasFile('profile_image')) {
                    $imagePath = $request->file('profile_image')->store('profile-images', 'public');
                }

                User::create([
                    'name'          => $request->name,
                    'email'         => $request->email,
                    'password'      => $request->password,
                    'role'          => $request->role,
                    'status'        => $request->status,
                    'designation'   => $request->designation,
                    'profile_image' => $imagePath,
                ]);
            });

            return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to create user: ' . $e->getMessage());
        }
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'          => 'required|string|max:100',
            'email'         => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role'          => 'required|in:admin,user',
            'status'        => 'required|in:active,inactive',
            'designation'   => 'nullable|string|max:100',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        try {
            DB::transaction(function () use ($request, $user) {
                $data = [
                    'name'        => $request->name,
                    'email'       => $request->email,
                    'role'        => $request->role,
                    'status'      => $user->id === auth()->id() ? $user->status : $request->status,
                    'designation' => $request->designation,
                ];

                if ($request->filled('password')) {
                    $request->validate(['password' => 'min:6|confirmed']);
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

            return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to update user: ' . $e->getMessage());
        }
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        try {
            DB::transaction(function () use ($user) {
                $user->update(['status' => $user->status === 'active' ? 'inactive' : 'active']);
            });

            $msg = $user->fresh()->status === 'active' ? 'activated' : 'deactivated';
            return back()->with('success', "User {$msg} successfully.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        try {
            DB::transaction(function () use ($user) {
                if ($user->profile_image) {
                    Storage::disk('public')->delete($user->profile_image);
                }
                $user->delete();
            });

            return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }
}
