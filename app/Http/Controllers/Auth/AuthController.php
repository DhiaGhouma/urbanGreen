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
     * Show the profile page (preferences + basic info)
     */
    public function showProfile()
    {
        $user = Auth::user();

        // Allowed options for preferences
        $options = [
            'activities_interest' => ['reboisement','nettoyage','jardinage','compostage','photographie','botanique','randonnée'],
            'prefered_days' => ['lundi','mardi','mercredi','jeudi','vendredi','samedi','dimanche'],
            'availability' => ['matin','après-midi','soir'],
            'volunteer_roles' => ['bénévole','coordinateur','formateur'],
        ];

        return view('auth.profile', [
            'user' => $user,
            'options' => $options,
        ]);
    }
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
            'email' => ['required', 'email:rfc', 'max:255'],
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

        // Redirect based on user role
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard')->with('success', 'Welcome back, ' . $user->name . '!');
        }

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
                'email:rfc,', 
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
            // Basic info (optional here so the preferences form can submit alone)
            'name' => [
                'sometimes',
                'required', 
                'string', 
                'max:255', 
                'min:2',
                'regex:/^[a-zA-ZÀ-ÿ\s\-\'\.]+$/u'
            ],
            'email' => [
                'sometimes',
                'required', 
                'email:rfc', 
                'max:255', 
                'unique:users,email,' . $user->id
            ],
            // Preferences (all optional so dashboard form remains valid)
            'activities_interest' => ['sometimes','array'],
            'activities_interest.*' => ['in:reboisement,nettoyage,jardinage,compostage,photographie,botanique,randonnée'],
            'prefered_days' => ['sometimes','array'],
            'prefered_days.*' => ['in:lundi,mardi,mercredi,jeudi,vendredi,samedi,dimanche'],
            'availability' => ['sometimes','array'],
            'availability.*' => ['in:matin,après-midi,soir'],
            'volunteer_roles' => ['sometimes','array'],
            'volunteer_roles.*' => ['in:bénévole,coordinateur,formateur'],
            'radius_km' => ['nullable','integer','min:1','max:200'],
        ]);

        // Update basic fields only if provided in the request
        $basicUpdates = [];
        if ($request->has('name')) {
            $basicUpdates['name'] = $validated['name'];
        }
        if ($request->has('email')) {
            $basicUpdates['email'] = $validated['email'];
        }
        if (!empty($basicUpdates)) {
            $user->fill($basicUpdates);
        }

        // Build preferences safely (keep keys only if provided)
        $prefs = $user->preferences ?: [];
        
        // Anciennes clés (pour compatibilité avec le formulaire)
        $prefs['activities_interest'] = $request->has('activities_interest') ? array_values($request->input('activities_interest', [])) : ($prefs['activities_interest'] ?? []);
        $prefs['prefered_days'] = $request->has('prefered_days') ? array_values($request->input('prefered_days', [])) : ($prefs['prefered_days'] ?? []);
        $prefs['availability'] = $request->has('availability') ? array_values($request->input('availability', [])) : ($prefs['availability'] ?? []);
        $prefs['volunteer_roles'] = $request->has('volunteer_roles') ? array_values($request->input('volunteer_roles', [])) : ($prefs['volunteer_roles'] ?? []);
        if ($request->filled('radius_km')) {
            $prefs['radius_km'] = (int) $request->input('radius_km');
        }
        
        // ═══════════════════════════════════════════════════════════════
        // Mapping vers nouvelles clés pour le système AI
        // ═══════════════════════════════════════════════════════════════
        
        // preferred_activities : map depuis activities_interest
        $prefs['preferred_activities'] = $prefs['activities_interest'] ?? [];
        
        // interests : extraire depuis activities_interest (simplification)
        $prefs['interests'] = $prefs['activities_interest'] ?? [];
        
        // experience_level : déduire depuis volunteer_roles ou utiliser par défaut
        if (in_array('formateur', $prefs['volunteer_roles'] ?? [])) {
            $prefs['experience_level'] = 'expert';
        } elseif (in_array('coordinateur', $prefs['volunteer_roles'] ?? [])) {
            $prefs['experience_level'] = 'intermédiaire';
        } else {
            $prefs['experience_level'] = $prefs['experience_level'] ?? 'débutant';
        }
        
        // preferred_types : déduire depuis activities_interest
        $types = [];
        if (in_array('jardinage', $prefs['activities_interest'] ?? [])) {
            $types[] = 'jardin communautaire';
        }
        if (in_array('reboisement', $prefs['activities_interest'] ?? []) || in_array('randonnée', $prefs['activities_interest'] ?? [])) {
            $types[] = 'forêt';
        }
        if (empty($types)) {
            $types = ['parc', 'jardin communautaire'];
        }
        $prefs['preferred_types'] = array_unique($types);
        
        // max_distance : map depuis radius_km
        $prefs['max_distance'] = $prefs['radius_km'] ?? 10;
        
        // coordinates : garder si existent déjà, sinon laisser null
        // (peut être ajouté plus tard via géolocalisation)
        if (!isset($prefs['coordinates'])) {
            $prefs['coordinates'] = null;
        }
        
        $user->preferences = $prefs;

        $user->save();

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
