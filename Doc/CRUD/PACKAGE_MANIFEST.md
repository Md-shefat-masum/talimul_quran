# Package Manifest

## Existing files overwritten by extraction

```text
app/Models/User.php
resources/views/backend/pages/dashboard/dashboard.blade.php
routes/web.php
```

## Main new backend files

```text
app/Models/UserType.php
app/Http/Controllers/User/UserController.php
app/Http/Controllers/User/Services/UserService.php
app/Http/Controllers/User/Actions/*
app/Http/Requests/User/*
routes/userRoutes.php
database/migrations/*user_types*
database/migrations/*management_fields_to_users*
database/seeders/UserTypeSeeder.php
```

## Main new frontend files

```text
resources/views/backend/layout/*
resources/views/backend/pages/users/*
public/assets/backend/js/common/*
public/assets/backend/js/modules/users/*
public/assets/backend/styles/modules/users.css
public/assets/backend/plugins/*
public/assets/backend/images/admin-logo.svg
public/assets/backend/images/admin-logo-mini.svg
public/assets/backend/images/default-avatar.svg
```

## Documentation and tests

```text
INSTALL_USER_MANAGEMENT.md
THIRD_PARTY_NOTICES.md
docs/modules/user-management-reference.md
tests/Feature/UserManagementTest.php
```

## V2 readability refinement

The reusable user form has a scoped contrast update. The following files changed from the first package:

```text
public/assets/backend/styles/modules/users.css
resources/views/backend/pages/users/partials/form.blade.php
resources/views/backend/pages/users/partials/modal.blade.php
INSTALL_USER_MANAGEMENT.md
```

## Teal Brand UI Update

- `public/assets/backend/styles/theme-teal.css` provides reusable teal brand tokens and Bootstrap-compatible primary overrides.
- User Management list, forms and modal use the module-scoped teal UI layer in `public/assets/backend/styles/modules/users.css`.
