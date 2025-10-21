<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Project;
use App\Models\GreenSpace;
use App\Models\Participation;
use App\Models\Event;
use App\Models\Association;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard with statistics and charts
     */
    public function dashboard()
    {
        // Get overall statistics
        $stats = [
            'total_users' => User::count(),
            'total_projects' => Project::count(),
            'total_green_spaces' => GreenSpace::count(),
            'total_participations' => Participation::count(),
            'total_events' => Event::count(),
            'total_associations' => Association::count(),
            'active_users' => User::where('last_login_at', '>=', now()->subDays(30))->count(),
        ];

        // User registration trend (last 6 months)
        $userTrend = User::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Participation status distribution
        $participationStats = Participation::select('statut', DB::raw('COUNT(*) as count'))
            ->groupBy('statut')
            ->get();

        // Project status distribution
        $projectStats = Project::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        // Green space type distribution
        $greenSpaceTypes = GreenSpace::select('type', DB::raw('COUNT(*) as count'))
            ->groupBy('type')
            ->get();

        // Recent users (last 10)
        $recentUsers = User::latest()->take(10)->get();

        // Recent participations (last 10)
        $recentParticipations = Participation::with(['user', 'greenSpace'])
            ->latest()
            ->take(10)
            ->get();

        // User role distribution
        $userRoles = User::select('role', DB::raw('COUNT(*) as count'))
            ->groupBy('role')
            ->get();

        // Event registrations trend
        $eventRegistrations = EventRegistration::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'userTrend',
            'participationStats',
            'projectStats',
            'greenSpaceTypes',
            'recentUsers',
            'recentParticipations',
            'userRoles',
            'eventRegistrations'
        ));
    }

    /**
     * Display a listing of all users
     */
    public function users(Request $request)
    {
        $query = User::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        // Sort
        $sortBy = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $users = $query->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Display the specified user
     */
    public function showUser(User $user)
    {
        // Load relationships
        $user->load([
            'participations.greenSpace',
            'participationFeedbacks',
            'eventRegistrations.event'
        ]);

        // Get user statistics
        $userStats = [
            'total_participations' => $user->participations()->count(),
            'confirmed_participations' => $user->participations()->where('statut', 'confirmee')->count(),
            'completed_participations' => $user->participations()->where('statut', 'terminee')->count(),
            'total_events' => $user->eventRegistrations()->count(),
            'confirmed_events' => $user->eventRegistrations()->where('statut', 'confirmee')->count(),
            'feedbacks_given' => $user->participationFeedbacks()->count(),
            'average_rating' => $user->participationFeedbacks()->avg('rating'),
        ];

        // Get participation trend
        $participationTrend = $user->participations()
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('admin.users.show', compact('user', 'userStats', 'participationTrend'));
    }

    /**
     * Update the specified user
     */
    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:user,admin,moderator'],
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User updated successfully!');
    }

    /**
     * Lock the specified user account
     */
    public function lockUser(User $user)
    {
        if ($user->isAdmin() && User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'Cannot lock the last admin account!');
        }

        $user->lockAccount(1440); // Lock for 24 hours

        return back()->with('success', 'User account has been locked for 24 hours.');
    }

    /**
     * Unlock the specified user account
     */
    public function unlockUser(User $user)
    {
        $user->unlockAccount();

        return back()->with('success', 'User account has been unlocked.');
    }

    /**
     * Delete the specified user
     */
    public function destroyUser(User $user)
    {
        if ($user->isAdmin() && User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'Cannot delete the last admin account!');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "User {$userName} has been deleted.");
    }
}
