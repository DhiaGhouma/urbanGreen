<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle user login with security measures
     */
    public function login(Request $request)
    {
        // Validate input
        $credentials = $request->validate([
            'email' => ['required', 'email:rfc,dns', 'max:255'],
            'password' => ['required', 'string', 'min:1'],
        ]);

        // Find user by email
        $user = User::where('email', $credentials['email'])->first();

        // Check if user exists
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Check if account is locked
        if ($user->isLocked()) {
            $lockTime = $user->locked_until->diffForHumans();
            throw ValidationException::withMessages([
                'email' => ["Your account is locked until {$lockTime}. Please try again later."],
            ]);
        }

        // Verify password
        if (!Hash::check($credentials['password'], $user->password)) {
            // Increment failed attempts
            $user->incrementFailedAttempts();
            
            $remainingAttempts = 5 - $user->failed_login_attempts;
            $message = $remainingAttempts > 0 
                ? "Invalid credentials. {$remainingAttempts} attempts remaining before account lockout."
                : 'Account has been locked due to too many failed attempts.';
            
            throw ValidationException::withMessages([
                'email' => [$message],
            ]);
        }

        // Reset failed attempts and update last login
        $user->resetFailedAttempts();
        $user->updateLastLogin();

        // Log the user in
        Auth::login($user, $request->boolean('remember'));

        // Regenerate session for security
        $request->session()->regenerate();

        return redirect()->intended('/')->with('success', 'Welcome back, ' . $user->name . '!');
    }

    /**
     * Show the registration form
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle user registration with validation
     */
    public function register(Request $request)
    {
        // Comprehensive validation
        $validated = $request->validate([
            'name' => [
                'required', 
                'string', 
                'max:255', 
                'min:2',
                'regex:/^[a-zA-ZÀ-ÿ\s\-\'\.]+$/u'
            ],
            'email' => [
                'required', 
                'email:rfc,dns', 
                'max:255', 
                'unique:users,email'
            ],
            'password' => [
                'required', 
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
            ],
            'terms' => ['accepted'],
        ], [
            'name.regex' => 'The name may only contain letters, spaces, hyphens, apostrophes, and dots.',
            'password.uncompromised' => 'The given password has appeared in a data leak. Please choose a different password.',
            'terms.accepted' => 'You must accept the terms and conditions to register.',
        ]);

        try {
            // Create the user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'user',
                'email_verified_at' => now(), // Auto-verify for now, can be changed later
                'last_login_at' => now(),
            ]);

            // Log the user in
            Auth::login($user);

            // Regenerate session
            $request->session()->regenerate();

            return redirect('/')->with('success', 'Welcome to UrbanGreen, ' . $user->name . '! Your account has been created successfully.');

        } catch (\Exception $e) {
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['registration' => 'Registration failed. Please try again.']);
        }
    }

    /**
     * Handle user logout
     */
    public function logout(Request $request)
    {
        // Get user name before logout
        $userName = Auth::user()->name ?? 'User';

        // Logout the user
        Auth::logout();

        // Invalidate the session
        $request->session()->invalidate();

        // Regenerate CSRF token
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Goodbye ' . $userName . '! You have been logged out successfully.');
    }

    /**
     * Show user profile/dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        return view('auth.dashboard', [
            'user' => $user,
            'recentActivity' => [
                'last_login' => $user->last_login_at,
                'member_since' => $user->created_at,
                'total_participations' => $user->participations()->count(),
            ]
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => [
                'required', 
                'string', 
                'max:255', 
                'min:2',
                'regex:/^[a-zA-ZÀ-ÿ\s\-\'\.]+$/u'
            ],
            'email' => [
                'required', 
                'email:rfc,dns', 
                'max:255', 
                'unique:users,email,' . $user->id
            ],
        ]);

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Change user password
     */
    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => [
                'required', 
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
            ],
        ]);

        // Verify current password
        if (!Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }

        // Update password
        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        return back()->with('success', 'Password changed successfully!');
    }
}
