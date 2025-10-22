@extends('layouts.admin')

@section('title', 'User Details - ' . $user->name)
@section('page-title', 'User Details')
@section('page-subtitle', 'Detailed information and activity for ' . $user->name)

@section('content')
<!-- User Header Card -->
<div class="row mb-4">
    <div class="col-12">
        <div class="chart-container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center gap-3">
                        <div class="user-avatar" style="width: 80px; height: 80px; font-size: 32px; background: linear-gradient(135deg, #667eea, #764ba2);">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div>
                            <h3 class="mb-1">{{ $user->name }}</h3>
                            <p class="text-muted mb-2">{{ $user->email }}</p>
                            <div class="d-flex gap-2">
                                <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'moderator' ? 'warning' : 'primary') }}">
                                    <i class="fas fa-{{ $user->role === 'admin' ? 'user-shield' : ($user->role === 'moderator' ? 'user-tag' : 'user') }}"></i>
                                    {{ ucfirst($user->role) }}
                                </span>
                                @if($user->isLocked())
                                    <span class="badge bg-danger">
                                        <i class="fas fa-lock"></i> Account Locked until {{ $user->locked_until->format('M d, Y H:i') }}
                                    </span>
                                @else
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle"></i> Active
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary mb-2">
                        <i class="fas fa-arrow-left"></i> Back to Users
                    </a>
                    <br>
                    @if($user->isLocked())
                        <form action="{{ route('admin.users.unlock', $user) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-unlock"></i> Unlock Account
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.users.lock', $user) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-warning" onclick="return confirm('Lock this user account for 24 hours?')">
                                <i class="fas fa-lock"></i> Lock Account
                            </button>
                        </form>
                    @endif
                    
                    <button type="button" class="btn btn-admin-primary" data-bs-toggle="modal" data-bs-target="#editUserModal">
                        <i class="fas fa-edit"></i> Edit User
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- User Statistics -->
<div class="row g-4 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="stat-card">
            <div class="stat-card-icon primary">
                <i class="fas fa-hands-helping"></i>
            </div>
            <div class="stat-card-value">{{ $userStats['total_participations'] }}</div>
            <div class="stat-card-label">Total Participations</div>
            @if($userStats['confirmed_participations'] > 0)
            <div class="stat-card-trend up">
                <i class="fas fa-check"></i> {{ $userStats['confirmed_participations'] }} confirmed
            </div>
            @endif
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stat-card">
            <div class="stat-card-icon success">
                <i class="fas fa-check-double"></i>
            </div>
            <div class="stat-card-value">{{ $userStats['completed_participations'] }}</div>
            <div class="stat-card-label">Completed Participations</div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stat-card">
            <div class="stat-card-icon warning">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-card-value">{{ $userStats['total_events'] }}</div>
            <div class="stat-card-label">Event Registrations</div>
            @if($userStats['confirmed_events'] > 0)
            <div class="stat-card-trend up">
                <i class="fas fa-check"></i> {{ $userStats['confirmed_events'] }} confirmed
            </div>
            @endif
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stat-card">
            <div class="stat-card-icon info">
                <i class="fas fa-star"></i>
            </div>
            <div class="stat-card-value">{{ $userStats['feedbacks_given'] }}</div>
            <div class="stat-card-label">Feedbacks Given</div>
            @if($userStats['average_rating'])
            <div class="stat-card-trend">
                <i class="fas fa-chart-line"></i> Avg: {{ number_format($userStats['average_rating'], 1) }}/5
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Account Information and Activity -->
<div class="row g-4 mb-4">
    <!-- Account Information -->
    <div class="col-lg-6">
        <div class="chart-container">
            <div class="chart-header">
                <h5><i class="fas fa-info-circle me-2"></i>Account Information</h5>
            </div>
            <table class="table table-borderless">
                <tbody>
                    <tr>
                        <td class="text-muted" style="width: 40%;"><i class="fas fa-envelope me-2"></i>Email</td>
                        <td><strong>{{ $user->email }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted"><i class="fas fa-user-tag me-2"></i>Role</td>
                        <td>
                            <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'moderator' ? 'warning' : 'primary') }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted"><i class="fas fa-calendar-plus me-2"></i>Member Since</td>
                        <td><strong>{{ $user->created_at->format('F d, Y') }}</strong> ({{ $user->created_at->diffForHumans() }})</td>
                    </tr>
                    <tr>
                        <td class="text-muted"><i class="fas fa-sign-in-alt me-2"></i>Last Login</td>
                        <td>
                            @if($user->last_login_at)
                                <strong>{{ $user->last_login_at->format('F d, Y H:i') }}</strong> ({{ $user->last_login_at->diffForHumans() }})
                            @else
                                <span class="text-muted">Never</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted"><i class="fas fa-shield-alt me-2"></i>Account Status</td>
                        <td>
                            @if($user->isLocked())
                                <span class="badge bg-danger">Locked until {{ $user->locked_until->format('M d, Y H:i') }}</span>
                            @else
                                <span class="badge bg-success">Active</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted"><i class="fas fa-exclamation-triangle me-2"></i>Failed Login Attempts</td>
                        <td><strong>{{ $user->failed_login_attempts }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Participation Trend Chart -->
    <div class="col-lg-6">
        <div class="chart-container">
            <div class="chart-header">
                <h5><i class="fas fa-chart-line me-2"></i>Participation Activity (Last 6 Months)</h5>
            </div>
            <canvas id="participationTrendChart" height="200"></canvas>
        </div>
    </div>
</div>

<!-- Recent Participations -->
<div class="row mb-4">
    <div class="col-12">
        <div class="chart-container">
            <div class="chart-header">
                <h5><i class="fas fa-history me-2"></i>Recent Participations</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Green Space</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($user->participations->take(10) as $participation)
                        <tr>
                            <td>
                                <strong>{{ $participation->greenSpace->name }}</strong>
                                <br>
                                <small class="text-muted">{{ $participation->greenSpace->location }}</small>
                            </td>
                            <td>{{ $participation->date->format('M d, Y') }}</td>
                            <td>
                                <span class="badge {{ $participation->getStatusBadgeClass() }}">
                                    {{ $participation->getStatutLabel() }}
                                </span>
                            </td>
                            <td><small class="text-muted">{{ $participation->created_at->diffForHumans() }}</small></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No participations found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Recent Event Registrations -->
<div class="row">
    <div class="col-12">
        <div class="chart-container">
            <div class="chart-header">
                <h5><i class="fas fa-calendar-alt me-2"></i>Recent Event Registrations</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Event</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Registered</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($user->eventRegistrations->take(10) as $registration)
                        <tr>
                            <td>
                                <strong>{{ $registration->event->titre }}</strong>
                                <br>
                                <small class="text-muted">{{ $registration->event->lieu }}</small>
                            </td>
                            <td>
                                <span class="badge bg-{{ $registration->event->type_badge_color }}">
                                    {{ ucfirst($registration->event->type) }}
                                </span>
                            </td>
                            <td>{{ $registration->event->date_debut->format('M d, Y') }}</td>
                            <td>
                                <span class="badge bg-{{ $registration->statut === 'confirmee' ? 'success' : ($registration->statut === 'en_attente' ? 'warning' : 'danger') }}">
                                    {{ ucfirst(str_replace('_', ' ', $registration->statut)) }}
                                </span>
                            </td>
                            <td><small class="text-muted">{{ $registration->created_at->diffForHumans() }}</small></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No event registrations found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                            <option value="moderator" {{ $user->role === 'moderator' ? 'selected' : '' }}>Moderator</option>
                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-admin-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Participation Trend Chart
    const participationTrendCtx = document.getElementById('participationTrendChart').getContext('2d');
    const participationTrendChart = new Chart(participationTrendCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($participationTrend->pluck('month')) !!},
            datasets: [{
                label: 'Participations',
                data: {!! json_encode($participationTrend->pluck('count')) !!},
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#667eea',
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
