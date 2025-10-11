@extends('layouts.admin')

@section('title', 'User Management')
@section('page-title', 'User Management')
@section('page-subtitle', 'Manage and monitor all system users')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="chart-container">
            <!-- Search and Filter Bar -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0"><i class="fas fa-users me-2"></i>All Users ({{ $users->total() }})</h5>
                
                <form action="{{ route('admin.users.index') }}" method="GET" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control" placeholder="Search users..." 
                           value="{{ request('search') }}" style="width: 250px;">
                    
                    <select name="role" class="form-select" style="width: 150px;">
                        <option value="">All Roles</option>
                        <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="moderator" {{ request('role') === 'moderator' ? 'selected' : '' }}>Moderator</option>
                    </select>
                    
                    <button type="submit" class="btn btn-admin-primary">
                        <i class="fas fa-search"></i> Search
                    </button>
                    
                    @if(request('search') || request('role'))
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Clear
                    </a>
                    @endif
                </form>
            </div>

            <!-- Users Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>
                                <a href="{{ route('admin.users.index', array_merge(request()->all(), ['sort' => 'id', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}" 
                                   class="text-decoration-none text-dark">
                                    ID <i class="fas fa-sort"></i>
                                </a>
                            </th>
                            <th>
                                <a href="{{ route('admin.users.index', array_merge(request()->all(), ['sort' => 'name', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}" 
                                   class="text-decoration-none text-dark">
                                    Name <i class="fas fa-sort"></i>
                                </a>
                            </th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th>
                                <a href="{{ route('admin.users.index', array_merge(request()->all(), ['sort' => 'created_at', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}" 
                                   class="text-decoration-none text-dark">
                                    Registered <i class="fas fa-sort"></i>
                                </a>
                            </th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td><strong>#{{ $user->id }}</strong></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar me-2" style="width: 35px; height: 35px; font-size: 14px; background: linear-gradient(135deg, #667eea, #764ba2);">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <strong>{{ $user->name }}</strong>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'moderator' ? 'warning' : 'primary') }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td>
                                @if($user->isLocked())
                                    <span class="badge bg-danger">
                                        <i class="fas fa-lock"></i> Locked
                                    </span>
                                @else
                                    <span class="badge bg-success">
                                        <i class="fas fa-check"></i> Active
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($user->last_login_at)
                                    <small class="text-muted">{{ $user->last_login_at->diffForHumans() }}</small>
                                @else
                                    <small class="text-muted">Never</small>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $user->created_at->format('M d, Y') }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if($user->isLocked())
                                        <form action="{{ route('admin.users.unlock', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Unlock User">
                                                <i class="fas fa-unlock"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.users.lock', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-warning" title="Lock User"
                                                    onclick="return confirm('Lock this user account for 24 hours?')">
                                                <i class="fas fa-lock"></i>
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete User"
                                                onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No users found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="row g-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-card-icon primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-card-value">{{ $users->total() }}</div>
            <div class="stat-card-label">Total Users</div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-card-icon danger">
                <i class="fas fa-user-shield"></i>
            </div>
            <div class="stat-card-value">{{ \App\Models\User::where('role', 'admin')->count() }}</div>
            <div class="stat-card-label">Administrators</div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-card-icon warning">
                <i class="fas fa-user-tag"></i>
            </div>
            <div class="stat-card-value">{{ \App\Models\User::where('role', 'moderator')->count() }}</div>
            <div class="stat-card-label">Moderators</div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-card-icon info">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="stat-card-value">{{ \App\Models\User::where('last_login_at', '>=', now()->subDays(7))->count() }}</div>
            <div class="stat-card-label">Active This Week</div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table th a {
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    
    .table th a i {
        font-size: 10px;
        opacity: 0.5;
    }
    
    .table th a:hover i {
        opacity: 1;
    }
    
    .btn-group .btn {
        border-radius: 6px;
        margin-left: 2px;
    }
    
    /* Custom Pagination Styles */
    .pagination {
        gap: 5px;
    }
    
    .pagination .page-link {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        color: #1e3a5f;
        padding: 8px 14px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .pagination .page-link:hover {
        background: #1e3a5f;
        color: #fff;
        border-color: #1e3a5f;
        transform: translateY(-2px);
    }
    
    .pagination .page-item.active .page-link {
        background: #4caf50;
        border-color: #4caf50;
        color: #fff;
    }
    
    .pagination .page-item.disabled .page-link {
        background: #f8f9fa;
        border-color: #dee2e6;
        color: #6c757d;
    }
</style>
@endpush
