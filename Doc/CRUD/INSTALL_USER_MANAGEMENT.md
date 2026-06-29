# User Management Module — Root Extract Package

This ZIP is a root-relative Laravel overlay package. Extract its contents inside the Laravel project root.

## What this package adds

- Modular User Management CRUD
- Thin Controller + Service + single-purpose Actions
- Reusable Blade form component for:
  - standalone create page
  - standalone edit page
  - modal create
  - modal edit
- Shared Axios instance included from the main backend layout
- Axios-powered DataTable requests
- Axios-powered Select2 AJAX transport
- Soft delete protection
- CSV export
- Local vendor assets only: no runtime CDN dependency
- Backend layout partials and local logo/avatar files
- Reference documentation and feature tests

## Files intentionally overwritten

Back up these existing files before extracting if your full project has custom changes:

```text
app/Models/User.php
resources/views/backend/pages/dashboard/dashboard.blade.php
routes/web.php
```

All other package files are new additions in the shared snapshot structure. The existing `DatabaseSeeder.php` is intentionally left untouched.

## Install steps

From the Laravel project root:

```bash
php artisan migrate
php artisan db:seed --class=UserTypeSeeder
php artisan optimize:clear
```

To run the included feature tests:

```bash
php artisan test --filter=UserManagementTest
```

## Production-safe seeding

The package seeds only user-type options. It does not create a default admin account or ship a default password. Create the first administrator through your existing secure onboarding flow.

## Authentication note

User Management routes use the production baseline `auth` middleware. Your full project must have a working login flow. If authentication scaffolding has not been installed yet, add it before accessing `/dashboard/users`.

## Versioned assets

The Blade layout reads `APP_VERSION` for local asset cache busting. Add this to `.env` when needed:

```env
APP_VERSION=1.0.0
```

Increase the value when deploying changed frontend assets.

## Reference URL

```text
/dashboard/users
```

## Form readability update

The reusable user form includes a scoped readability layer in:

```text
public/assets/backend/styles/modules/users.css
```

It improves label contrast, input borders, Select2 readability, modal surface contrast and the modal action area without changing global dashboard form styles.

## Teal brand UI layer

The package now includes a reusable teal admin brand stylesheet:

```text
public/assets/backend/styles/theme-teal.css
```

The User Management listing page, filters, DataTable controls, action buttons, page forms and modal form use the teal UI system. For a fresh deployment, set:

```env
APP_VERSION=1.0.2
```
