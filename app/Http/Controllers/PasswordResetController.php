<?php

namespace App\Http\Controllers;

use App\Mail\PasswordResetMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No account found with this email address.'])->withInput();
        }

        try {
            $token = Str::random(64);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                ['token' => $token, 'created_at' => now()]
            );

            $resetUrl = route('password.reset.form', ['token' => $token, 'email' => $user->email]);

            Mail::to($user->email)->send(new PasswordResetMail($resetUrl, $user->name));

            return back()->with('success', 'Password reset link has been sent to your email.');
        } catch (\Exception $e) {
            dd($e->getMessage());
            return back()->with('error', 'Failed to send reset email. Please try again.')->withInput();
        }
    }

    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$record) {
            return back()->withErrors(['email' => 'Invalid or expired reset link. Please request a new one.']);
        }

        if (now()->diffInMinutes($record->created_at) > 60) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'This reset link has expired. Please request a new one.']);
        }

        try {
            DB::transaction(function () use ($request) {
                User::where('email', $request->email)->update([
                    'password' => Hash::make($request->password),
                ]);

                DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            });

            return redirect()->route('login')->with('success', 'Password reset successfully! Please sign in with your new password.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to reset password. Please try again.');
        }
    }
}
