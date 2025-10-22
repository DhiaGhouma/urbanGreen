<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Association;
use App\Models\Project;
use App\Models\GreenSpace;
use App\Models\Event;
use App\Models\Participation;
use App\Models\Report;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard with comprehensive statistics
     */
    public function index()
    {
        // =====================================================
        // STATISTICS - Count totals
        // =====================================================
        
        // Users statistics
        $totalUsers = User::count();
        $usersByRole = User::select('role', DB::raw('count(*) as total'))
            ->groupBy('role')
            ->pluck('total', 'role');
        $activeUsers = User::where('last_login_at', '>=', Carbon::now()->subDays(30))->count();
        
        // Associations
        $totalAssociations = Association::count();
        $recentAssociations = Association::where('created_at', '>=', Carbon::now()->subDays(30))->count();
        
        // Projects by status
        $totalProjects = Project::count();
        $projectsByStatus = Project::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');
        
        // Green Spaces
        $totalGreenSpaces = GreenSpace::count();
        $greenSpacesByType = GreenSpace::select('type', DB::raw('count(*) as total'))
            ->groupBy('type')
            ->pluck('total', 'type');
        $greenSpacesByStatus = GreenSpace::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');
        
        // Events
        $totalEvents = Event::count();
        $eventsByStatus = Event::select('statut', DB::raw('count(*) as total'))
            ->groupBy('statut')
            ->pluck('total', 'statut');
        
        // Participations
        $totalParticipations = Participation::count();
        $participationsByStatus = Participation::select('statut', DB::raw('count(*) as total'))
            ->groupBy('statut')
            ->pluck('total', 'statut');
        
        // Reports
        $totalReports = Report::count();
        $reportsByStatus = Report::select('statut', DB::raw('count(*) as total'))
            ->groupBy('statut')
            ->pluck('total', 'statut');
        $urgentReports = Report::where('priority', 'high')->count();
        
        // =====================================================
        // CHARTS DATA - Trends and distributions
        // =====================================================
        
        // Chart 1: Projects by Status (for Doughnut chart)
        $projectsChartData = [
            'labels' => $projectsByStatus->keys()->toArray(),
            'data' => $projectsByStatus->values()->toArray(),
        ];
        
        // Chart 2: Participations Over Time - Last 6 months (for Line chart)
        $participationsTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $count = Participation::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $participationsTrend['labels'][] = $month->format('M Y');
            $participationsTrend['data'][] = $count;
        }
        
        // Chart 3: Green Spaces by Type (for Bar chart)
        $greenSpacesChartData = [
            'labels' => $greenSpacesByType->keys()->toArray(),
            'data' => $greenSpacesByType->values()->toArray(),
        ];
        
        // Chart 4: Events by Status (for Pie chart)
        $eventsChartData = [
            'labels' => $eventsByStatus->keys()->toArray(),
            'data' => $eventsByStatus->values()->toArray(),
        ];
        
        // Chart 5: User Registrations Trend - Last 12 months (for Area chart)
        $userRegistrationsTrend = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $count = User::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $userRegistrationsTrend['labels'][] = $month->format('M Y');
            $userRegistrationsTrend['data'][] = $count;
        }
        
        // =====================================================
        // RECENT ACTIVITIES - Latest records
        // =====================================================
        
        // Latest users registered
        $recentUsers = User::orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        // Latest participations
        $recentParticipations = Participation::with(['user', 'greenSpace'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        // Urgent/Recent reports
        $recentReports = Report::with(['user', 'greenSpace'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        // Pending participations (en_attente status)
        $pendingParticipations = Participation::with(['user', 'greenSpace'])
            ->where('statut', 'en_attente')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        // =====================================================
        // RETURN VIEW WITH ALL DATA
        // =====================================================
        
        return view('admin.dashboard', compact(
            // Statistics
            'totalUsers',
            'usersByRole',
            'activeUsers',
            'totalAssociations',
            'recentAssociations',
            'totalProjects',
            'projectsByStatus',
            'totalGreenSpaces',
            'greenSpacesByType',
            'greenSpacesByStatus',
            'totalEvents',
            'eventsByStatus',
            'totalParticipations',
            'participationsByStatus',
            'totalReports',
            'reportsByStatus',
            'urgentReports',
            
            // Charts data
            'projectsChartData',
            'participationsTrend',
            'greenSpacesChartData',
            'eventsChartData',
            'userRegistrationsTrend',
            
            // Recent activities
            'recentUsers',
            'recentParticipations',
            'recentReports',
            'pendingParticipations'
        ));
    }
}
