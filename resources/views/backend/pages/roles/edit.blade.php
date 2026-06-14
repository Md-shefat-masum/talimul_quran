@extends('backend.layout.app')

@section('title', 'Edit Role')

@section('content')
<div class="user-management-shell">
    <div class="user-management-hero user-management-hero--compact">
        <div>
            <div class="user-management-hero__eyebrow">Access Control / Edit</div>
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-shield-edit-outline"></i>
                </span>
                Edit Role
            </h3>
            <p class="user-management-hero__subtitle">Editing: {{ $role->name }}</p>
        </div>
        <a href="{{ route('backend.roles.index') }}" class="btn btn-outline-primary btn-sm mt-3 mt-md-0">
            <i class="mdi mdi-arrow-left me-1"></i>
            Back to Roles
        </a>
    </div>

    @include('backend.pages.roles.partials.form', [
        'role' => $role,
        'modules' => $modules,
        'selectedPermissions' => $selectedPermissions,
        'action' => route('backend.roles.update', $role),
        'method' => 'PUT',
        'submitText' => 'Update Role',
    ])
</div>
@endsection
