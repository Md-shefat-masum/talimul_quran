@extends('backend.layout.app')

@section('title', 'Permission Registry')

@section('content')
<div class="user-management-shell">
    <div class="user-index-topbar">
        <div class="user-index-heading">
            <span class="user-index-heading__icon bg-gradient-primary text-white">
                <i class="mdi mdi-key-chain"></i>
            </span>
            <div>
                <p class="user-index-heading__kicker">Access Control</p>
                <h3 class="user-index-heading__title">Permission Registry</h3>
            </div>
        </div>

        @can('roles.view')
            <a href="{{ route('backend.roles.index') }}" class="btn btn-outline-primary btn-sm">
                <i class="mdi mdi-shield-account-outline me-1"></i>
                Roles
            </a>
        @endcan
    </div>

    <div class="card user-management-card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                    <tr>
                        <th>Permission</th>
                        <th>Action</th>
                        <th>Route Name</th>
                        <th>URL</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($permissions as $permission)
                        <tr>
                            <td><code>{{ $permission['key'] }}</code></td>
                            <td>{{ ucfirst($permission['action']) }}</td>
                            <td>{{ $permission['route_name'] }}</td>
                            <td class="text-muted">{{ $permission['route'] ?? 'Requires route parameter or unavailable' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
