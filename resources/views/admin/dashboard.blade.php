@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Overview of system statistics and activities')

@section('content')
<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-card-icon primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-card-value">{{ number_format($stats['total_users']) }}</div>
            <div class="stat-card-label">Total Users</div>
            @if($stats['active_users'] > 0)
            <div class="stat-card-trend up">
                <i class="fas fa-arrow-up"></i> {{ $stats['active_users'] }} active this month
            </div>
            @endif
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-card-icon success">
                <i class="fas fa-seedling"></i>
            </div>
            <div class="stat-card-value">{{ number_format($stats['total_green_spaces']) }}</div>
            <div class="stat-card-label">Green Spaces</div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-card-icon warning">
                <i class="fas fa-project-diagram"></i>
            </div>
            <div class="stat-card-value">{{ number_format($stats['total_projects']) }}</div>
            <div class="stat-card-label">Projects</div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-card-icon info">
                <i class="fas fa-hands-helping"></i>
            </div>
            <div class="stat-card-value">{{ number_format($stats['total_participations']) }}</div>
            <div class="stat-card-label">Participations</div>
        </div>
    </div>
</div>

<!-- Additional Stats Row -->
<div class="row g-4 mb-4">
    <div class="col-xl-4 col-md-6">
        <div class="stat-card">
            <div class="stat-card-icon primary">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="stat-card-value">{{ number_format($stats['total_events']) }}</div>
            <div class="stat-card-label">Events</div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6">
        <div class="stat-card">
            <div class="stat-card-icon success">
                <i class="fas fa-building"></i>
            </div>
            <div class="stat-card-value">{{ number_format($stats['total_associations']) }}</div>
            <div class="stat-card-label">Associations</div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6">
        <div class="stat-card">
            <div class="stat-card-icon info">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="stat-card-value">{{ number_format($stats['active_users']) }}</div>
            <div class="stat-card-label">Active Users (30 days)</div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <!-- User Registration Trend -->
    <div class="col-xl-8">
        <div class="chart-container">
            <div class="chart-header">
                <h5><i class="fas fa-chart-line me-2"></i>User Registration Trend (Last 6 Months)</h5>
            </div>
            <canvas id="userTrendChart" height="80"></canvas>
        </div>
    </div>

    <!-- User Role Distribution -->
    <div class="col-xl-4">
        <div class="chart-container">
            <div class="chart-header">
                <h5><i class="fas fa-chart-pie me-2"></i>User Roles</h5>
            </div>
            <canvas id="userRolesChart"></canvas>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Participation Status -->
    <div class="col-xl-6">
        <div class="chart-container">
            <div class="chart-header">
                <h5><i class="fas fa-chart-bar me-2"></i>Participation Status</h5>
            </div>
            <canvas id="participationStatusChart" height="100"></canvas>
        </div>
    </div>

    <!-- Project Status -->
    <div class="col-xl-6">
        <div class="chart-container">
            <div class="chart-header">
                <h5><i class="fas fa-chart-bar me-2"></i>Project Status</h5>
            </div>
            <canvas id="projectStatusChart" height="100"></canvas>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Green Space Types -->
    <div class="col-xl-6">
        <div class="chart-container">
            <div class="chart-header">
                <h5><i class="fas fa-chart-doughnut me-2"></i>Green Space Types</h5>
            </div>
            <canvas id="greenSpaceTypesChart"></canvas>
        </div>
    </div>

    <!-- Event Registrations Trend -->
    <div class="col-xl-6">
        <div class="chart-container">
            <div class="chart-header">
                <h5><i class="fas fa-chart-line me-2"></i>Event Registrations (Last 6 Months)</h5>
            </div>
            <canvas id="eventRegistrationsChart" height="100"></canvas>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row g-4">
    <!-- Recent Users -->
    <div class="col-xl-6">
        <div class="chart-container">
            <div class="chart-header">
                <h5><i class="fas fa-user-plus me-2"></i>Recent Users</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Registered</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentUsers as $user)
                        <tr>
                            <td>
                                <a href="{{ route('admin.users.show', $user) }}" class="text-decoration-none">
                                    <strong>{{ $user->name }}</strong>
                                </a>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'moderator' ? 'warning' : 'primary') }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td>{{ $user->created_at->diffForHumans() }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No users found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Participations -->
    <div class="col-xl-6">
        <div class="chart-container">
            <div class="chart-header">
                <h5><i class="fas fa-hands-helping me-2"></i>Recent Participations</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Green Space</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentParticipations as $participation)
                        <tr>
                            <td>
                                <a href="{{ route('admin.users.show', $participation->user) }}" class="text-decoration-none">
                                    {{ $participation->user->name }}
                                </a>
                            </td>
                            <td>{{ Str::limit($participation->greenSpace->name, 30) }}</td>
                            <td>
                                <span class="badge {{ $participation->getStatusBadgeClass() }}">
                                    {{ $participation->getStatutLabel() }}
                                </span>
                            </td>
                            <td>{{ $participation->date->format('M d, Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No participations found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Chart Colors
    const chartColors = {
        primary: '#667eea',
        success: '#38ef7d',
        warning: '#f5576c',
        info: '#00f2fe',
        danger: '#dc3545',
        secondary: '#6c757d'
    };

    // User Registration Trend Chart
    const userTrendCtx = document.getElementById('userTrendChart').getContext('2d');
    const userTrendChart = new Chart(userTrendCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($userTrend->pluck('month')) !!},
            datasets: [{
                label: 'New Users',
                data: {!! json_encode($userTrend->pluck('count')) !!},
                borderColor: chartColors.primary,
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: chartColors.primary,
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });

    // User Roles Chart
    const userRolesCtx = document.getElementById('userRolesChart').getContext('2d');
    const userRolesChart = new Chart(userRolesCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($userRoles->pluck('role')->map(fn($r) => ucfirst($r))) !!},
            datasets: [{
                data: {!! json_encode($userRoles->pluck('count')) !!},
                backgroundColor: [chartColors.primary, chartColors.danger, chartColors.warning],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Participation Status Chart
    const participationStatusCtx = document.getElementById('participationStatusChart').getContext('2d');
    const participationStatusChart = new Chart(participationStatusCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($participationStats->pluck('statut')) !!},
            datasets: [{
                label: 'Participations',
                data: {!! json_encode($participationStats->pluck('count')) !!},
                backgroundColor: [
                    chartColors.warning,
                    chartColors.success,
                    chartColors.danger,
                    chartColors.info
                ],
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });

    // Project Status Chart
    const projectStatusCtx = document.getElementById('projectStatusChart').getContext('2d');
    const projectStatusChart = new Chart(projectStatusCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($projectStats->pluck('status')) !!},
            datasets: [{
                label: 'Projects',
                data: {!! json_encode($projectStats->pluck('count')) !!},
                backgroundColor: [
                    chartColors.primary,
                    chartColors.warning,
                    chartColors.success
                ],
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });

    // Green Space Types Chart
    const greenSpaceTypesCtx = document.getElementById('greenSpaceTypesChart').getContext('2d');
    const greenSpaceTypesChart = new Chart(greenSpaceTypesCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($greenSpaceTypes->pluck('type')) !!},
            datasets: [{
                data: {!! json_encode($greenSpaceTypes->pluck('count')) !!},
                backgroundColor: [
                    chartColors.success,
                    chartColors.primary,
                    chartColors.warning,
                    chartColors.info
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Event Registrations Chart
    const eventRegistrationsCtx = document.getElementById('eventRegistrationsChart').getContext('2d');
    const eventRegistrationsChart = new Chart(eventRegistrationsCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($eventRegistrations->pluck('month')) !!},
            datasets: [{
                label: 'Event Registrations',
                data: {!! json_encode($eventRegistrations->pluck('count')) !!},
                borderColor: chartColors.success,
                backgroundColor: 'rgba(56, 239, 125, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: chartColors.success,
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
</script>
@endpush
