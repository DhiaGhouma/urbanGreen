<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUsersController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $users = $query->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Display the specified user details
     */
    public function show(User $user)
    {
        $user->load(['participations.greenSpace', 'eventRegistrations.event', 'participationFeedbacks']);
        
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'in:user,moderator,admin'],
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'Utilisateur mis à jour avec succès!');
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'Mot de passe mis à jour avec succès!');
    }

    /**
     * Lock/Unlock user account
     */
    public function toggleLock(User $user)
    {
        if ($user->isLocked()) {
            $user->unlockAccount();
            $message = 'Compte déverrouillé avec succès!';
        } else {
            $user->lockAccount(1440); // Lock for 24 hours
            $message = 'Compte verrouillé pour 24 heures!';
        }

        return redirect()->route('admin.users.show', $user)
            ->with('success', $message);
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Vous ne pouvez pas supprimer votre propre compte!');
        }

        // Prevent deleting the last admin
        if ($user->isAdmin() && User::where('role', 'admin')->count() === 1) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Vous ne pouvez pas supprimer le dernier administrateur!');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur supprimé avec succès!');
    }
}
