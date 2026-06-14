@php
    $permissions = $module['permissions'] ?? [];
    $subModules = $module['sub_modules'] ?? [];
@endphp

<div class="permission-tree__module permission-tree__module--level-{{ $level }}">
    <div class="permission-tree__module-title">
        <i class="mdi mdi-folder-key-outline me-1"></i>
        {{ $module['name'] }}
    </div>

    @if($permissions)
        <div class="permission-tree__actions">
            @foreach($permissions as $permission)
                <label class="permission-tree__permission">
                    <input
                        type="checkbox"
                        class="form-check-input"
                        name="permissions[]"
                        value="{{ $permission['key'] }}"
                        @checked(in_array($permission['key'], $selectedPermissions, true))
                    >
                    <span>
                        <strong>{{ ucfirst($permission['action']) }}</strong>
                        <small>{{ $permission['key'] }}</small>
                    </span>
                </label>
            @endforeach
        </div>
    @endif

    @foreach($subModules as $subModule)
        @include('backend.pages.roles.partials.permission-tree', [
            'module' => $subModule,
            'selectedPermissions' => $selectedPermissions,
            'level' => $level + 1,
        ])
    @endforeach
</div>
