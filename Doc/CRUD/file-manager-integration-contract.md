# File Manager Integration Contract

## Purpose
- Global file manager root lives in the backend layout as `<file-manager></file-manager>`.
- Frontend modules can open it with vanilla JS or `data-file-manager` attributes.
- Selected files write public URLs into form fields and raw storage paths into `data-selected-paths`.
- The active media source of truth is the database (`media_folders`, `media`, `media_in_uses`), not FTP directory scanning.
- FTP remains available as the default physical storage disk through `FILE_MANAGER_STORAGE_DISK=ftp`.
- Media organization is DB-only after upload. Folder/file rename and move actions must not move, rename, or delete physical disk paths.
- Optional usage tracking records where a media row is used and protects used files from accidental delete.

## Blade Picker Component
Use the reusable picker include for new CRUD forms:

```blade
@include('backend.components.file-manager-picker', [
    'formId' => 'courseForm',
    'field' => 'cover_url',
    'pathField' => 'cover_path',
    'id' => 'courseForm-cover-url',
    'label' => 'Cover Image',
    'value' => old('cover_url', $course?->cover_url),
    'pathValue' => old('cover_path', $course?->cover_path),
    'size' => '1600x600',
    'folder' => 'courses/covers',
    'usageModule' => 'course-management',
    'usageField' => 'cover_url',
    'ownerType' => \App\Models\Course::class,
    'ownerId' => $course?->id,
    'usageLabel' => $course?->title ? $course->title.' cover' : 'Course cover',
])
```

Expected database fields:
- `cover_url`: public URL saved to forms and rendered to users.
- `cover_path`: normalized storage path used to resolve the DB media row and usage tracking.

Multiple/gallery picker:

```blade
@include('backend.components.file-manager-picker', [
    'formId' => 'courseForm',
    'field' => 'gallery_urls',
    'pathField' => 'gallery_paths',
    'id' => 'courseForm-gallery-urls',
    'label' => 'Gallery',
    'value' => old('gallery_urls', $course?->gallery_urls),
    'pathValue' => old('gallery_paths', $course?->gallery_paths),
    'multiple' => true,
    'valueFormat' => 'json',
    'folder' => 'courses/gallery',
    'buttonText' => 'Select Gallery',
])
```

For multiple pickers:
- URL field stores a JSON array when `valueFormat` is `json`.
- Path field stores a JSON array of raw FTP paths.
- The display input shows the selected file count.
- The gallery chip list supports removing a single selected item without reopening the modal.
- User management now uses this contract for `document_urls` and `document_paths`, with usage tracking under `user-management / document_urls`.

## Data Attribute Contract
Any button can open the file manager:

```html
<input type="hidden" id="hero_image" name="hero_image">
<button
    type="button"
    data-file-manager
    data-file-manager-target="#hero_image"
    data-file-manager-size="1600x600"
    data-file-manager-path="pages/home"
    data-file-manager-usage-module="homepage"
    data-file-manager-usage-field="hero_image"
    data-file-manager-owner-type="App\Models\Page"
    data-file-manager-owner-id="home"
    data-file-manager-usage-label="Homepage hero"
>
    Select image
</button>
```

Optional attributes:
- `data-file-manager-multiple`: allow multiple file selection.
- `data-file-manager-accept`: file input accept hint, default `image/*`.
- `data-file-manager-size`: requested editor size like `512x512`.
- `data-file-manager-path`: initial media folder path.
- `data-file-manager-value-format`: `string` or `json`; use `json` for gallery/multiple inputs.

Usage attributes should be emitted only when an owner id already exists. For create forms, save `*_url` and `*_path` first, then call `FileManagerUsageService::track()` after the model is created.

After selection:
- Target value receives selected public URL or comma-separated URLs.
- If `data-file-manager-value-format="json"`, target value receives a JSON array of selected public URLs.
- Target `data-selected-paths` receives JSON array of raw storage paths.
- Target `data-selected-urls` receives JSON array of public URLs.
- Target emits `change` and `file-manager:selected`.

## Vanilla JS Contract
Open manually:

```js
window.FileManager.open({
    target: document.querySelector('#hero_image'),
    multiple: false,
    valueFormat: 'string',
    path: 'pages/home',
    size: {width: 1600, height: 600},
    usage: {
        module: 'homepage',
        owner_type: 'App\\Models\\Page',
        owner_id: 'home',
        field_name: 'hero_image',
        label: 'Homepage hero'
    },
    callback: function (items, urls, paths) {
        console.log(items, urls, paths);
    }
});
```

## API Contract
All file manager API routes require an authenticated dashboard user through the
`file-manager.auth` middleware. Unauthenticated JSON requests return
`401 Unauthorized`; direct browser hits return a plain `401` response instead of
depending on a named login route.

Current routes:
- `GET /dashboard/file-manager?path=folder/path&page=1&per_page=60&q=search`
- `GET /dashboard/file-manager/preview?path=file/path.jpg`
- `GET /dashboard/file-manager/thumbnail?path=file/path.jpg&width=360&height=270`
- `GET /dashboard/file-manager/maintenance/thumbnail-cache`
- `DELETE /dashboard/file-manager/maintenance/thumbnail-cache`
- `POST /dashboard/file-manager/maintenance/import`
- `POST /dashboard/file-manager/photo`
- `POST /dashboard/file-manager/folder`
- `PATCH /dashboard/file-manager/item/rename`
- `PATCH /dashboard/file-manager/item/move`
- `DELETE /dashboard/file-manager/item`
- `GET /dashboard/file-manager/usage?path=file/path.jpg&type=file`
- `POST /dashboard/file-manager/usage`
- `DELETE /dashboard/file-manager/usage`

List pagination:
- Folder/file results are read from `media_folders` and `media` rows.
- `page`: 1-based page number.
- `per_page`: max 120 items per page.
- `q`: optional filename/folder-name search query.
- Response includes `pagination.page`, `pagination.per_page`, `pagination.total`, `pagination.shown`, `pagination.has_more`, and `pagination.next_page`.
- Frontend appends `Load more` results and uses server-backed search instead of filtering the full folder in memory.
- Image previews render with `loading="lazy"` and `decoding="async"`.

Thumbnail cache:
- Image file items include `thumbnail_url` for grid cards and `preview_url` for larger detail/open previews.
- Thumbnail endpoint resolves the media DB row, reads the physical file from its configured disk, creates a bounded JPEG derivative with GD, and caches it under `storage/app/private/file-manager-thumbnails`.
- Cache keys include storage path, media updated timestamp, media size, and requested dimensions.
- If image decoding fails, the endpoint falls back to the original preview contents.
- Maintenance endpoints return thumbnail cache file count, byte count, readable byte label, and cache path.
- Thumbnail cache clear requires the `maintenance` permission ability and is denied to guests by default.
- The sidebar maintenance panel is shown only when `window.FileManagerConfig.permissions.maintenance` is true.
- The panel can refresh cache stats and clear cached thumbnails from inside the file manager modal.

Delete guard:
- If a file/folder has active `media_in_uses` tracks, delete returns `409 Conflict`.
- Frontend shows usage context and asks before force delete.
- Force delete sends `force: true`.
- Folder delete guard checks the DB folder subtree through `media_folder_id`, not storage path prefixes.

Storage-safe organization:
- Folder rename updates display name only; `saved_name_into_storage` remains stable.
- Folder create/move/rename/delete do not create, move, rename, or delete FTP/main-disk directories.
- File rename updates display metadata only; `media.path` remains stable.
- File move updates `media_folder_id` only; `media.path` remains stable.
- API items may include `display_path` and `storage_path`; forms should continue storing selected public URL and raw storage path.

Storage import:
- Existing storage files are registered into DB media rows only through explicit import tooling.
- Normal list/search requests must not scan FTP/storage.
- Every explicit import creates a `media_imports` audit row.
- Artisan command:
  - `php artisan file-manager:import-media uploads`
  - `php artisan file-manager:import-media legacy --dry-run --limit=100`
  - `php artisan file-manager:import-media uploads --no-recursive`
- Maintenance API accepts `path`, `recursive`, `dry_run`, and `limit`.
- Maintenance history API returns recent audit rows from `/dashboard/file-manager/maintenance/imports`.
- Import creates missing DB folder rows and creates/updates `media` rows, but never moves/renames/deletes physical files or directories.
- Import responses include `import_id`, `status`, `started_at`, and `finished_at`.

Photo upload conflict handling:
- `conflict_strategy=error`: stop on duplicate filename and return `409 Conflict`.
- `conflict_strategy=rename`: save with the next available filename, for example `hero-2.jpg`.
- `conflict_strategy=replace`: overwrite/update the media entry at the same storage path.
- Duplicate responses include `conflict.path`, `conflict.name`, `conflict.suggested_name`, and `conflict.suggested_file_name`.
- Frontend upload requests pass `onUploadProgress` to Axios and render progress through the file manager store.

## Permission Contract
Permission config lives in `config/file_manager.php`.

Current abilities:
- `read`
- `upload`
- `create_folder`
- `rename`
- `move`
- `delete`
- `force_delete`
- `track_usage`
- `forget_usage`
- `maintenance`

The permission service checks:
- Spatie-style methods like `hasAnyPermission()` or `hasPermissionTo()` when available.
- Laravel gates only when a matching gate is defined.
- Config defaults when no permission package or gate exists.
- Folder-level `media_folders.permission_overrides` after the global permission is resolved.

Folder permission overrides:
- Stored as JSON on `media_folders.permission_overrides`.
- Supported keys match the ability names, for example `{"upload": false, "delete": false}`.
- Overrides inherit from parent folder to child folder.
- A child folder can explicitly override a parent setting.
- Upload/create checks use the nearest existing parent folder when the requested nested path does not exist yet.
- Overrides only affect database authorization checks; they do not rename, move, create, scan, or delete physical storage paths.
- Maintenance users can update a folder through `PATCH /dashboard/file-manager/folder/permissions`.
- The request accepts `folder_id` or `path`, plus an `overrides` object. Omit a key to inherit it.
- The details panel exposes an operator UI with `Inherit`, `Allow`, and `Deny` selectors for common folder actions.

Frontend receives the server permission snapshot in `window.FileManagerPermissions`, then merges it into `window.FileManagerConfig.permissions`. Vue actions, toolbar buttons, item menus, details-panel actions, upload mode, and drag/drop move use that permission snapshot.

File manager dashboard routes are protected by the scoped `file-manager.auth` middleware. `FILE_MANAGER_ALLOW_GUEST` defaults to `false`, and guest permissions default to denied. For a temporary staging-only guest surface, explicitly set `FILE_MANAGER_ALLOW_GUEST=true` and override only the guest abilities that should be exposed. Keep the configured permission names aligned with the app role/permission system when Spatie permissions or Laravel gates are introduced.

## Module Integration Checklist
- Add `*_url` and `*_path` fields in the module migration.
- Add validation rules for both fields.
- Add fields to model `$fillable`.
- Save both fields in create/update actions.
- Call `FileManagerUsageService::track()` after the owner model has an id.
- Call `FileManagerUsageService::forget()` when replacing or clearing a tracked file.
- Render picker with matching `usageModule`, `usageField`, `ownerType`, and `ownerId`.
- Show `*_url` in index/details UI when useful.

## Current Reference Implementation
- User avatar field:
  - DB fields: `users.avatar_url`, `users.avatar_path`
  - Picker folder: `users/avatars`
  - Editor size: `512x512`
  - Usage module: `user-management`
  - Usage field: `avatar_url`
