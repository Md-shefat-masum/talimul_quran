@extends('backend.layout.app')

@section('title', 'Role Management')

@section('content')
<div class="user-management-shell">
    <div class="user-index-topbar">
        <div class="user-index-heading">
            <span class="user-index-heading__icon bg-gradient-primary text-white">
                <i class="mdi mdi-shield-account-outline"></i>
            </span>
            <div>
                <p class="user-index-heading__kicker">Access Control</p>
                <h3 class="user-index-heading__title">Role Management</h3>
            </div>
        </div>

        @can('roles.create')
            <a href="{{ route('backend.roles.create') }}" class="btn btn-gradient-primary btn-sm">
                <i class="mdi mdi-plus me-1"></i>
                Create Role
            </a>
        @endcan
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card user-management-card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                    <tr>
                        <th>Role</th>
                        <th>Permissions</th>
                        <th>Users</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($roles as $role)
                        <tr>
                            <td>
                                <div class="fw-semibold text-dark">{{ $role->name }}</div>
                                <div class="small text-muted">{{ $role->slug }}</div>
                            </td>
                            <td>{{ count($role->permissions ?: []) }}</td>
                            <td>{{ $role->users_count }}</td>
                            <td>
                                <span class="user-status-badge {{ $role->status ? 'user-status-badge--active' : 'user-status-badge--inactive' }}">
                                    {{ $role->status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-2">
                                    @can('roles.update')
                                        <a href="{{ route('backend.roles.edit', $role) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="mdi mdi-pencil-outline me-1"></i>
                                            Edit
                                        </a>
                                    @endcan

                                    @can('roles.delete')
                                        <form method="POST" action="{{ route('backend.roles.destroy', $role) }}" onsubmit="return confirm('Delete this role?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" @disabled($role->is_system || $role->users_count > 0)>
                                                <i class="mdi mdi-delete-outline me-1"></i>
                                                Delete
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No roles found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
