@php
    $selected = old('permissions', $selectedPermissions ?? []);
    $status = (string) old('status', $role?->status ?? 1);
@endphp

<form method="POST" action="{{ $action }}">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="row g-4">
        <div class="col-12 col-xl-4">
            <div class="card user-management-card">
                <div class="card-body">
                    <h4 class="user-form-card__heading mb-3">Role Details</h4>

                    <div class="mb-3">
                        <label class="form-label" for="role-name">Role Name <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            class="form-control @error('name') is-invalid @enderror"
                            id="role-name"
                            name="name"
                            value="{{ old('name', $role?->name) }}"
                            placeholder="Admin, Manager, Editor"
                        >
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-0">
                        <label class="form-label" for="role-status">Status <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" id="role-status" name="status">
                            <option value="1" {{ $status === '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ $status === '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-8">
            <div class="card user-management-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <h4 class="user-form-card__heading mb-1">Permissions</h4>
                            <p class="text-muted small mb-0">Fixed module tree. Route names are metadata only.</p>
                        </div>
                        <span class="badge user-form-page-badge">{{ count($selected) }} selected</span>
                    </div>

                    @error('permissions')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror

                    <div class="permission-tree">
                        @foreach($modules as $module)
                            @include('backend.pages.roles.partials.permission-tree', [
                                'module' => $module,
                                'selectedPermissions' => $selected,
                                'level' => 0,
                            ])
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="user-form-actions d-flex flex-wrap gap-2 justify-content-end">
        <a href="{{ route('backend.roles.index') }}" class="btn btn-light">Cancel</a>
        <button type="submit" class="btn btn-gradient-primary">
            <i class="mdi mdi-content-save-outline me-1"></i>
            {{ $submitText }}
        </button>
    </div>
</form>
