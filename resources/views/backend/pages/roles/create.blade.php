@extends('backend.layout.app')

@section('title', 'Create Role')

@section('content')
<div class="user-management-shell">
    <div class="user-management-hero user-management-hero--compact">
        <div>
            <div class="user-management-hero__eyebrow">Access Control / Create</div>
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-shield-plus-outline"></i>
                </span>
                Create Role
            </h3>
            <p class="user-management-hero__subtitle">Assign permissions from the fixed manual permission registry.</p>
        </div>
        <x-backend.action-button
            variant="outline-primary"
            :href="route('backend.roles.index')"
            icon="mdi mdi-arrow-left"
            class="mt-3 mt-md-0"
        >
            Back to Roles
        </x-backend.action-button>
    </div>

    @include('backend.pages.roles.partials.form', [
        'role' => $role,
        'modules' => $modules,
        'selectedPermissions' => $selectedPermissions,
        'action' => route('backend.roles.store'),
        'method' => 'POST',
        'submitText' => 'Create Role',
    ])
</div>
@endsection
