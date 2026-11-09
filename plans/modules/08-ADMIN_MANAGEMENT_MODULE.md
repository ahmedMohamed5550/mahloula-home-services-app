# Module: 08 - Admin Dashboard & Platform Operations

Status: NOT STARTED  
Approval: PENDING  
Owner: Claude Code

---

## 1. Module purpose

Provides platform-wide administration and operational monitoring:
- Dashboard KPI metrics aggregation (counts of standard users, waiting employees, accepted employees, updated employees, sections, services, and sponsors).
- Administrative user creation.
- User management: listing users with real-time search, viewing individual user profiles, and deleting user accounts.
- Administrative web session management (login, logout, CSRF token regeneration).

---

## 2. Scope and dependency inventory

| Type | Files / classes | Responsibility |
|---|---|---|
| Models | `App\Models\User` (and aggregate queries across `Employee`, `Section`, `Service`, `Sponser`) | User model and system-wide entities |
| Routes | `routes/web.php`: `/`, `/admin/login`, `/admin/logout`, `admin/dashboard`, `admin/create`, `admin/store`, `admin/user/show`, `admin/user/show/{id}`, `admin/user/delete/{id}` | Web admin routing |
| Controllers | `App\Http\Controllers\admin\AdminController` | Admin dashboard metrics, user management, and session control |
| Livewire components | `App\Livewire\SearchUser` | Real-time user search and pagination in admin dashboard |
| Blade views | `resources/views/admin/dashboard.blade.php`, `resources/views/admin/create.blade.php`, `resources/views/admin/user/show.blade.php`, `resources/views/admin/user/showOne.blade.php`, `resources/views/livewire/search-user.blade.php` | Admin UI templates and Livewire component |
| Tests | `tests/Feature/ExampleTest.php` (`GET /` returns 200) | Baseline only |

---

## 3. Existing behavior contract

- **Root Redirect (`GET /`)**:
  - Renders `admin.login` view.
- **Admin Login (`POST /admin/login`)**:
  - Validates `email` (required, email) and `password` (required, min:6).
  - Authenticates via `Auth::attempt(['email' => $request->email, 'password' => $request->password])`.
  - Checks if `Auth::user()->userType == 'admin'`:
    - If yes: Redirects to `route('admin.dashboard')` with `success => 'تم تسجيل الدخول بنجاح'`.
    - If no: Logs out immediately and redirects back with `error => 'You do not have admin access permissions'`.
  - If invalid credentials: redirects back with `error => 'Invalid Email Or Password'`.
- **Admin Dashboard (`GET admin/dashboard`)**:
  - Runs count queries:
    - Users: `User::where('userType', 'user')->count()`
    - Waiting employees: `Employee::where('checkByAdmin', 'waiting')->count()`
    - Accepted employees: `Employee::where('checkByAdmin', 'accepted')->count()`
    - Updated employees: `Employee::where('checkByAdmin', 'updated')->count()`
    - Sections: `Section::count()`
    - Services: `Service::count()`
    - Sponsers: `Sponser::count()`
  - Renders `admin.dashboard` view with the 7 count metrics.
- **Admin User Creation (`GET admin/create` & `POST admin/store`)**:
  - `create`: Renders `admin.create`.
  - `store`: Validates `name`, `email`, `phone`, `password`. Creates user via `User::create($request->all())`. Redirects to `admin.dashboard`.
- **User Management**:
  - `List`: `GET admin/user/show` renders `admin.user.show` which embeds `@livewire('search-user')`.
  - `Show One`: `GET admin/user/show/{id}` finds user via `User::findOrFail($id)` and renders `admin.user.showOne`.
  - `Delete`: `DELETE admin/user/delete/{id}` executes `User::destroy($id)` and redirects back.
- **Admin Logout (`GET /admin/logout`)**:
  - Calls `Auth::guard('web')->logout()`, invalidates session, regenerates CSRF token, redirects to `/`.

---

## 4. Relationships and dependencies

- **Internal dependencies**: Aggregates models from Modules 01, 03, 04, 07.
- **Dependencies on other modules**: Must be implemented after underlying domain models exist.
- **Safe implementation order**: Step 8 (Final backend module).

---

## 5. Problems found

- **CRITICAL SECURITY FLAW — Unprotected Admin Routes**:
  - `routes/web.php` lacks authentication/authorization middleware for all administrative routes:
    `/admin/dashboard`, `admin/create`, `admin/store`, `admin/user/*`, `admin/employee/*`, `section/*`, `service/*`, `sponser/*`, `admin/project/*`.
    Any unauthenticated visitor can access the dashboard and execute destructive operations without logging in.
  - The middleware `App\Http\Middleware\IsAdmin` exists, but:
    1. It is never registered or applied to `web.php` routes.
    2. Its code checks `session('user')`, but `AdminController::login` never sets `session(['user' => ...])`.
- **CRITICAL SECURITY FLAW — Plaintext Password in Admin Creation**:
  - In `admin\AdminController::store`:
    `User::create($request->all());`
    Stores the user's password in **plaintext** without `Hash::make()`.
- **Maintainability & Code Quality**:
  - Method name typo: `deletUser` in `admin\AdminController.php`.
  - Livewire `SearchUser` searches `User::where('userType', 'user')->where('name', 'like', '%' . $this->search . '%')->paginate(10)` without indexing `userType` or `name`.
- **Testing gap**: Zero tests for admin authentication checks, metric calculations, or user deletion.

---

## 6. Proposed target structure

| Concern | Current location | Proposed location | Reason |
|---|---|---|---|
| Route security | Unprotected in `routes/web.php` | Apply web auth + `IsAdmin` middleware group | Close critical vulnerability |
| HTTP validation | Inline in `admin\AdminController` | `AdminLoginRequest`, `StoreAdminRequest` | Extract validation rules |
| Business orchestration | `admin\AdminController` | `App\Services\Admin\DashboardService`, `App\Services\Admin\UserManagementService` | Decouple metrics and user administration |
| Password hashing | Direct `$request->all()` | Hashing in Form Request / Service | Fix plaintext vulnerability |
| Livewire interactions | `SearchUser` | Retain component (Livewire workstream) | Isolate backend refactoring from UI |

---

## 7. Test plan

- **Characterization tests**:
  - `test_unauthenticated_user_cannot_access_admin_dashboard()`
  - `test_non_admin_user_cannot_login_to_admin_panel()`
  - `test_admin_can_login_with_valid_credentials()`
  - `test_dashboard_metrics_calculate_accurate_counts()`
  - `test_admin_can_create_user_with_hashed_password()`
  - `test_admin_can_view_and_delete_user()`
  - `test_admin_logout_invalidates_session()`
- **Commands**: `php artisan test --filter AdminManagementTest`

---

## 8. Query optimization plan

- **Current queries**: 7 separate `count()` queries in `home()`.
- **Observation**:
  `User::where('userType', 'user')->count()`,
  `Employee::where('checkByAdmin', ...)->count()`, etc.
  Total execution time is small, but `checkByAdmin` and `userType` should have indexes in target schema review.
- **Proposed change**: Consolidate employee status counts into a single conditional aggregate query:
  `Employee::selectRaw("SUM(checkByAdmin = 'waiting') as waiting, SUM(checkByAdmin = 'accepted') as accepted, SUM(checkByAdmin = 'updated') as updated")->first()`.
- **Equivalence guarantee**: Exactly identical integer count values returned to the view.

---

## 9. Implementation checklist

- [ ] User approved module plan.
- [ ] Baseline tests written and verified.
- [ ] Security fix: Hashed password in user creation.
- [ ] Security fix: Protect admin routes with authenticated admin middleware.
- [ ] Dashboard metrics service implemented.
- [ ] Full regression suite passing.
- [ ] Completion report updated.

---

## 10. Completion report

Final status: NOT STARTED
