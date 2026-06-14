@extends('backend.layout.app')

@section('title', 'Create User')

@section('content')
<div class="user-management-shell">
    <div class="user-management-hero user-management-hero--compact">
        <div>
            <div class="user-management-hero__eyebrow">User Management / Create</div>
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-account-plus-outline"></i>
                </span>
                Create User
            </h3>
            <p class="user-management-hero__subtitle">Use the same reusable account form that powers the quick-add modal.</p>
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
                            <p class="text-muted small mb-0">Fields marked with an asterisk are required.</p>
                        </div>
                        <span class="badge user-form-page-badge">Create Page</span>
                    </div>

                    @include('backend.pages.users.partials.form', [
                        'formId' => 'userCreatePageForm',
                        'mode' => 'create',
                        'context' => 'page',
                        'user' => null,
                        'roles' => $roles,
                        'submitText' => 'Create User',
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
