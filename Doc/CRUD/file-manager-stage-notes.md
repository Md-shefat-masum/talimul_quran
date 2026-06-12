# File Manager Stage Notes

## Stage
- Date: 2026-06-12
- Surface: Global backend file manager
- Root tag: `<file-manager/>`
- Frontend: Vue 3 global build, Option API components, Pinia option store
- Source of truth: database media tables (`media_folders`, `media`, `media_in_uses`)
- Storage disk: configurable Laravel filesystem disk, default `ftp`
- Public file base URL: `FTP_URL`, for example `https://posftp.bme.com.bd`

## Current Delivery Scope
- Floating launcher fixed at `right: 10px; bottom: 10px`.
- Google Drive-style modal layout with sidebar, toolbar, breadcrumbs, grid/list view, details panel, and footer selection action.
- Database media API foundation: list, preview, upload photo, create folder, rename item, move item, delete item.
- Legacy FTP-directory listing implementation is preserved in `LegacyFtpFileManagerService` as a safe-zone archive, but it is not the active source of truth.
- Photo upload flow with predefined image sizes and custom width/height.
- Filerobot image editor adapter for crop, resize, adjust, filters, annotations, and client-side export before FTP upload.
- Canvas-based crop/fit controls remain as the lightweight fallback when Filerobot is unavailable.
- Reusable item action menu for file/folder cards and details panel:
  - open/select
  - open URL
  - copy URL/path
  - rename
  - move
  - delete
- Used-track foundation:
  - active tracking uses `media_in_uses`
  - legacy `file_manager_usages` migration is retained for compatibility/history
  - usage summary API
  - usage tracking API
  - usage forget API
  - delete guard with `409 Conflict` when a file/folder is tracked
  - optional force delete after explicit confirmation
- Concrete user-management integration:
  - users table has `avatar_url` and `avatar_path`
  - create/edit user forms use the global file manager for a 512 x 512 avatar
  - user table renders the avatar thumbnail when available
  - user avatar selections are tracked as `user-management / avatar_url`
  - users table has `document_urls` and `document_paths` JSON fields
  - create/edit user forms use the reusable multiple picker for documents/photos
  - user document selections are tracked as `user-management / document_urls`
- Move workflow polish:
  - prompt-based move replaced with an in-modal destination dialog
  - destination shortcuts include root, current folder, parent folder, child folders, and recent paths
  - sidebar shows current folder shortcuts and recent paths for faster navigation
- Drag/drop move:
  - file and folder cards are draggable
  - folder cards become drop targets
  - dropping opens the Move dialog with the destination prefilled
  - move still requires the final `Move here` confirmation
- Vanilla JS bridge:
  - `window.FileManager.open({ target, multiple, size, path, callback })`
  - `[data-file-manager]` triggers
  - selected public URLs are written into text/hidden inputs.
  - raw storage paths are still available in `data-selected-paths` and the `file-manager:selected` event.
- Reusable picker contract:
  - shared Blade include for CRUD file/image fields
  - module integration contract documentation
  - create forms suppress usage attrs until an owner id exists
  - multiple/gallery fields support JSON URL/path values and removable preview chips
- Permission gates:
  - config-driven ability map
  - backend guards for every file-manager API action
  - route-level `file-manager.auth` middleware for the file-manager API
  - guests receive `401 Unauthorized` before file-manager actions run
  - per-folder permission overrides for contextual actions
  - frontend action visibility based on server permission snapshot
- Upload polish:
  - upload progress state and UI
  - duplicate filename strategies: ask, auto rename, replace
  - duplicate conflict response with suggested filename
- Performance:
  - server-backed folder pagination
  - server-backed search query
  - load-more result appending
  - lazy image preview hints
  - cached thumbnail derivative endpoint for grid previews
- Import audit:
  - every explicit storage import writes a `media_imports` history row
  - dry-run, completed, failed, and completed-with-errors states are trackable
  - maintenance sidebar shows recent import runs
- Test coverage:
  - file manager pagination/search response
  - duplicate upload conflict strategy
  - thumbnail endpoint derivative generation
  - thumbnail cache maintenance endpoints

## Component Structure
- `app/Models/FileManagerUsage.php`
- `app/Models/Media.php`
- `app/Models/MediaFolder.php`
- `app/Models/MediaInUse.php`
- `app/Models/MediaImport.php`
- `app/Services/FileManager/FileManagerPermissionService.php`
- `app/Services/FileManager/LegacyFtpFileManagerService.php`
- `config/file_manager.php`
- `app/Exceptions/FileManager/DuplicateFileException.php`
- `resources/views/backend/components/file-manager-picker.blade.php`
- `Doc/CRUD/file-manager-integration-contract.md`
- `public/assets/backend/js/file_manager/app.js`
- `public/assets/backend/js/file_manager/bridge.js`
- `public/assets/backend/js/file_manager/picker.js`
- `public/assets/backend/js/file_manager/config.js`
- `public/assets/backend/js/file_manager/api/file-manager-api.js`
- `public/assets/backend/js/file_manager/store/file-manager-store.js`
- `public/assets/backend/js/file_manager/components/item-actions.js`
- `public/assets/backend/js/file_manager/components/*`
- `public/assets/backend/js/file_manager/utils/canvas-editor.js`
- `public/assets/backend/js/file_manager/vendor/filerobot-image-editor.bundle.js`
- `public/assets/backend/js/file_manager/vendor/filerobot-image-editor.bundle.css`

## Reusable Integration Pattern
```html
<input type="hidden" id="hero_image" name="hero_image">
<button
    type="button"
    data-file-manager
    data-file-manager-target="#hero_image"
    data-file-manager-size="1600x600"
>
    Select hero image
</button>
```

Multiple selection:
```html
<input type="hidden" id="gallery_images" name="gallery_images">
<button
    type="button"
    data-file-manager
    data-file-manager-target="#gallery_images"
    data-file-manager-multiple
>
    Select gallery
</button>
```

## Automation Roadmap
1. Foundation:
   - DB media schema
   - storage disk config
   - API routes
   - Vue/Pinia modal app
   - vanilla bridge
2. Photo workflow:
   - upload original
   - crop/resize/editor via Filerobot
   - preset sizes
   - custom form-requested sizes
   - selected values resolve to `FTP_URL + /path/to/file`
3. Directory workflow:
   - infinite nested folders
   - breadcrumbs
   - done: create/rename/delete/move API and UI actions
   - done: folder shortcuts/sidebar recent paths
   - done: move destination dialog
   - done: optional drag/drop move handoff into the Move dialog
4. Used tracks:
   - done: database table for selected media by module/model/field
   - done: show usage in details panel
   - done: prevent unsafe delete when file/folder is in use
   - done: user-management avatar form is wired with usage attributes
5. Advanced image editor:
   - done: Filerobot adapter added for richer CMS image editing
   - keep current canvas editor as lightweight fallback
6. Permissions:
   - role-based action visibility
   - per-folder write/delete restrictions
7. Performance:
   - paginated directory API
   - thumbnail cache
   - lazy preview loading

## Stage Verification
- `node --check` passed for all file manager JS modules outside the generated vendor bundle.
- `npm run build` passed.
- Playwright verified `/dashboard/users` file manager launcher, modal upload panel, sample image selection, Filerobot editor render, Filerobot save dialog, desktop and mobile viewport snapshots.
- Browser console stayed clean after editor render and save-dialog interaction.

## Directory Action Stage Verification
- `php -l` passed for `FileManagerService` and `FileManagerController`.
- `php artisan route:list --path=dashboard/file-manager` shows 7 file manager routes.
- `node --check` passed for file manager JS modules outside the generated vendor bundle.
- `npm run build` passed.
- Playwright verified `/dashboard/users` file manager launcher, modal browser mode, mock nested path breadcrumbs, file/folder cards, card action menu, details panel actions, desktop and mobile viewport snapshots.
- Browser console stayed clean after item action menu render.

## Used-Track Stage Verification
- `php artisan migrate` ran `2026_06_12_000003_create_file_manager_usages_table`.
- `php -l` passed for the new model, exception, usage service, file manager service, and controller.
- `php artisan route:list --path=dashboard/file-manager` shows 10 file manager routes.
- `node --check` passed for file manager JS modules outside the generated vendor bundle.
- `npm run build` passed.
- Direct service guard test created a temporary usage row and confirmed `FileInUseException` before FTP deletion.
- Playwright verified `/dashboard/users` file manager launcher, details panel usage summary loaded from the API, and clean console output.

## User Avatar Integration Verification
- `php artisan migrate` ran `2026_06_12_000004_add_avatar_fields_to_users_table`.
- `php -l` passed for updated user model, requests, create action, and update action.
- `node --check` passed for user module JS.
- `npm run build` passed.
- Temporary backend create-action test confirmed avatar selection creates a `file_manager_usages` row, then cleaned the temporary user and usage row.
- Playwright verified `/dashboard/users/create` avatar picker render, file-manager selection event syncing `avatar_url`, `avatar_path`, display value, and preview image.
- Added missing `public/assets/images/arrow-down.svg` expected by backend CSS and confirmed console clean after reload.

## Move Workflow Polish Verification
- `node --check` passed for all file manager JS modules outside the generated vendor bundle.
- `npm run build` passed.
- `php artisan route:list --path=dashboard/file-manager` still shows 10 file manager routes.
- Playwright verified `/dashboard/users` file manager launcher, sidebar current-folder shortcuts, sidebar recent paths, details-panel Move button, move destination dialog, shortcut selection updating the destination input, mobile viewport layout, and clean console output.

## Drag/Drop Move Verification
- `node --check` passed for all file manager JS modules outside the generated vendor bundle.
- `npm run build` passed.
- Playwright verified `/dashboard/users` file manager launcher, mocked file/folder cards, dragging `hero.jpg` onto `gallery`, Move dialog opening with `cms/media/gallery` prefilled, responsive dialog state, and clean console output.
- The final `Move here` button was not clicked during QA, so no live FTP file was moved.

## Reusable Picker Contract Verification
- Added `resources/views/backend/components/file-manager-picker.blade.php` for module forms.
- Added `Doc/CRUD/file-manager-integration-contract.md` with Blade, data attribute, vanilla JS, API, delete-guard, and module checklist contracts.
- User avatar create/edit form now consumes the shared picker component.
- Create forms do not emit `data-file-manager-usage-*` attributes before an owner id exists; backend create/update actions remain responsible for saved usage tracks.
- `node --check` passed for user module JS and file manager JS modules outside the generated vendor bundle.
- `npm run build` passed.
- `php artisan route:list --path=dashboard/file-manager` still shows 10 file manager routes.
- Playwright verified `/dashboard/users/create` picker render, target binding, no pre-save usage attrs, `file-manager:selected` syncing URL/path/display/preview, and clean console output.

## Permission Gate Verification
- Added `config/file_manager.php` ability configuration for `read`, `upload`, `create_folder`, `rename`, `move`, `delete`, `force_delete`, `track_usage`, and `forget_usage`.
- Added `FileManagerPermissionService` with support for Spatie-style permission methods, defined Laravel gates, and configurable defaults.

## Auth Hardening Verification
- Added scoped `file-manager.auth` middleware alias through Laravel 12 bootstrap middleware registration.
- File manager routes now require an authenticated dashboard user.
- The middleware returns a clean `401 Unauthorized` JSON response for API clients and plain `401` text for direct browser hits, avoiding a hard dependency on a named `login` route.
- `FILE_MANAGER_ALLOW_GUEST` now defaults to `false`.
- Guest permission defaults are locked down for read, upload, create folder, rename, move, delete, usage tracking, and maintenance.
- Temporary staging guest access requires both `FILE_MANAGER_ALLOW_GUEST=true` and deliberate guest ability overrides.
- Existing authenticated users still fall back to the configured default permission matrix when no Spatie permission or Laravel gate is present.
- File manager controller now guards list, preview, usage, upload, folder create, rename, move, delete, force delete, usage track, and usage forget actions.
- Layout exposes `window.FileManagerPermissions`; file manager config/store merge those permissions into the Vue UI.
- Toolbar, upload panel, item action menu, details-panel buttons, drag/drop move, force-delete confirmation, and usage tracking bridge are permission-aware.
- `php -l` passed for the permission service, controller, and config file.
- `node --check` passed for user module JS and file manager JS modules outside the generated vendor bundle.
- `npm run build` passed.
- `php artisan route:list --path=dashboard/file-manager` still shows 10 file manager routes.
- Playwright verified `/dashboard/users` permission snapshot, modal render, full-access Upload/Folder visibility, simulated restricted permissions hiding Upload/Folder and disabling draggable cards, and clean console output.

## Multi-Input Gallery Verification
- Reusable picker component now supports `multiple => true` and `valueFormat => json`.
- Multiple pickers store URL values as JSON arrays and raw FTP path values as JSON arrays.
- Added generic `file_manager/picker.js` to sync picker UI from `file-manager:selected`, initialize existing values, clear all values, and remove individual gallery chips.
- Bridge now supports `data-file-manager-value-format` and passes `valueFormat` through `window.FileManager.open()`.
- Gallery picker UI shows selected count and responsive removable preview chips.
- `node --check` passed for user module JS and file manager JS modules outside the generated vendor bundle.
- `npm run build` passed.
- Playwright verified `/dashboard/users/create` single avatar picker regression, injected gallery picker JSON URL/path sync, selected count display, removable chip behavior, clear-all behavior, and clean console output.

## Upload Progress and Conflict Verification
- Upload request validation now accepts `conflict_strategy` as `rename`, `replace`, or `error`.
- File manager service now detects duplicate target filenames before storing uploads.
- `error` strategy returns `409 Conflict` with existing path and suggested filename metadata.
- `rename` strategy keeps the existing auto-suffix behavior.
- `replace` strategy writes to the requested filename.
- Axios upload calls now pass `onUploadProgress`; Pinia store tracks `uploadProgress` and `uploadConflict`.
- Upload panel now includes duplicate strategy selection, progress bar, duplicate conflict message, "use suggested name", and "replace" actions.
- Sidebar upload entry now respects upload permission.
- `php -l` passed for the duplicate exception, upload request, file manager service, and controller.
- `node --check` passed for user module JS and file manager JS modules outside the generated vendor bundle.
- `npm run build` passed.
- Fake FTP disk test confirmed duplicate `error` strategy returns suggested `hero-2.jpg` without touching live FTP.
- Playwright verified `/dashboard/users` upload panel duplicate strategy control, simulated progress bar, simulated conflict actions, sidebar upload visibility, and clean console output.

## Pagination and Lazy Preview Verification
- `GET /dashboard/file-manager` now accepts `page`, `per_page`, and `q`.
- File manager list responses include `pagination` metadata: page, per-page count, total, shown, has-more, next-page, and query.
- Backend list keeps folders first, sorts folder/file names, filters by basename when `q` is present, and returns plain array items.
- Pinia store now tracks pagination state, supports `loadMore()`, and appends paged results.
- Toolbar search now triggers debounced server-backed reloads instead of filtering the already loaded subset.
- Grid shows result count, active filter text, and a Load more button when more items are available.
- Grid/detail image previews use `loading="lazy"` and `decoding="async"`.
- `php -l` passed for the file manager service and controller.
- `node --check` passed for user module JS and file manager JS modules outside the generated vendor bundle.
- `npm run build` passed.
- Fake FTP disk test confirmed page 1/page 2 pagination and `q=alp` filtering.
- Playwright verified pagination metadata UI, Load more visibility, lazy image attributes, debounced search API options, and clean console output.

## Thumbnail Cache Verification
- Added `GET /dashboard/file-manager/thumbnail` route; file manager route list now shows 11 routes.
- Image file items now include `thumbnail_url` for lightweight grid cards while keeping `preview_url` for details/open preview.
- Thumbnail endpoint uses GD to create bounded JPEG derivatives and caches them in `storage/app/private/file-manager-thumbnails`.
- Thumbnail cache key includes FTP path, remote last-modified timestamp, remote size, and requested dimensions.
- Decode failures fall back to original preview bytes.
- Grid cards now prefer `thumbnail_url` and keep `loading="lazy"` plus `decoding="async"`.
- `php -l` passed for the file manager service and controller.
- `node --check` passed for file manager JS modules outside the generated vendor bundle.
- `npm run build` passed.
- Fake FTP disk test confirmed a 640 x 480 image produced a 120 x 90 cached JPEG thumbnail and list items include `thumbnail_url`.
- Playwright verified grid cards prefer thumbnail source, keep lazy image attributes, and console output stayed clean.

## Regression Test Harness Verification
- Added `tests/Feature/FileManagerTest.php`.
- Test coverage uses `Storage::fake('ftp')`, so no live FTP reads/writes/deletes occur.
- Covered paginated/searchable list responses, `thumbnail_url` presence, duplicate upload `conflict_strategy=error`, suggested filename response, and thumbnail endpoint JPEG derivative generation.
- Added thumbnail cache stats/clear tests and guest denial coverage.
- `php artisan test tests/Feature/FileManagerTest.php` passed with 5 tests and 28 assertions.
- `php -l` passed for the new test file, file manager service, and controller.
- `node --check` passed for file manager JS modules outside the generated vendor bundle.
- `npm run build` passed.

## Thumbnail Cache Maintenance Verification
- Added `maintenance` permission ability; authenticated defaults allow it, guest defaults deny it.
- Added `GET /dashboard/file-manager/maintenance/thumbnail-cache` for cache stats.
- Added `DELETE /dashboard/file-manager/maintenance/thumbnail-cache` to clear cached thumbnail derivatives.
- Stats include file count, bytes, readable byte label, and cache path.
- Route list now shows 13 file manager routes.
- Targeted feature tests confirmed maintenance users can inspect/clear cache and guests receive `403 Forbidden`.
- `php -l` passed for config, service, controller, and file manager tests.
- `node --check` passed for file manager JS modules outside the generated vendor bundle.
- `npm run build` passed.

## Maintenance UI Verification
- Added frontend endpoint config for `thumbnailCache`.
- Added API helpers for reading and clearing thumbnail cache stats.
- Pinia store now tracks `thumbnailCache`, `thumbnailCacheLoading`, and `canMaintenance`.
- File manager `open()` loads thumbnail cache stats only when the maintenance ability is available.
- Sidebar now shows a permission-aware Maintenance panel with thumbnail count, cache size, refresh action, and clear cache action.
- Clear action uses confirmation and calls the maintenance API; guest contexts keep the panel hidden.
- `php artisan test tests/Feature/FileManagerTest.php` passed with 5 tests and 28 assertions.
- `node --check` passed for file manager JS modules outside the generated vendor bundle.
- `npm run build` passed.
- Playwright verified guest-hidden maintenance UI, mocked permitted stats render, refresh/clear call flow, no framework overlay, clean console output, and screenshot evidence at `/private/tmp/file-manager-maintenance-ui.png`.

## Database Media Source Stage Verification
- FTP-first active listing was replaced with database-backed media listing.
- The previous FTP listing service is preserved as `LegacyFtpFileManagerService` for future reuse.
- Added `media_folders`, `media`, and `media_in_uses` migrations based on the BME dashboard media pattern.
- Added `Media`, `MediaFolder`, and `MediaInUse` models.
- Active file-manager list API now reads folders/files from DB rows and no longer scans FTP directories.
- Upload still stores the physical file on the configured storage disk, then creates/updates a `media` row.
- Usage tracking now writes to `media_in_uses`; the old `file_manager_usages` table is retained as legacy history.
- Disabled the ad-hoc `/test` FTP write route in `routes/web.php`.
- `php artisan migrate` ran the three DB media migrations.
- `php -l` passed for the active DB service, legacy FTP service, usage service, and new models.
- `php artisan test tests/Feature/FileManagerTest.php` passed with DB-backed media assertions.

## DB Media ID-Aware UI/API Verification
- File manager API requests now preserve `media_id` and `folder_id` alongside storage paths for rename, move, delete, usage, upload, and folder creation.
- Controller actions accept DB identifiers while keeping path-based compatibility for existing frontend calls.
- Pinia store now tracks `currentFolderId`, exposes `selectedMediaIds`, and uses stable `itemKey()`/`sameItem()` helpers instead of path-only identity.
- Grid selection, active state, drag/drop target state, and details usage refresh now use DB-aware identity.
- Frontend copy changed from FTP-directory language to database media-library language.
- Added regression coverage proving media items can be managed by DB id.
- `php artisan test tests/Feature/FileManagerTest.php` passed with 6 tests and 35 assertions.
- `node --check` passed for file-manager JS modules outside the generated vendor bundle.
- `npm run build` passed.
- Playwright CLI verified `/dashboard/users` file manager modal, DB media copy, `uploads` folder navigation, `currentFolderId = 1`, clean browser console, and mobile modal smoke check.
- Screenshot evidence: `/private/tmp/file-manager-db-media-id-ui.png` and `/private/tmp/file-manager-db-media-id-mobile.png`.

## Storage-Safe DB Organization Verification
- Folder rename updates only `media_folders.name` and `slug`; `saved_name_into_storage`, disk directories, and media storage paths stay untouched.
- Folder create now creates only a DB folder row; it does not create a directory on FTP/main disk.
- Folder move updates only `media_folders.parent_id`; it does not move any physical directory or file.
- File rename updates display metadata (`media.filename`, `slug`) only; `media.path` remains stable.
- File move updates only `media.media_folder_id` and folder trail metadata; `media.path` remains stable.
- Details panel now separates `display_path` from `storage_path` so DB organization and physical storage are not confused.
- Directory usage/delete guard now uses the DB folder subtree (`media_folder_id`) instead of storage-path prefix matching.
- `php artisan test tests/Feature/FileManagerTest.php` passed with 8 tests and 53 assertions.
- `node --check` passed for file-manager JS modules outside the generated vendor bundle.
- `npm run build` passed.

## Storage-Safe UI Polish Verification
- Toolbar now explains that media organization happens in the database while physical storage paths stay stable.
- Sidebar storage note now says `DB organization, stable storage`.
- File cards label folders as `DB media folder` instead of implying physical directories.
- Empty folder state now says `No media in this folder` and reinforces stable storage paths.
- Details panel now shows `Display path`, `Folder key` or `Storage path`, `Database ID`, and a storage-safe note.
- Move dialog now labels the destination as a media folder and explains that moving updates only the DB folder.
- `php artisan test tests/Feature/FileManagerTest.php` passed with 8 tests and 53 assertions.
- `node --check` passed for file-manager JS modules outside the generated vendor bundle.
- `npm run build` passed.
- Browser plugin was blocked by missing runtime file, so Playwright CLI fallback verified `/dashboard/users`, modal open, details note, move dialog note, desktop/mobile viewports, and clean console output.
- Screenshot evidence: `/private/tmp/file-manager-storage-safe-ui.png` and `/private/tmp/file-manager-storage-safe-mobile.png`.

## Storage Import Tooling Verification
- Added `MediaImportService` for explicit, on-demand storage scans that register existing files into DB media rows.
- Normal `/dashboard/file-manager` listing remains database-only and does not scan FTP/storage.
- Added `php artisan file-manager:import-media {path=uploads} {--no-recursive} {--dry-run} {--limit=}`.
- Added `POST /dashboard/file-manager/maintenance/import`, guarded by the `maintenance` ability.
- Import creates missing `media_folders` rows from storage path segments and creates/updates `media` rows with stable `media.path`.
- Import does not move, rename, delete, or create physical storage files/directories.
- Sidebar maintenance panel now includes `Import storage index` and renders created/updated/scanned summary.
- `php artisan test tests/Feature/FileManagerTest.php` passed with 10 tests and 70 assertions.
- `node --check` passed for file-manager JS modules outside the generated vendor bundle.
- `npm run build` passed.
- `php artisan route:list --path=dashboard/file-manager` shows 14 routes.
- `php artisan list file-manager` shows `file-manager:import-media`.
- Browser plugin was blocked, so Playwright CLI fallback verified import summary UI and clean console output.
- Screenshot evidence: `/private/tmp/file-manager-import-media-ui.png`.

## Import Audit Verification
- Added `media_imports` migration and `MediaImport` model.
- `MediaImportService` now records every explicit storage import with disk, root, recursive flag, dry-run flag, limit, status, counters, compact item sample, errors, creator, started time, and finished time.
- Import scan failures are recorded as failed import rows instead of disappearing before a summary is persisted.
- Added `GET /dashboard/file-manager/maintenance/imports` for recent import history, guarded by the maintenance ability.
- Maintenance sidebar now shows recent import rows with created/updated/failed counters and a refresh action.
- Import API responses now include `import_id`, `status`, `started_at`, and `finished_at`.
- `php artisan migrate` ran `2026_06_12_000008_create_media_imports_table`.
- `php artisan route:list --path=dashboard/file-manager` shows 15 routes.
- `php artisan test tests/Feature/FileManagerTest.php` passed with 10 tests and 81 assertions.
- `npm run build` passed.

## Folder Permission Override Verification
- Added `permission_overrides` JSON column to `media_folders`.
- Added model fillable/cast support for folder permission override arrays.
- `FileManagerPermissionService` now accepts an optional folder context and applies inherited folder overrides after global user/guest permission resolution.
- Folder override inheritance walks parent to child; child folders can explicitly override a parent setting.
- Controller read, upload, create folder, rename, move, delete, force delete, usage read, usage tracking, preview, and thumbnail checks now pass folder context where available.
- Upload/create checks use the nearest existing folder context, so a restricted parent cannot be bypassed by sending a not-yet-existing nested path.
- List responses return the effective permission snapshot for the current folder.
- `php artisan migrate` ran `2026_06_12_000009_add_permission_overrides_to_media_folders_table`.
- `php artisan test tests/Feature/FileManagerTest.php` passed with folder override assertions.

## Folder Permission UI Verification
- Added `PATCH /dashboard/file-manager/folder/permissions`, guarded by the maintenance ability.
- Added `FileManagerService::updateFolderPermissionOverrides()` to normalize override keys against configured file-manager abilities and persist only explicit allow/deny values.
- Directory API items now include `permission_overrides`.
- File manager frontend config/API/store now supports saving folder permission overrides.
- Details panel shows a compact folder permission matrix for maintenance users when a directory is selected.
- Matrix supports `Inherit`, `Allow`, and `Deny` for read, upload, create, rename, move, and delete.
- Saving overrides refreshes the current listing and effective permission snapshot.
- Feature tests cover saving overrides, list permission reflection, and clearing back to inherited defaults.

## User Documents Multiple Picker Verification
- Added `document_urls` and `document_paths` JSON fields to users.
- User model now casts document URL/path fields to arrays.
- User create/update validation accepts JSON values from the reusable multiple picker.
- User create/update actions persist document arrays and track selected media rows through `media_in_uses`.
- Update action forgets removed document usage tracks.
- User form now includes the reusable picker in multiple/JSON mode for documents/photos under `users/documents`.
- User modal populate/reset flow syncs document JSON values and usage context.
- Added feature coverage proving multiple selected media paths are saved and tracked.
- `php artisan migrate` ran `2026_06_12_000010_add_document_fields_to_users_table`.
- `php artisan test tests/Feature/UserManagementTest.php --filter=user_documents_can_be_saved_and_tracked_from_multiple_picker` passed.
- `npm run build` passed.

## User Management Auth Baseline Verification
- Added scoped `dashboard.auth` middleware alias for dashboard CRUD modules.
- User management routes now use `dashboard.auth`.
- Guest browser requests redirect to the frontend home route instead of depending on a missing named `login` route.
- Guest JSON requests receive `401 Unauthorized` with a JSON message.
- Added feature coverage for unauthenticated JSON denial.
- `php artisan test tests/Feature/UserManagementTest.php` passed with 8 tests and 23 assertions.
- `php artisan test tests/Feature/FileManagerTest.php` passed after the auth baseline change.
- `php artisan route:list --path=dashboard/users` shows 10 protected user-management routes.

## Next Stage
- Add a central dashboard-auth/login strategy when the real admin login surface is introduced; `dashboard.auth` can then redirect there instead of the frontend home route.
