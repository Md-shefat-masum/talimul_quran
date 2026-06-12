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
