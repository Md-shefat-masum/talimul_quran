@extends('backend.layout.app')

@section('title', 'My Profile')

@section('content')
@php
    $primaryRole = $user->roles->first()?->name ?? 'No Role';
    $profileImagePath = old('profile_image_path', $user->profile_image_path ?? $user->avatar_path);
    $profileImageUrl = old('profile_image_url', $user->profileImageUrl() ?? $user->avatar_url);
@endphp

<div class="user-management-shell">
    <div class="user-management-hero user-management-hero--compact">
        <div>
            <div class="user-management-hero__eyebrow">Account / Profile</div>
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-account-circle-outline"></i>
                </span>
                My Profile
            </h3>
            <p class="user-management-hero__subtitle">Keep your personal account details and profile image up to date.</p>
        </div>
        <span class="badge user-form-page-badge mt-3 mt-md-0">{{ $primaryRole }}</span>
    </div>

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2">
            <i class="mdi mdi-check-circle-outline"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-12 col-xl-4">
            <div class="card user-management-card user-form-card h-100">
                <div class="card-body text-center">
                    <div class="sidebar-profile-card__avatar mx-auto mb-3">
                        <img src="{{ $profileImageUrl ?: asset('assets/backend/images/default-avatar.svg') }}" alt="User avatar">
                    </div>
                    <h4 class="user-form-card__heading mb-1">{{ $user->name }}</h4>
                    <p class="text-muted small mb-1">{{ $user->email }}</p>
                    <p class="text-muted small mb-0">{{ $user->userType?->name ?? 'No user type assigned' }}</p>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-8">
            <div class="card user-management-card user-form-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                        <div>
                            <h4 class="user-form-card__heading mb-1">Profile Details</h4>
                            <p class="text-muted small mb-0">Leave password blank to keep your current password.</p>
                        </div>
                        <span class="badge user-form-page-badge">Self Service</span>
                    </div>

                    <form method="POST" action="{{ route('backend.profile.update') }}" class="user-form-component">
                        @csrf
                        @method('PATCH')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="profile-name">Full Name <span class="text-danger">*</span></label>
                                <input
                                    type="text"
                                    class="form-control @error('name') is-invalid @enderror"
                                    id="profile-name"
                                    name="name"
                                    value="{{ old('name', $user->name) }}"
                                    autocomplete="name"
                                >
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="profile-email">Email Address <span class="text-danger">*</span></label>
                                <input
                                    type="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    id="profile-email"
                                    name="email"
                                    value="{{ old('email', $user->email) }}"
                                    autocomplete="email"
                                >
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="profile-phone">Phone Number</label>
                                <input
                                    type="text"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    id="profile-phone"
                                    name="phone"
                                    value="{{ old('phone', $user->phone) }}"
                                    autocomplete="tel"
                                >
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="profile-role">Primary Role</label>
                                <input type="text" class="form-control" id="profile-role" value="{{ $primaryRole }}" readonly>
                            </div>

                            <div class="col-12">
                                @include('backend.components.file-manager-picker', [
                                    'formId' => 'profileForm',
                                    'field' => 'profile_image_url',
                                    'pathField' => 'profile_image_path',
                                    'id' => 'profile-image-url',
                                    'label' => 'Profile Image',
                                    'value' => $profileImageUrl,
                                    'pathValue' => $profileImagePath,
                                    'placeholder' => 'No profile image selected',
                                    'preview' => 'image',
                                    'size' => '512x512',
                                    'folder' => 'users/profile-images',
                                    'usageModule' => 'user-profile',
                                    'usageField' => 'profile_image_path',
                                    'ownerType' => \App\Models\User::class,
                                    'ownerId' => $user->id,
                                    'usageLabel' => $user->name.' profile image',
                                ])
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="profile-password">New Password</label>
                                <input
                                    type="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    id="profile-password"
                                    name="password"
                                    placeholder="Leave blank to keep current password"
                                    autocomplete="new-password"
                                >
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="profile-password-confirmation">Confirm New Password</label>
                                <input
                                    type="password"
                                    class="form-control"
                                    id="profile-password-confirmation"
                                    name="password_confirmation"
                                    autocomplete="new-password"
                                >
                            </div>
                        </div>

                        <div class="user-form-actions d-flex flex-wrap gap-2 justify-content-end">
                            <button type="submit" class="btn btn-gradient-primary">
                                <i class="mdi mdi-content-save-outline me-1"></i>
                                Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
