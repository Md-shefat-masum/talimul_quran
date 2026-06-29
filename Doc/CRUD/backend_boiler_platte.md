# 🚀 Laravel Monolith (MBC) Production Ready AI Guideline

> **Cursor tracking:** A concise always-on summary lives in `.cursor/rules/laravel-mbc-boilerplate.mdc`. This file remains the full canonical spec.

## 🧭 0. System Definition (NON-NEGOTIABLE)

This is a **Production-Grade Laravel Monolith Application** using:

* Architecture: **MBC Modular Pattern**
* UI: **Bootstrap Admin Dashboard**
* Backend Style: **Service + Action Driven**
* Frontend: **Axios-based AJAX system**
* File System: **FTP-based media storage only**

---

# 🏗️ 1. Architecture Rules (CORE ENGINE)

## 1.1 Mandatory Layer Structure

Every module MUST include:

```
Model
Controller (thin only)
Service (business orchestration, when present)
Action (single responsibility logic, preferred)
Blade Views
Routes
```

### 🚨 Strict Rules:

* Controller = ONLY request handling
* Service = business orchestration
* Action = ONE task only (no mixing logic)
* Model = DB only
* View = Bootstrap UI only

---

## 1.2 Module Isolation Rule

In this repo, “modules” are **domain folders** under `app/Http/Controllers/` + a matching route file under `routes/`.

```
app/Http/Controllers/{Domain}/...
routes/{domain}Routes.php
```

Domain name MUST match (as much as possible):

* Route name
* Controller namespace/folder
* Action namespace/folder
* View folder (under `resources/views/backend/...`)

### Canonical examples from this codebase

```
app/Http/Controllers/Analytics/DashboardAnalyticsv2Controller.php
app/Http/Controllers/Analytics/Actions/*.php
routes/analyticsRoutes.php
```

```
app/Http/Controllers/Report/Actions/*.php
routes/reportRoutes.php
```

> Note: Some legacy models also live under domain folders like `app/Http/Controllers/Account/Models/*`. Prefer `app/Models/*` for new models unless the existing domain already uses a local `Models/` folder and you must stay consistent.

---

# 📦 2. Routing System

## 2.1 Resource Routes (CRUD)

Use Laravel Resource Routes:

```php
Route::resource('users', UserController::class);
```

## 2.2 Module Route System

Routes are split:

```
routes/authRoutes.php
routes/dashboardRoutes.php
routes/inventoryRoutes.php
routes/accountRoutes.php
routes/analyticsRoutes.php
routes/reportRoutes.php
...
```

### Rule:

* `routes/web.php` is the loader and `require`s the domain route files.
* Prefer **no business logic in `routes/web.php`** (keep it to loading route files).
* If legacy closures exist, do not copy that pattern into new work.

---

## 2.3 Dual Route Behavior (CRITICAL)

Every list/index page must have an Axios JSON data source. In this codebase there are **two accepted patterns**.

### Pattern A (legacy): Same route, dual response

- Normal request: returns Blade view
- Ajax request (`$request->ajax()` / `expectsJson()`): returns JSON

### Pattern B (common): Separate JSON endpoint

- `index()` returns Blade view
- `list(Request $request)` returns JSON (paginated list + extra analytics if needed)

```text
One page = Blade view + JSON endpoint powering the table/cards
```

### JSON response conventions (match existing code)

- Many endpoints return: `{'success': true|false, 'message'?: string, 'data'?: mixed}`
- Many list endpoints return: `{'data': <Laravel paginator object>, ...}`
- Validation failures commonly use **422** with `{'success': false, 'errors': ...}` or `{'success': false, 'message': ...}`

---

# ⚡ 3. Frontend System (Axios First)

## 3.1 Rules

* All dynamic actions MUST use Axios
* No direct form submission (except fallback)
* CSRF token mandatory for:
  POST, PUT, PATCH, DELETE

---

## 3.2 Data Table System

All list pages MUST include:

* Search
* Pagination
* Sorting
* Column visibility
* Export

Data flow:

```
Blade Load → Axios fetch data → Render table
```

---

# 🧾 4. Form System (CRUD STANDARD)

* One reusable form for Create + Update

### Logic:

```
Create → ID = null
Edit → ID exists
```

---

# 🎨 5. UI SYSTEM (BOOTSTRAP ONLY)

## 5.1 UI Framework

* Bootstrap ONLY (no Tailwind / heavy UI libs)
* Minimal custom CSS (LESS only)

---

## 5.2 Allowed Components

* Grid system
* Cards
* Tables
* Modals
* Buttons
* Forms
* Dropdowns
* Alerts

---

## 5.3 UI Philosophy

* Clean admin panel
* Functional > Fancy
* Mobile-first responsive
* Fast rendering

---

# 📁 6. ASSET ARCHITECTURE (STRICT)

All dashboard assets MUST be here:

```
public/assets/dashboard/
```

Structure:

```
css/
js/
plugins/
images/
fonts/
```

### Rules:

* No scattered assets
* No inline JS/CSS
* Plugins must be local (NO CDN)

---

# 🖼️ 7. FILE MANAGEMENT SYSTEM (ADVANCED)

## 7.1 Storage Rule

**Rule in this codebase**: media is stored via Laravel disks and URLs are resolved via `get_file_url()`.

The project supports multiple disks (`public`, `s3`, `ftp`). For new work:

- Prefer **FTP** for production media when configured.
- Do not hardcode URLs; store a **relative `path`** and render as: `get_file_url() . '/' . $path`
- Use `Storage::disk($disk)` where `$disk` is:
  - explicitly passed (`public|s3|ftp`) for media manager flows, or
  - derived from `config('filesystems.default')` for general upload flows.

Tenant note:

- `get_file_url()` uses `config('app.file_url', config('app.app_url', url('/')))`
- tenants may override `config('app.file_url')` dynamically

---

## 7.2 File Manager Features

* Upload
* Browse folders
* Search
* Preview
* Modal-based picker

---

## 7.3 Image Editor (MANDATORY)

Client-side editing BEFORE upload:

* Crop
* Resize
* Rotate
* Zoom

Output formats:

* JPG
* PNG
* WEBP

---

## 7.4 Server Processing

Use:
👉 Intervention Image (Laravel)

Server responsibilities:

* Validation
* Compression
* Format conversion
* Final optimization
* FTP upload

---

## 7.5 Upload Flow

```
Client Editor → Axios Upload → Laravel → Intervention → FTP → DB record
```

---

## 7.6 Media Tracking System

Track:

* Usage count
* Module usage
* File references
* Unused detection

---

# ⚡ 8. PERFORMANCE SYSTEM

## 8.1 Service Worker

* Cache static assets
* Improve repeat load speed

---

## 8.2 API Cache (5 sec)

* Cache responses for 5 seconds
* Auto refresh after TTL

---

## 8.3 Versioning System

ENV:

```
APP_VERSION=1.0
```

Rule:

* Version change = full cache refresh

---

# 🧠 9. DATABASE RULES

* No heavy joins in migrations
* Relations defined in Models only
* Every column MUST have:

  * Comment
  * Meaning

---

# 🧩 10. DROPDOWN SYSTEM (AJAX SELECT)

* Lazy load (max 10 items)
* Infinite scroll support
* Search via API

---

# 🔐 11. SECURITY & MIDDLEWARE

This repo uses route-group middleware for access control.

### Mandatory baseline

- `auth`

### Common admin middleware stack (existing)

- `CheckUserType` (route-level permission allowlist for `user_type == 2`)
- `DemoMode` (blocks POST changes for demo user when `DEMO_MODE=true`)

### Rule (for new work)

- Put admin features under a group like: `['auth', 'CheckUserType', 'DemoMode']` when it matches nearby routes.
- Do not invent a new permission system; integrate with `CheckUserType` route-permission mapping.

---

# 🧼 12. CODE QUALITY RULES

* No logic in controllers
* No duplicated code
* Each Action = ONE responsibility
* Code must be beginner readable

---

# 🐞 13. DEBUGGING SYSTEM

Must track:

* Query count
* Execution time
* Payload size

---

# 🔄 14. ROUTE ORGANIZATION

* One module = one route file
* web.php = only loader

---

# 📊 15. DEFAULT SYSTEM BEHAVIOR

## Every system MUST:

* Use Axios
* Use Bootstrap UI
* Use Service + Action pattern
* Be modular
* Be reusable

---

# 🚨 16. FINAL AI / CODEX INSTRUCTION (MOST IMPORTANT)

This section is written to “teach” Codex/Claude how you build production-grade Laravel in this repo.

## 16.1 AI Role (What you are)

You are a **production Laravel engineer** working inside an **existing monolith**.

### Non-negotiables

- **Do not invent architecture**: follow this guideline + existing project patterns.
- **Prefer consistency over cleverness**: match naming, folders, and coding style already used.
- **Controller stays thin**: request/response + validation + calling Service/Action only.
- **Action stays single-purpose**: one task, one output.
- **Service orchestrates**: combines multiple Actions / repositories / side effects.

## 16.2 How to start any feature (AI workflow)

Before writing code, do these in order:

- **Locate the module**: determine the module folder/namespace and route file.
- **List endpoints + UI pages**: what Blade pages exist, what JSON endpoints power them.
- **Define inputs/outputs**: request params, JSON schema, Blade variables, validations.
- **Choose the layers**:
  - Controller methods needed
  - Service methods needed
  - Actions needed (small + reusable)
  - Model queries / scopes / relationships

Then generate code with:

- **Stable naming**: same “ModuleName / FeatureName” across route/controller/service/action/view.
- **No duplication**: shared logic must be Action or Service (never copy/paste into controllers).

## 16.3 Dual Route Contract (View + JSON)

Every list/index page route must support:

- **Normal request**: returns a Blade page (HTML)
- **Axios request**: returns JSON payload for the same page

Implementation rules:

- Detect axios/json via `request()->expectsJson()` / `request()->ajax()`
- Keep JSON shape stable: `{ data, meta }` (pagination/search/sort lives in `meta`)

## 16.4 Output Quality Bar (Production-grade)

When generating code, you MUST ensure:

- **Validation**: Form Requests or validated input in Controller (no silent acceptance)
- **Authorization**: middleware / policies / permission checks per module
- **Query performance**: avoid N+1; use eager loading; keep list queries indexed
- **Transactions**: wrap multi-write workflows in DB transactions
- **Error handling**: return consistent JSON errors for axios flows
- **Idempotency**: avoid double inserts on retries where relevant
- **Readability**: junior-readable code; clear method names; small functions

## 16.5 Hard Must / Must Not (Quick rules)

### MUST FOLLOW

- Module-based structure
- Service + Action separation
- Axios for all requests
- Dual route (View + JSON)
- Bootstrap UI only
- FTP file system only
- No business logic in controllers
- Local plugins (NO CDN)

### MUST NOT

- Mix business logic in controller
- Put logic in routes
- Use inline CSS/JS
- Introduce new UI frameworks
- Bypass module isolation
- Break JSON response shape used by existing axios tables

## 16.6 “If you don’t know, assume this”

- **CRUD**: index/create/store/edit/update/destroy via resource routes
- **Tables**: search + pagination + sorting via axios JSON
- **Forms**: one Blade partial reused by create/edit
- **Assets**: only in `public/assets/dashboard/` (LESS for custom styles)

## 16.7 Prompt Template (copy/paste to Codex/Claude)

Use this as the first message when starting a new task with an AI:

```text
You are working on a Production-Grade Laravel Monolith that follows:
- Module isolation
- Thin controllers
- Service + Action architecture
- Dual routes: Blade view + JSON (axios)
- Bootstrap UI only, no CDN plugins, minimal LESS
- FTP-only media storage

Rules:
- Match existing naming, folders, and conventions.
- Do not put business logic in controllers or routes.
- Keep Actions single responsibility.
- Return JSON for axios tables with stable { data, meta } shape.

Task: <describe the feature>
Files likely involved: routes/modules/<module>.php, Controller, Service, Actions, Blade views, assets.
```

---

# 🐳 17. Docker Setup (FAST LOCAL TESTING)

This is a **fast dev/test** container setup for this monolith. Keep production deployment separate.

## 17.1 What you get

- **nginx**: serves the app
- **php-fpm**: runs Laravel
- **mysql**: database
- **redis**: cache/queue
- **mailpit** (optional): local mail inbox UI

## 17.2 Files to add (recommended)

Create these files in the repo (paths are suggestions; adjust if you already have a standard):

- `docker-compose.yml`
- `docker/php/Dockerfile`
- `docker/nginx/default.conf`

## 17.3 `docker-compose.yml` (example)

```yaml
services:
  app:
    build:
      context: .
      dockerfile: docker/php/Dockerfile
    working_dir: /var/www/html
    volumes:
      - ./:/var/www/html
    environment:
      APP_ENV: local
      PHP_OPCACHE_VALIDATE_TIMESTAMPS: "1"
    depends_on:
      - mysql
      - redis

  nginx:
    image: nginx:alpine
    ports:
      - "8080:80"
    volumes:
      - ./:/var/www/html
      - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf:ro
    depends_on:
      - app

  mysql:
    image: mysql:8.0
    ports:
      - "33060:3306"
    environment:
      MYSQL_DATABASE: dashboard
      MYSQL_USER: dashboard
      MYSQL_PASSWORD: dashboard
      MYSQL_ROOT_PASSWORD: root
    volumes:
      - mysql_data:/var/lib/mysql

  redis:
    image: redis:7-alpine
    ports:
      - "63790:6379"

  mailpit:
    image: axllent/mailpit:latest
    ports:
      - "8025:8025"
      - "1025:1025"

volumes:
  mysql_data:
```

## 17.4 `docker/php/Dockerfile` (example)

```dockerfile
FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
    libzip-dev libonig-dev libxml2-dev \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install pdo_mysql mbstring zip exif pcntl gd \
  && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
```

## 17.5 `docker/nginx/default.conf` (example)

```nginx
server {
  listen 80;
  server_name _;
  root /var/www/html/public;

  index index.php index.html;

  location / {
    try_files $uri $uri/ /index.php?$query_string;
  }

  location ~ \.php$ {
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    fastcgi_pass app:9000;
  }
}
```

## 17.6 Laravel `.env` values for Docker (minimum)

- `APP_URL=http://localhost:8080`
- `DB_HOST=mysql`
- `DB_PORT=3306`
- `DB_DATABASE=dashboard`
- `DB_USERNAME=dashboard`
- `DB_PASSWORD=dashboard`
- `REDIS_HOST=redis`
- (optional) `MAIL_HOST=mailpit`, `MAIL_PORT=1025`

## 17.7 Run commands (fast)

```bash
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

Open:

- App: `http://localhost:8080`
- Mailpit: `http://localhost:8025`

## 17.8 Queue / Scheduler (optional but recommended)

- Queue worker (simple):

```bash
docker compose exec app php artisan queue:work
```

- Scheduler (manual loop for dev):

```bash
docker compose exec app php artisan schedule:work
```

---

# 🎯 FINAL RESULT

This system produces:

* Production-ready Laravel Monolith
* Scalable module-based architecture
* Clean backend separation
* Fast AJAX-driven UI
* Enterprise-grade file system
* Fully AI/Codex compatible development flow

---

