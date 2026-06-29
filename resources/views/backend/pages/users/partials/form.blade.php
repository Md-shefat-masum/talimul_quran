@php
    $formId = $formId ?? 'userForm';
    $mode = $mode ?? 'create';
    $context = $context ?? 'page';
    $submitText = $submitText ?? ($mode === 'edit' ? 'Update User' : 'Save User');
    $selectedTypeId = old('user_type_id', $user?->user_type_id);
    $selectedTypeText = old('user_type_text', $user?->userType?->name);
    $selectedRoleIds = collect(old('roles', $user?->roles?->pluck('id')->all() ?? []))
        ->map(fn($id) => (int) $id)
        ->all();
    $selectedStatus = (string) old('status', $user?->status ?? 1);
    $profileImagePath = old('profile_image_path', $user?->profile_image_path ?? $user?->avatar_path);
    $profileImageUrl = old('profile_image_url', $user?->profileImageUrl() ?? $user?->avatar_url);
    $additionalImagePaths = old('additional_image_paths', $user?->additional_image_paths ?? []);
    $additionalImageUrls = old('additional_image_urls', $user?->additionalImageUrls() ?? []);
@endphp

<form id="{{ $formId }}" class="js-user-form user-form-component" data-context="{{ $context }}"
    data-mode="{{ $mode }}" data-user-id="{{ $user?->id }}" data-store-url="{{ route('backend.users.store') }}"
    data-update-url-template="{{ route('backend.users.update', ['user' => '__USER_ID__']) }}"
    data-index-url="{{ route('backend.users.index') }}"
    data-user-type-options-url="{{ route('backend.users.options.user-types') }}" novalidate>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label" for="{{ $formId }}-name">Full Name <span
                    class="text-danger">*</span></label>
            <input type="text" class="form-control" id="{{ $formId }}-name" name="name"
                value="{{ old('name', $user?->name) }}" placeholder="Enter full name" autocomplete="name">
            <div class="invalid-feedback" data-error-for="name"></div>
        </div>

        <div class="col-md-6">
            <label class="form-label" for="{{ $formId }}-email">Email Address <span
                    class="text-danger">*</span></label>
            <input type="email" class="form-control" id="{{ $formId }}-email" name="email"
                value="{{ old('email', $user?->email) }}" placeholder="name@example.com" autocomplete="email">
            <div class="invalid-feedback" data-error-for="email"></div>
        </div>

        <div class="col-md-6">
            <label class="form-label" for="{{ $formId }}-phone">Phone Number</label>
            <input type="text" class="form-control" id="{{ $formId }}-phone" name="phone"
                value="{{ old('phone', $user?->phone) }}" placeholder="Optional contact number" autocomplete="tel">
            <div class="invalid-feedback" data-error-for="phone"></div>
        </div>

        <div class="col-md-6">
            <label class="form-label" for="{{ $formId }}-user-type">User Type <span
                    class="text-danger">*</span></label>
            <select class="form-select js-user-type-select" id="{{ $formId }}-user-type" name="user_type_id"
                data-placeholder="Search and select user type">
                @if ($selectedTypeId && $selectedTypeText)
                    <option value="{{ $selectedTypeId }}" selected>{{ $selectedTypeText }}</option>
                @endif
            </select>
            <div class="invalid-feedback d-block" data-error-for="user_type_id"></div>
        </div>

        <div class="col-md-6">
            <label class="form-label" for="{{ $formId }}-status">Status <span
                    class="text-danger">*</span></label>
            <select class="form-select" id="{{ $formId }}-status" name="status">
                <option value="1" {{ $selectedStatus === '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ $selectedStatus === '0' ? 'selected' : '' }}>Inactive</option>
            </select>
            <div class="invalid-feedback" data-error-for="status"></div>
        </div>

        <div class="col-md-6">
            <label class="form-label" for="{{ $formId }}-roles">Roles</label>
            <select class="form-select js-user-role-select" id="{{ $formId }}-roles" name="roles[]" multiple
                data-placeholder="Select one or more roles">
                @foreach ($roles ?? [] as $role)
                    <option value="{{ $role->id }}" @selected(in_array($role->id, $selectedRoleIds, true))>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
            <div class="invalid-feedback d-block" data-error-for="roles"></div>
        </div>

        <div class="col-md-6 d-flex align-items-end">
            <div class="user-form-tip w-100">
                <i class="mdi mdi-information-outline me-1"></i>
                Searchable selects help keep large user and role lists easy to scan.
            </div>
        </div>

        <div class="col-12">
            @include('backend.components.file-manager-picker', [
                'formId' => $formId,
                'field' => 'profile_image_url',
                'pathField' => 'profile_image_path',
                'id' => $formId . '-profile-image-url',
                'label' => 'Profile Image',
                'value' => $profileImageUrl,
                'pathValue' => $profileImagePath,
                'placeholder' => 'No profile image selected',
                'preview' => 'image',
                'size' => '512x512',
                'folder' => 'users/profile-images',
                'usageModule' => 'user-management',
                'usageField' => 'profile_image_path',
                'ownerType' => \App\Models\User::class,
                'ownerId' => $user?->id,
                'usageLabel' => $user?->name ? $user->name . ' profile image' : 'User profile image',
            ])
        </div>

        <div class="col-12">
            @include('backend.components.file-manager-picker', [
                'formId' => $formId,
                'field' => 'additional_image_urls',
                'pathField' => 'additional_image_paths',
                'id' => $formId . '-additional-image-urls',
                'label' => 'Additional Images',
                'value' => $additionalImageUrls,
                'pathValue' => $additionalImagePaths,
                'placeholder' => 'No additional images selected',
                'preview' => 'image',
                'folder' => 'users/additional-images',
                'multiple' => true,
                'valueFormat' => 'json',
                'buttonText' => 'Select Images',
                'usageModule' => 'user-management',
                'usageField' => 'additional_image_paths',
                'ownerType' => \App\Models\User::class,
                'ownerId' => $user?->id,
                'usageLabel' => $user?->name ? $user->name . ' additional images' : 'User additional images',
            ])
        </div>

        <div class="col-md-6">
            <label class="form-label" for="{{ $formId }}-password">
                Password
                @if ($mode === 'create')
                    <span class="text-danger">*</span>
                @endif
            </label>
            <input type="password" class="form-control" id="{{ $formId }}-password" name="password"
                placeholder="{{ $mode === 'edit' ? 'Leave blank to keep current password' : 'Minimum 8 characters' }}"
                autocomplete="new-password">
            <div class="invalid-feedback" data-error-for="password"></div>
        </div>

        <div class="col-md-6">
            <label class="form-label" for="{{ $formId }}-password-confirmation">Confirm Password</label>
            <input type="password" class="form-control" id="{{ $formId }}-password-confirmation"
                name="password_confirmation" placeholder="Repeat the password" autocomplete="new-password">
            <div class="invalid-feedback" data-error-for="password_confirmation"></div>
        </div>
    </div>

    <div class="user-form-actions d-flex flex-wrap gap-2 justify-content-end">
        @if ($context === 'modal')
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        @else
            <a href="{{ route('backend.users.index') }}" class="btn btn-light">Cancel</a>
        @endif

        <button type="submit" class="btn btn-gradient-primary js-user-form-submit">
            <span class="js-submit-idle">
                <i class="mdi mdi-content-save-outline me-1"></i>
                {{ $submitText }}
            </span>
            <span class="js-submit-loading d-none">
                <span class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span>
                Saving...
            </span>
        </button>
    </div>
</form>
