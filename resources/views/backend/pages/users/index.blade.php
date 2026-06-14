@extends('backend.layout.app')

@section('title', 'User Management')

@section('content')
<div
    id="userManagementPage"
    class="user-management-shell user-management-shell--index"
    data-data-url="{{ route('backend.users.data') }}"
    data-show-url-template="{{ route('backend.users.show', ['user' => '__USER_ID__']) }}"
    data-edit-url-template="{{ route('backend.users.edit', ['user' => '__USER_ID__']) }}"
    data-delete-url-template="{{ route('backend.users.destroy', ['user' => '__USER_ID__']) }}"
    data-export-url="{{ route('backend.users.export.csv') }}"
    data-user-type-options-url="{{ route('backend.users.options.user-types') }}"
>
    <div class="user-index-topbar">
        <div class="user-index-heading">
            <span class="user-index-heading__icon bg-gradient-primary text-white">
                <i class="mdi mdi-account-group-outline"></i>
            </span>
            <div>
                <p class="user-index-heading__kicker">Management Module</p>
                <h3 class="user-index-heading__title">User Management</h3>
            </div>
        </div>

        <div class="user-summary-strip" aria-label="User summary">
            <div class="user-summary-chip user-summary-chip--total">
                <span class="user-summary-chip__icon"><i class="mdi mdi-account-group-outline"></i></span>
                <span class="user-summary-chip__copy">
                    <span class="user-summary-chip__label">Total</span>
                    <strong data-summary="total">0</strong>
                </span>
            </div>
            <div class="user-summary-chip user-summary-chip--active">
                <span class="user-summary-chip__icon"><i class="mdi mdi-account-check-outline"></i></span>
                <span class="user-summary-chip__copy">
                    <span class="user-summary-chip__label">Active</span>
                    <strong data-summary="active">0</strong>
                </span>
            </div>
            <div class="user-summary-chip user-summary-chip--inactive">
                <span class="user-summary-chip__icon"><i class="mdi mdi-account-off-outline"></i></span>
                <span class="user-summary-chip__copy">
                    <span class="user-summary-chip__label">Inactive</span>
                    <strong data-summary="inactive">0</strong>
                </span>
            </div>
            <div class="user-summary-chip user-summary-chip--filtered">
                <span class="user-summary-chip__icon"><i class="mdi mdi-filter-outline"></i></span>
                <span class="user-summary-chip__copy">
                    <span class="user-summary-chip__label">Filtered</span>
                    <strong data-summary="filtered">0</strong>
                </span>
            </div>
        </div>

        <div class="user-index-actions">
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

    <div class="card user-management-card user-directory-panel">
        <div class="card-body">
            <div class="user-directory-bar">
                <div class="user-directory-heading">
                    <h4 class="user-directory-title">User Directory</h4>
                    <p class="user-directory-subtitle">Search, filter, sort, export and manage users without a full-page reload.</p>
                </div>

                <div class="user-toolbar">
                    <button type="button" class="btn btn-sm" id="refreshUsersTable">
                        <i class="mdi mdi-refresh me-1"></i>
                        Refresh
                    </button>
                    <button type="button" class="btn btn-sm" id="clearUsersFilters">
                        <i class="mdi mdi-filter-remove-outline me-1"></i>
                        Clear
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

            <div class="user-table-controls">
                <div class="user-control-field user-control-field--type">
                    <label class="form-label" for="filterUserType">User Type</label>
                    <select class="form-select" id="filterUserType" data-placeholder="All user types"></select>
                </div>
                <div class="user-control-field user-control-field--status">
                    <label class="form-label" for="filterUserStatus">Status</label>
                    <select class="form-select" id="filterUserStatus">
                        <option value="">All statuses</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <div class="user-control-field user-control-field--length">
                    <label class="form-label" for="usersPerPage">Rows</label>
                    <select class="form-select" id="usersPerPage">
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <div class="user-control-field user-control-field--search">
                    <label class="form-label" for="usersQuickSearch">Search</label>
                    <div class="user-search-input">
                        <i class="mdi mdi-magnify"></i>
                        <input type="search" class="form-control" id="usersQuickSearch" placeholder="Name, email or phone">
                    </div>
                </div>
            </div>

            <div class="table-responsive user-table-wrap">
                <table class="table table-hover align-middle w-100" id="usersTable">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>User Type</th>
                        <th>Roles</th>
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
