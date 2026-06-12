# File Manager Stage Notes

## Stage
- Date: 2026-06-12
- Surface: Global backend file manager
- Root tag: `<file-manager/>`
- Frontend: Vue 3 global build, Option API components, Pinia option store
- Backend disk: Laravel filesystem `ftp`
- Public file base URL: `FTP_URL`, for example `https://posftp.bme.com.bd`

## Current Delivery Scope
- Floating launcher fixed at `right: 10px; bottom: 10px`.
- Google Drive-style modal layout with sidebar, toolbar, breadcrumbs, grid/list view, details panel, and footer selection action.
- FTP API foundation: list, preview, upload photo, create folder, delete item.
- Photo upload flow with predefined canvas sizes and custom width/height.
- Canvas-based crop/fit controls: zoom, move X, move Y.
- Vanilla JS bridge:
  - `window.FileManager.open({ target, multiple, size, path, callback })`
  - `[data-file-manager]` triggers
  - selected public URLs are written into text/hidden inputs.
  - raw FTP paths are still available in `data-selected-paths` and the `file-manager:selected` event.

## Component Structure
- `public/assets/backend/js/file_manager/app.js`
- `public/assets/backend/js/file_manager/bridge.js`
- `public/assets/backend/js/file_manager/config.js`
- `public/assets/backend/js/file_manager/api/file-manager-api.js`
- `public/assets/backend/js/file_manager/store/file-manager-store.js`
- `public/assets/backend/js/file_manager/components/*`
- `public/assets/backend/js/file_manager/utils/canvas-editor.js`

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
   - FTP disk config
   - API routes
   - Vue/Pinia modal app
   - vanilla bridge
2. Photo workflow:
   - upload original
   - crop/resize canvas
   - preset sizes
   - custom form-requested sizes
   - selected values resolve to `FTP_URL + /path/to/file`
3. Directory workflow:
   - infinite nested folders
   - breadcrumbs
   - create/rename/delete/move
4. Used tracks:
   - database table for selected paths by module/model/field
   - show usage in details panel
   - prevent unsafe delete when file is in use
5. Advanced image editor:
   - keep current canvas editor as lightweight baseline
   - add Filerobot adapter if a richer editor is needed for annotations/filters
6. Permissions:
   - role-based action visibility
   - per-folder write/delete restrictions
7. Performance:
   - paginated directory API
   - thumbnail cache
   - lazy preview loading
