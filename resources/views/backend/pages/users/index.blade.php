@extends('backend.layout.app')

@section('title', 'User Management')

@section('content')
<div
    id="userManagementPage"
    class="user-management-shell"
    data-data-url="{{ route('backend.users.data') }}"
    data-show-url-template="{{ route('backend.users.show', ['user' => '__USER_ID__']) }}"
    data-edit-url-template="{{ route('backend.users.edit', ['user' => '__USER_ID__']) }}"
    data-delete-url-template="{{ route('backend.users.destroy', ['user' => '__USER_ID__']) }}"
    data-export-url="{{ route('backend.users.export.csv') }}"
    data-user-type-options-url="{{ route('backend.users.options.user-types') }}"
>
    <div class="user-management-hero">
        <div>
            <div class="user-management-hero__eyebrow">Management Module</div>
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-account-group-outline"></i>
                </span>
                User Management
            </h3>
            <p class="user-management-hero__subtitle">Manage accounts with a reusable CRUD pattern for future modules.</p>
        </div>

        <div class="d-flex flex-wrap gap-2 mt-3 mt-md-0">
            <a href="{{ route('backend.users.create') }}" class="btn btn-outline-primary btn-sm">
                <i class="mdi mdi-open-in-new me-1"></i>
                Full-page Form
            </a>
            <button type="button" class="btn btn-gradient-primary btn-sm" id="openCreateUserModal">
                <i class="mdi mdi-account-plus-outline me-1"></i>
                Quick Add User
            </button>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card user-stat-card user-stat-card--total h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="user-stat-icon"><i class="mdi mdi-account-group-outline"></i></span>
                    <div>
                        <p class="user-stat-label">Total Users</p>
                        <h4 class="user-stat-value mb-0" data-summary="total">0</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card user-stat-card user-stat-card--active h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="user-stat-icon"><i class="mdi mdi-account-check-outline"></i></span>
                    <div>
                        <p class="user-stat-label">Active</p>
                        <h4 class="user-stat-value mb-0" data-summary="active">0</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card user-stat-card user-stat-card--inactive h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="user-stat-icon"><i class="mdi mdi-account-off-outline"></i></span>
                    <div>
                        <p class="user-stat-label">Inactive</p>
                        <h4 class="user-stat-value mb-0" data-summary="inactive">0</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card user-stat-card user-stat-card--filtered h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="user-stat-icon"><i class="mdi mdi-filter-outline"></i></span>
                    <div>
                        <p class="user-stat-label">Filtered Result</p>
                        <h4 class="user-stat-value mb-0" data-summary="filtered">0</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card user-management-card">
        <div class="card-body">
            <div class="d-flex flex-column flex-xl-row justify-content-between gap-3 mb-4">
                <div>
                    <h4 class="user-directory-title">User Directory</h4>
                    <p class="user-directory-subtitle mb-0">Search, filter, sort, export and manage users without a full-page reload.</p>
                </div>

                <div class="d-flex flex-wrap gap-2 align-items-center user-toolbar">
                    <button type="button" class="btn btn-sm" id="refreshUsersTable">
                        <i class="mdi mdi-refresh me-1"></i>
                        Refresh
                    </button>
                    <button type="button" class="btn btn-sm" id="clearUsersFilters">
                        <i class="mdi mdi-filter-remove-outline me-1"></i>
                        Clear Filters
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="mdi mdi-table-column me-1"></i>
                            Columns
                        </button>
                        <div class="dropdown-menu dropdown-menu-end p-3 user-column-menu" id="userColumnsMenu"></div>
                    </div>
                    <a href="{{ route('backend.users.export.csv') }}" class="btn btn-sm user-export-btn" id="exportUsersCsv">
                        <i class="mdi mdi-file-delimited-outline me-1"></i>
                        Export CSV
                    </a>
                </div>
            </div>

            <div class="row g-3 mb-4 user-filter-panel">
                <div class="col-md-6 col-xl-4">
                    <label class="form-label" for="filterUserType">User Type</label>
                    <select class="form-select" id="filterUserType" data-placeholder="All user types"></select>
                </div>
                <div class="col-md-6 col-xl-3">
                    <label class="form-label" for="filterUserStatus">Status</label>
                    <select class="form-select" id="filterUserStatus">
                        <option value="">All statuses</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle w-100" id="usersTable">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>User Type</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

@include('backend.pages.users.partials.modal')
@endsection

@push('scripts')
    <script src="{{ asset('assets/backend/js/modules/users/form.js') }}?v={{ env('APP_VERSION', '1.0.0') }}"></script>
    <script src="{{ asset('assets/backend/js/modules/users/index.js') }}?v={{ env('APP_VERSION', '1.0.0') }}"></script>
@endpush
