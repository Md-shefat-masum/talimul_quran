@extends('backend.layout.app')

@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
            <i class="mdi mdi-home"></i>
        </span>
        Dashboard
    </h3>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-3">Admin Dashboard Foundation</h4>
                <p class="text-muted mb-4">
                    The reusable backend layout is ready. User Management is the reference module for future CRUD features.
                </p>
                <a href="{{ route('backend.users.index') }}" class="btn btn-gradient-primary btn-sm">
                    <i class="mdi mdi-account-group-outline me-1"></i>
                    Open User Management
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
