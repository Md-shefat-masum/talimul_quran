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
            <x-backend.form-card title="Role Details">
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
            </x-backend.form-card>
        </div>

        <div class="col-12 col-xl-8">
            <x-backend.form-card
                title="Permissions"
                subtitle="Fixed module tree. Route names are metadata only."
                badge="{{ count($selected) }} selected"
            >
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
            </x-backend.form-card>
        </div>
    </div>

    <div class="user-form-actions d-flex flex-wrap gap-2 justify-content-end">
        <x-backend.action-button
            variant="light"
            size=""
            :href="route('backend.roles.index')"
        >
            Cancel
        </x-backend.action-button>
        <x-backend.action-button
            as="button"
            type="submit"
            size=""
            icon="mdi mdi-content-save-outline"
        >
            {{ $submitText }}
        </x-backend.action-button>
    </div>
</form>
