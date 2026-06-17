@extends('backend.layout.app')

@section('title', 'Role Management')

@section('content')
<div class="user-management-shell">
    <x-backend.page-header
        kicker="Access Control"
        title="Role Management"
        icon="mdi mdi-shield-account-outline"
    >
        <x-slot:actions>
            <x-backend.action-button
                ability="roles.create"
                :href="route('backend.roles.create')"
                icon="mdi mdi-plus"
            >
                Create Role
            </x-backend.action-button>
        </x-slot:actions>
    </x-backend.page-header>

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
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="fw-semibold text-dark">{{ $role->name }}</span>
                                    @if($role->is_system)
                                        <x-backend.status-badge variant="system" />
                                    @endif
                                </div>
                                <div class="small text-muted">{{ $role->slug }}</div>
                            </td>
                            <td>{{ count($role->permissions ?: []) }}</td>
                            <td>{{ $role->users_count }}</td>
                            <td>
                                <x-backend.status-badge :status="$role->status" />
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-2">
                                    <x-backend.action-button
                                        ability="roles.update"
                                        variant="outline-primary"
                                        :href="route('backend.roles.edit', $role)"
                                        icon="mdi mdi-pencil-outline"
                                    >
                                        Edit
                                    </x-backend.action-button>

                                    @if(auth()->user()?->can('roles.delete'))
                                        <form
                                            method="POST"
                                            action="{{ route('backend.roles.destroy', $role) }}"
                                            data-confirm-submit
                                            data-confirm-title="Delete this role?"
                                            data-confirm-text="{{ $role->name }} will be permanently removed if it is not assigned to any user."
                                            data-confirm-button-text="Yes, delete role"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <x-backend.action-button
                                                as="button"
                                                type="submit"
                                                variant="danger"
                                                icon="mdi mdi-delete-outline"
                                                :disabled="$role->is_system || $role->users_count > 0"
                                            >
                                                Delete
                                            </x-backend.action-button>
                                        </form>
                                    @endif
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
