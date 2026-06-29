# User Management Reference Module

## 1. Purpose

This module is the reference CRUD implementation for this Laravel monolith. AI tools and junior developers should inspect this module before creating another management feature.

The module deliberately avoids advanced abstractions. It uses readable Laravel classes and a predictable folder map.

## 2. Folder Map

```text
app/Http/Controllers/User/
├── UserController.php
├── Services/UserService.php
└── Actions/
    ├── BuildUserListQueryAction.php
    ├── GetUserDataTableAction.php
    ├── FindUserAction.php
    ├── CreateUserAction.php
    ├── UpdateUserAction.php
    ├── DeleteUserAction.php
    ├── GetUserTypeOptionsAction.php
    └── ExportUsersCsvAction.php

app/Http/Requests/User/
├── StoreUserRequest.php
└── UpdateUserRequest.php

resources/views/backend/pages/users/
├── index.blade.php
├── create.blade.php
├── edit.blade.php
└── partials/
    ├── form.blade.php
    └── modal.blade.php
```

## 3. Layer Responsibilities

### Controller

`UserController` accepts HTTP requests and returns Blade or JSON responses. It does not contain database filtering or create/update rules.

### Service

`UserService` coordinates Actions. It is the simple entry point used by the Controller.

### Actions

Each Action performs one task. Examples:

- `CreateUserAction`: creates a user
- `UpdateUserAction`: updates a user
- `DeleteUserAction`: soft deletes a user
- `GetUserDataTableAction`: builds a DataTables-compatible response
- `GetUserTypeOptionsAction`: builds a Select2-compatible response

### Form Requests

Create and update validation rules stay in `StoreUserRequest` and `UpdateUserRequest`.

## 4. Reusable Form Rule

There is only one user form markup file:

```text
resources/views/backend/pages/users/partials/form.blade.php
```

It holds the `<form>` element and all user fields. It is included in four contexts:

```text
create page
edit page
create modal
edit modal
```

Do not duplicate the fields in another Blade file. Pass simple variables such as `mode`, `context`, `formId` and `user`.

## 5. Shared Axios Rule

All AJAX requests use the shared Axios instance:

```text
public/assets/backend/js/common/axios-instance.js
```

The main backend layout includes it once. Modules reuse:

```js
window.appAxios
```

Do not create a separate Axios config inside each module.

The shared instance handles:

- base URL
- CSRF token
- JSON accept header
- XMLHttpRequest header
- request timeout
- common session-expiry event

## 6. AJAX Select2 Rule

Database-backed select elements use AJAX Select2. This module demonstrates it with `user_type_id`.

Select2 requests must also use the shared Axios instance. The reusable adapter is:

```text
public/assets/backend/js/common/select2-axios.js
```

The endpoint response shape is:

```json
{
  "results": [
    {"id": 1, "text": "Admin"}
  ],
  "pagination": {
    "more": false
  }
}
```

## 7. AJAX DataTable Rule

Listing pages use a server-side AJAX DataTable. The user list uses the shared Axios instance instead of DataTables' default jQuery AJAX request.

Response shape:

```json
{
  "draw": 1,
  "recordsTotal": 100,
  "recordsFiltered": 25,
  "data": [],
  "summary": {
    "total": 100,
    "active": 80,
    "inactive": 20,
    "filtered": 25
  }
}
```

## 8. Route Map

```text
GET     /dashboard/users
GET     /dashboard/users/data
GET     /dashboard/users/create
POST    /dashboard/users
GET     /dashboard/users/{user}
GET     /dashboard/users/{user}/edit
PUT     /dashboard/users/{user}
DELETE  /dashboard/users/{user}
GET     /dashboard/users/options/user-types
GET     /dashboard/users/export/csv
```

Routes live in:

```text
routes/userRoutes.php
```

`routes/web.php` only loads the domain route file.

## 9. Database Map

### `user_types`

```text
id
name
code
status
created_at
updated_at
```

### additional `users` fields

```text
phone
user_type_id
status
deleted_at
```

## 10. Security Notes

- Admin routes use `auth` middleware.
- Users cannot delete their own logged-in account.
- Passwords are hidden and hashed through the User model cast.
- Delete is a soft delete.
- Sortable DataTable columns are whitelisted.
- Validation accepts only expected fields.

## 11. Creating Another Management Module

Use this checklist:

1. Create a new domain folder under `app/Http/Controllers/{Domain}`.
2. Add one thin Controller.
3. Add one Service as the Controller's entry point.
4. Add small Actions with one responsibility each.
5. Add Form Requests for validation.
6. Add a separate route file under `routes/` and require it from `web.php`.
7. Add one reusable form Blade partial.
8. Use that same form partial for page and modal workflows.
9. Use AJAX Select2 for database-driven select elements.
10. Use a server-side AJAX DataTable for the list page.
11. Send every AJAX request through `window.appAxios`.
12. Keep scripts in `public/assets/backend/js/modules/{module}`.
13. Keep plugin files local. Do not add a CDN include.
14. Keep the code readable for a junior developer.

## 12. Common Mistakes to Avoid

- Do not place query logic in Controllers.
- Do not duplicate form fields in create, edit and modal files.
- Do not create multiple Axios instances.
- Do not use jQuery AJAX for Select2 or DataTables.
- Do not add inline CSS or inline JavaScript.
- Do not permanently delete users unless a separate business rule explicitly requires it.
