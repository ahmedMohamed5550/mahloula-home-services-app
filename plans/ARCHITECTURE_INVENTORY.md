# Architecture Inventory

Date of Analysis: 2026-11-09  
Laravel Version: 10.48.25  
PHP Runtime: 8.4.23 (Composer specification: `^8.1`)  
Status: Phase 0 Discovery Complete (Read-Only)

---

## 1. Runtime Environment & Packages

### 1.1 Core Runtime
- **PHP Version**: PHP 8.4.23 CLI. Note: Deprecation warnings are emitted on Carbon v2 methods under PHP 8.4 because Carbon 2 in vendor is pre-PHP 8.4 compatible.
- **Framework**: Laravel 10.48.25.
- **Node / Asset Bundling**: Vite 4.0.0 with `laravel-vite-plugin` 0.7.2 and `axios` 1.1.2.
- **Test Framework**: PHPUnit 10.0 with Mockery 1.4.4 and Faker 1.9.1.

### 1.2 Composer Dependencies
| Package | Version / Constraint | Purpose / Evaluation |
|---|---|---|
| `laravel/framework` | `^10.0` (10.48.25) | Core application framework |
| `livewire/livewire` | `*` (v3.5.18) | Component-driven reactive UI for Admin panel |
| `tymon/jwt-auth` | `^2.1` | Primary JWT authentication provider for API |
| `php-open-source-saver/jwt-auth` | `^2.3` | Duplicate JWT library in `composer.json` (redundant/conflict risk) |
| `laravel/sanctum` | `^3.2` | Included in composer.json and User model uses `HasApiTokens`, but API routes use JWT |
| `laravel/socialite` | `^5.16` | Google OAuth login provider |
| `darkaonline/l5-swagger` | `^8.6` | OpenAPI / Swagger API documentation annotations |
| `guzzlehttp/guzzle` | `^7.2` | HTTP client for external requests |
| `laravel/tinker` | `^2.8` | REPL debugging tool |

---

## 2. Directory Layout & Architectural Patterns

### 2.1 Codebase Structure
The application follows a standard Laravel monolithic directory structure with dual exposure:
1. **RESTful API** (`routes/api.php`): Serves mobile apps or external SPA clients, authenticated primarily via JWT (`auth:api`). Responses are returned as JSON via `ApiResponseTrait` and API Resources.
2. **Blade / Livewire Admin Panel** (`routes/web.php`): An admin dashboard with Blade views, Livewire 3 components, and legacy Bootstrap 5 / Corona admin assets in `public/admin/`.

```text
app/
├── Console/Kernel.php
├── Constants/Constants.php
├── Helpers/Helpers.php
├── Http/
│   ├── Controllers/
│   │   ├── AdminController.php (API/utility)
│   │   ├── ContactController.php
│   │   ├── EmployeeController.php
│   │   ├── EmployeeWorkController.php
│   │   ├── FavouriteController.php
│   │   ├── FeedbackController.php
│   │   ├── LikeController.php
│   │   ├── LocationController.php
│   │   ├── LoginController.php
│   │   ├── ProjectController.php
│   │   ├── SectionController.php
│   │   ├── ServiceController.php
│   │   ├── SponserController.php
│   │   ├── UserController.php
│   │   ├── admin/
│   │   │   ├── AdminController.php (Web admin)
│   │   │   ├── EmployeeController.php (Web admin)
│   │   │   ├── ProjectController.php (Web admin)
│   │   │   ├── SectionController.php (Web admin)
│   │   │   ├── ServiceController.php (Web admin)
│   │   │   └── SponserController.php (Web admin)
│   │   └── Auth/
│   │       └── GoogleController.php
│   ├── Middleware/
│   │   ├── Authenticate.php
│   │   ├── IsAdmin.php
│   │   ├── IsProvider.php
│   │   └── IsUser.php
│   ├── Requests/ (15 Form Requests)
│   └── Resources/ (19 API Resources)
├── Livewire/ (8 Livewire 3 components)
├── Mail/UserRegister.php
├── Models/ (15 Eloquent models)
├── Notifications/CustomResetPasswordNotification.php
├── Providers/
├── Services/Timezone/TimezoneService.php
└── Traits/ApiResponseTrait.php
```

---

## 3. Route & Middleware Inventory

### 3.1 Route Groups
- **API (`routes/api.php`)**:
  - `auth:api` (JWT guard) used across protected endpoints.
  - Role middlewares: `IsUser` (`userType === 'user'`), `IsProvider` (`userType in ('employee', 'company')`).
- **Web (`routes/web.php`)**:
  - Public `/` redirects to `admin.login`.
  - Admin login/logout via `admin\AdminController`.
  - **CRITICAL DEFECT**: Admin routes (`admin/dashboard`, `admin/waitingEmployee`, `admin/acceptEmployee`, `section/*`, `service/*`, `sponser/*`, `admin/user/*`, `admin/project/*`) have **no middleware attached**! The `IsAdmin` middleware exists in `app/Http/Middleware/IsAdmin.php` but is never applied to `routes/web.php`. Furthermore, `IsAdmin` inspects `session('user')`, which is never populated during `AdminController::login()`.

### 3.2 Middleware Analysis
| Middleware | Alias | Logic | Issue / Risk |
|---|---|---|---|
| `Authenticate` | `auth` | Standard Laravel auth | Defaults to configured guard (e.g. `auth:api` for JWT) |
| `IsAdmin` | `IsAdmin` | `session('user')->userType === 'admin'` | Never attached to `web.php`; reads `session('user')` which is never set |
| `IsUser` | `IsUser` | `Auth::user()->userType === 'user'` | Returns JSON `401` with typo `'unauthoraized'` on failure |
| `IsProvider` | `IsProvider` | `in_array(Auth::user()->userType, ['employee', 'company'])` | Returns JSON `401` on failure |

---

## 4. Models and Eloquent Relationships

| Model | Table | Fillable Attributes | Relationships | Key Casts & Traits |
|---|---|---|---|---|
| `User` | `users` | `name`, `email`, `password`, `phone`, `image`, `userType`, `timezone`, `provider`, `provider_id` | `hasMany(Contact)`, `hasOne(Employee)`, `hasMany(Location)`, `hasMany(Comment)` | `HasApiTokens`, `HasFactory`, `Notifiable`, `JWTSubject`, `password => 'hashed'` |
| `Employee` | `employees` | `company_image`, `company_name`, `instagram_link`, `linked_in_link`, `imageSSN`, `livePhoto`, `nationalId`, `description`, `phone_number_2`, `mobile_number_1`, `mobile_number_2`, `fax_number`, `whatsapp_number`, `facebook_link`, `tiktok_link`, `website`, `checkByAdmin`, `status`, `service_id`, `user_id`, `section_id` | `belongsTo(Service)`, `belongsTo(User)`, `belongsTo(Section)`, `hasMany(EmployeeWork, employee_id, user_id)`, `hasMany(Feedback, employee_id, user_id)`, `hasMany(Like, employee_id, user_id)`, `hasMany(Favourite, employee_id, user_id)` | `HasFactory`, `Notifiable`. Note: `employee_id` in foreign models points to `users.id`, not `employees.id` |
| `EmployeeWork` | `employee_works` | `employee_id`, `image_url`, `video_url` | `belongsTo(User, employee_id, id)` | `HasFactory` |
| `Section` | `sections` | `name`, `desc`, `image` | `hasMany(Service)`, `hasMany(Employee)` | `HasFactory` |
| `Service` | `services` | `name`, `desc`, `image`, `section_id` | `belongsTo(Section)`, `hasMany(Employee)` | `HasFactory` |
| `Project` | `projects` | `section_id`, `service_id`, `user_id`, `desc`, `city`, `views`, `is_edited`, `is_liked`, `img`, `video` | `belongsTo(Service)`, `belongsTo(User)`, `belongsTo(Section)`, `hasMany(ProjectLike)`, `hasMany(Comment)` | `HasFactory` |
| `ProjectLike` | `project_likes` | `user_id`, `project_id` | `belongsTo(User)`, `belongsTo(Project)` | `HasFactory` |
| `Comment` | `comments` | `user_id`, `project_id`, `content`, `is_edited` | `belongsTo(Project)`, `belongsTo(User)` | `HasFactory` |
| `Contact` | `contacts` | `content`, `user_id` | `belongsTo(User)` | `HasFactory`. Used as admin notification message to provider |
| `Favourite` | `favourites` | `user_id`, `employee_id` | `belongsTo(Employee, employee_id, user_id)`, `belongsTo(User, user_id, id)` | `HasFactory` |
| `Feedback` | `feedbacks` | `comment`, `rating`, `user_id`, `employee_id` | `belongsTo(User, user_id, id)`, `belongsTo(User, employee_id, id)` | `HasFactory`. Note: points to User directly |
| `Like` | `likes` | `user_id`, `employee_id` | `belongsTo(Employee, employee_id, user_id)`, `belongsTo(User, user_id, id)` | `HasFactory` |
| `Location` | `locations` | `city`, `bitTitle`, `street`, `specialMarque`, `zipCode`, `lat`, `long`, `user_id` | `belongsTo(User)` | `HasFactory` |
| `RefreshToken` | `refresh_tokens` | `user_id`, `token`, `expires_at` | `belongsTo(User)` | `expires_at => 'datetime'` |
| `Sponser` | `sponsers` | `title`, `desc`, `image`, `expired_at`, `type` | None | `dates => ['expired_at']` (Legacy Laravel syntax) |

---

## 5. Cross-Cutting Concerns & Coding Conventions

### 5.1 Response Formatting (`ApiResponseTrait`)
All API controllers use `ApiResponseTrait`:
```php
protected function apiResponse($title, $status = 200, $data = [])
{
    $response['message'] = $title;
    $response['data'] = $data;
    $response['status'] = $status;
    return response($response, $status);
}
```
**Observation**:
- Field names are `message`, `data`, and `status` (integer HTTP code inside payload).
- In some endpoints, errors return status inside payload as boolean (`'status' => 'false'`) or JSON direct from `response()->json(...)`. These nuances form part of the existing observable contract.

### 5.2 File Storage & Asset URL Divergence
Two completely divergent file management schemes exist:
1. **API Uploads**: Upload files using `$request->file(...)->move(public_path('folder_name'), $filename)` and store absolute URL `asset('folder_name/' . $filename)` directly in the database.
2. **Admin Web Uploads**: Upload files using `$request->file(...)->store('folder_name', 'uploads')` where the `uploads` disk points to `public_path('')`, and store the relative path `folder_name/filename` in the database.
3. **Deletions**: Inconsistent use of `unlink(public_path(...))` vs `Storage::disk('uploads')->delete(...)`.

### 5.3 Timezone Management (`TimezoneService`)
The application defines `App\Services\Timezone\TimezoneService`. It converts model timestamps (`created_at`, `updated_at`) from UTC to the authenticated user's timezone (`Auth::user()->timezone`) during API Resource serialization (`ProjectResource`, `EmployeeProfileResource`).

### 5.4 Redundant / Conflicting Code
- `App\Http\Controllers\AdminController` vs `App\Http\Controllers\admin\AdminController`:
  - Root `AdminController` has API methods: `changeEmployeeStatus`, `changeCheckByAdmin`.
  - Nested `admin\AdminController` has Web methods: `login`, `home`, `create`, `store`, `showUser`, `showOne`, `deletUser`, `logout`.
- `Helpers.php` contains dead / copy-pasted code referencing a `patients` app (`generateCacheKey` references `patients_index_...`, `checkPermissions` references `$role->permissions()`).

---

## 6. Security & Vulnerability Findings

1. **Unprotected Web Admin Panel**:
   - `routes/web.php` lines 35–90: All administrative routes (`admin/dashboard`, `admin/waitingEmployee`, `admin/acceptEmployee`, `section/*`, `service/*`, `sponser/*`, `admin/user/*`, `admin/project/*`) lack authentication and authorization middleware.
2. **Plaintext Password Storage in Admin Creation**:
   - In `admin\AdminController::store`, `$request->all()` is passed directly to `User::create()`, storing the admin password without `Hash::make()`.
3. **Unprotected Endpoint in API**:
   - `POST /api/employee/changeCheckByAdmin/{id}` is defined without `auth:api` middleware in line 74 of `routes/api.php` (even though line 80 defines it again with middleware).
4. **Token Leak in Password Reset**:
   - In `routes/api.php` line 29: `GET /reset-password/{token}` returns `response()->json(['token' => $token], 200)` directly.
5. **Predictable File Upload Names**:
   - `$request->file(...)->getClientOriginalName()` is used without randomization in `UserController`, `EmployeeController`, `EmployeeWorkController`, and `ProjectController`. Uploading two files with the same name overwrites existing user files.
