<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Mail\ResetPasswordMail;

class AuthController extends Controller
{
    // Show registration form
    public function showRegisterForm()
    {
        return view('front.auth.register');
    }

    // Handle registration
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'contact' => 'nullable|string|max:20',
            'password' => ['required', 'confirmed', PasswordRule::min(6)],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'contact' => $request->contact,
            'password' => Hash::make($request->password),
            'role' => 'user', // default role
        ]);

        Auth::login($user);

        return redirect()->route('user.dashboard')->with('success', 'Registration successful! Welcome to NEXA!');
    }

    // Show login form
    public function showLoginForm()
    {
        return view('front.auth.login');
    }

    // Handle login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            $request->session()->regenerate();

            // Role-based redirect
            $role = Auth::user()->role;
            if ($role === 'admin') {
                return redirect()->route('admin.dashboard')->with('success', 'Welcome Admin!');
            } else {
                return redirect()->route('user.dashboard')->with('success', 'Welcome!');
            }
        }

        return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logged out successfully!');
    }

    // Show forgot password form
    public function showForgotForm()
    {
        return view('front.auth.forgot-password');
    }

    // Send password reset link using custom Mailable
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No user found with this email.']);
        }

        // Generate a token
        $token = Str::random(64);

        // Save token to password_resets table
        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($token),
                'created_at' => Carbon::now(),
            ]
        );

        // Generate reset URL
        $url = url("/reset-password/{$token}?email={$request->email}");

        // Send reset password email
        Mail::to($request->email)->send(new ResetPasswordMail($url));

        return back()->with('success', 'Password reset link sent to your email!');
    }

    // Show reset password form
    public function showResetForm(Request $request, $token)
    {
        return view('front.auth.reset-password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    // Handle password reset
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', PasswordRule::min(6)],
        ]);

        $record = DB::table('password_resets')->where('email', $request->email)->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => 'Invalid or expired reset token.']);
        }

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete token after successful reset
        DB::table('password_resets')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Password reset successfully!');
    }
}
