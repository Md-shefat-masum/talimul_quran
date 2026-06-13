<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Source Of Truth
    |--------------------------------------------------------------------------
    |
    | The active file manager lists folders and files from database tables.
    | The old FTP-listing implementation is preserved in
    | LegacyFtpFileManagerService for future reuse, but it is not active.
    |
    */
    'mode' => env('FILE_MANAGER_MODE', 'database'),
    'storage_disk' => env('FILE_MANAGER_STORAGE_DISK', 'ftp'),

    /*
    |--------------------------------------------------------------------------
    | Import Audit Retention
    |--------------------------------------------------------------------------
    |
    | Import rows are useful for operator history, but should not grow without
    | a maintenance policy. The prune command keeps recent rows by age and also
    | preserves the newest fixed number of rows.
    |
    */
    'import_retention_days' => (int) env('FILE_MANAGER_IMPORT_RETENTION_DAYS', 90),
    'import_retention_keep' => (int) env('FILE_MANAGER_IMPORT_RETENTION_KEEP', 100),
    'import_retention_schedule' => [
        'enabled' => filter_var(env('FILE_MANAGER_PRUNE_IMPORTS_SCHEDULE', false), FILTER_VALIDATE_BOOL),
        'time' => env('FILE_MANAGER_PRUNE_IMPORTS_TIME', '02:30'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Public Route Compatibility
    |--------------------------------------------------------------------------
    |
    | The active file manager is dashboard-only. Guest access is locked by
    | default and can be reopened explicitly for a temporary staging surface.
    |
    */
    'allow_guest' => env('FILE_MANAGER_ALLOW_GUEST', false),

    /*
    |--------------------------------------------------------------------------
    | Default Authenticated Access
    |--------------------------------------------------------------------------
    |
    | If no policy, gate, or permission-package method is present, authenticated
    | dashboard users receive these default file-manager abilities.
    |
    */
    'default_authenticated_permissions' => [
        'read' => true,
        'upload' => true,
        'create_folder' => true,
        'rename' => true,
        'move' => true,
        'delete' => true,
        'force_delete' => false,
        'track_usage' => true,
        'forget_usage' => true,
        'maintenance' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | User Type Permission Profiles
    |--------------------------------------------------------------------------
    |
    | If the authenticated user has a related userType with a code, these
    | profiles are applied before the default authenticated permissions. This
    | keeps action visibility and backend guards aligned without requiring a
    | permission package for smaller installations.
    |
    */
    'user_type_permissions' => [
        'admin' => [
            'read' => true,
            'upload' => true,
            'create_folder' => true,
            'rename' => true,
            'move' => true,
            'delete' => true,
            'force_delete' => false,
            'track_usage' => true,
            'forget_usage' => true,
            'maintenance' => true,
        ],
        'editor' => [
            'read' => true,
            'upload' => true,
            'create_folder' => true,
            'rename' => true,
            'move' => true,
            'delete' => false,
            'force_delete' => false,
            'track_usage' => true,
            'forget_usage' => false,
            'maintenance' => false,
        ],
        'viewer' => [
            'read' => true,
            'upload' => false,
            'create_folder' => false,
            'rename' => false,
            'move' => false,
            'delete' => false,
            'force_delete' => false,
            'track_usage' => false,
            'forget_usage' => false,
            'maintenance' => false,
        ],
    ],

    'guest_permissions' => [
        'read' => false,
        'upload' => false,
        'create_folder' => false,
        'rename' => false,
        'move' => false,
        'delete' => false,
        'force_delete' => false,
        'track_usage' => false,
        'forget_usage' => false,
        'maintenance' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Permission Names
    |--------------------------------------------------------------------------
    |
    | These names are checked against Spatie-style permission methods when
    | available, and against Laravel gates only when a gate is defined.
    |
    */
    'abilities' => [
        'read' => ['file-manager.read', 'file-manager.view', 'file-manager.manage'],
        'upload' => ['file-manager.upload', 'file-manager.write', 'file-manager.manage'],
        'create_folder' => ['file-manager.create-folder', 'file-manager.write', 'file-manager.manage'],
        'rename' => ['file-manager.rename', 'file-manager.write', 'file-manager.manage'],
        'move' => ['file-manager.move', 'file-manager.write', 'file-manager.manage'],
        'delete' => ['file-manager.delete', 'file-manager.manage'],
        'force_delete' => ['file-manager.force-delete', 'file-manager.manage'],
        'track_usage' => ['file-manager.track-usage', 'file-manager.write', 'file-manager.manage'],
        'forget_usage' => ['file-manager.forget-usage', 'file-manager.write', 'file-manager.manage'],
        'maintenance' => ['file-manager.maintenance', 'file-manager.manage'],
    ],
];
