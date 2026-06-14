@extends('backend.layout.app')

@section('title', 'Edit User')

@section('content')
<div class="user-management-shell">
    <div class="user-management-hero user-management-hero--compact">
        <div>
            <div class="user-management-hero__eyebrow">User Management / Edit</div>
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-account-edit-outline"></i>
                </span>
                Edit User
            </h3>
            <p class="user-management-hero__subtitle">Update account information. Leave password blank to keep the current password.</p>
        </div>
        <a href="{{ route('backend.users.index') }}" class="btn btn-outline-primary btn-sm mt-3 mt-md-0">
            <i class="mdi mdi-arrow-left me-1"></i>
            Back to Directory
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="card user-management-card user-form-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                        <div>
                            <h4 class="user-form-card__heading mb-1">Account Details</h4>
                            <p class="text-muted small mb-0">Editing: {{ $user->name }}</p>
                        </div>
                        <span class="badge user-form-page-badge">Edit Page</span>
                    </div>

                    @include('backend.pages.users.partials.form', [
                        'formId' => 'userEditPageForm',
                        'mode' => 'edit',
                        'context' => 'page',
                        'user' => $user,
                        'roles' => $roles,
                        'submitText' => 'Update User',
                    ])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/backend/js/modules/users/form.js') }}?v={{ env('APP_VERSION', '1.0.0') }}"></script>
@endpush
