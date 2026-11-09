# Module: 01 - Authentication & Identity

Status: NOT STARTED  
Approval: PENDING  
Owner: Claude Code

---

## 1. Module purpose

Provides secure user identity and access management for all user roles (`user`, `employee`, `company`, `admin`). This includes:
- User registration with optional profile image upload and role designation.
- Credential authentication (email or phone + password) generating JWT access tokens (TTL: 60 mins) and persistent refresh tokens (TTL: 7 days).
- Token rotation and refresh endpoint.
- Logout and session/token invalidation.
- Password reset request and confirmation with custom email notification.
- Google OAuth 2.0 social login flow.
- Web administrative session login and logout.

---

## 2. Scope and dependency inventory

| Type | Files / classes | Responsibility |
|---|---|---|
| Models | `App\Models\User`, `App\Models\RefreshToken` | User credentials, roles, JWT claims; refresh token persistence |
| Routes | `routes/api.php`: `/register`, `/login`, `/refresh-token`, `/logout`, `/forgot-password`, `/reset-password`, `/reset-password/{token}`, `/auth/google`, `/auth/google/callback`<br>`routes/web.php`: `/admin/login`, `/admin/logout` | API authentication routes and Web admin auth routes |
| Controllers | `App\Http\Controllers\UserController`, `App\Http\Controllers\LoginController`, `App\Http\Controllers\Auth\GoogleController`, `App\Http\Controllers\admin\AdminController` | Authentication orchestration and token lifecycle |
| Form Requests | `App\Http\Requests\UserRequest`, `App\Http\Requests\LoginRequest` | Validation of registration and login requests |
| Resources | `App\Http\Resources\UserResource`, `App\Http\Resources\LoginResource` | Response shaping for registered/authenticated user and tokens |
| Notifications / Mail | `App\Notifications\CustomResetPasswordNotification`, `App\Mail\UserRegister` | Reset link dispatch and welcome email (currently commented out) |
| Views | `resources/views/emails/register.blade.php`, `resources/views/emails/reset-password.blade.php`, `resources/views/admin/login.blade.php` | Email templates and admin login screen |
| Tests | `tests/Feature/ExampleTest.php` (checks `GET /` which renders `admin.login`) | Baseline only |

---

## 3. Existing behavior contract

- **Registration (`POST /api/register`)**:
  - Validates `name`, `email` (unique), `password` (min 8), `phone` (unique, min 10), `userType` (`in:user,admin,employee,company`), `timezone`, and optional `image`.
  - Hashes password using `Hash::make()`.
  - Moves image to `public_path('users_folder')` and stores `asset('users_folder/' . filename)` in DB.
  - Returns HTTP 200 with JSON payload: `message => 'User registered successfully'`, `status => 200`, `data => UserResource`.
- **Login (`POST /api/login`)**:
  - Accepts `email_or_phone` and `password`. Dynamically selects field type via `filter_var(..., FILTER_VALIDATE_EMAIL)`.
  - Authenticates via `Auth::attempt()`.
  - On failure, returns HTTP 401 with `message => 'Unauthorized. Incorrect email or password.'`.
  - On success, generates 7-day refresh token, persists to `refresh_tokens` table via `updateOrCreate(['user_id' => $user->id])`, returns HTTP 200 with `LoginResource` containing `token` and `refresh_token`.
- **Refresh Token (`POST /api/refresh-token`)**:
  - Validates presence of `refresh_token`. If missing, returns HTTP 400.
  - Verifies JWT claim `refresh === true`. If invalid, returns HTTP 401.
  - Rotates both access token and refresh token, updates `refresh_tokens` table, returns HTTP 200 with `LoginResource`.
- **Logout (`GET /api/logout`)**:
  - Requires `auth:api`. Deletes all `RefreshToken` records for authenticated user. Calls `Auth::logout()`. Returns HTTP 200 `message => 'Successfully logged out'`.
- **Password Reset (`POST /api/forgot-password` & `POST /api/reset-password`)**:
  - `forgot-password`: Validates email exists in users table, dispatches `Password::sendResetLink()` which sends `CustomResetPasswordNotification`.
  - `reset-password`: Validates email, token, password (confirmed, min 8), resets user password via `Password::reset()`.

---

## 4. Relationships and dependencies

- **Internal dependencies**: `User` has one `RefreshToken`.
- **Dependencies on other modules**: None. All other modules depend on `User`.
- **Shared infrastructure**: `ApiResponseTrait`, `CustomResetPasswordNotification`, Tymon JWTAuth facade.
- **Safe implementation order**: Must be implemented first (Order 1).

---

## 5. Problems found

- **Correctness risk**:
  - Dual JWT packages in `composer.json` (`tymon/jwt-auth` and `php-open-source-saver/jwt-auth`) create potential service provider ambiguity.
  - In `UserController::register`, registration returns HTTP 200 while Swagger documentation specifies HTTP 201.
  - In `UserController::logout`, route is registered as `Route::get('/logout')` instead of `POST`.
- **Maintainability**:
  - Business logic, token generation, image upload, and transaction handling reside directly within `UserController` and `LoginController`.
- **Security / Authorization**:
  - In `routes/api.php`, `Route::get('/reset-password/{token}')` exposes the reset token back in JSON.
  - Image files uploaded during registration use original unhashed client file names (`getClientOriginalName()`), allowing file collisions.
- **Testing gap**: Zero tests covering login, token refresh, registration, or password reset.

---

## 6. Proposed target structure

| Concern | Current location | Proposed location | Reason |
|---|---|---|---|
| HTTP validation | `UserRequest`, `LoginRequest`, inline in `resetPassword` | `UserRequest`, `LoginRequest`, `ResetPasswordRequest`, `ForgotPasswordRequest` | Centralize and decouple validation rules |
| Business orchestration | `UserController`, `LoginController`, `GoogleController` | `App\Services\Auth\AuthService`, `App\Services\Auth\TokenService` | Isolate token rotation and registration logic |
| Response formatting | In controllers via `apiResponse()` + `LoginResource`, `UserResource` | Retain exact `LoginResource` and `UserResource` | Maintain 100% API contract fidelity |
| Query responsibility | Direct Eloquent calls in controller | Model scopes / Service methods | Improve testability |

---

## 7. Test plan

- **Characterization tests to add**:
  - `test_user_can_register_with_valid_data()`
  - `test_registration_fails_on_duplicate_email_or_phone()`
  - `test_user_can_login_with_email_or_phone()`
  - `test_login_fails_with_invalid_credentials()`
  - `test_refresh_token_rotates_tokens()`
  - `test_refresh_token_fails_with_invalid_token()`
  - `test_logout_deletes_stored_refresh_tokens()`
  - `test_forgot_password_sends_notification()`
  - `test_reset_password_updates_user_password()`
- **External dependencies to fake**: `Mail::fake()`, `Socialite::shouldReceive()`.
- **Commands to run**: `php artisan test --filter AuthTest`

---

## 8. Query optimization plan

- **Current query**: `RefreshToken::where('user_id', $user->id)->delete()` during logout.
- **Observed issue**: Missing index on `refresh_tokens.user_id` could cause full table scans at scale.
- **Proposed change**: Defer schema index addition to approved migration batch; query remains unchanged.
- **Equivalence guarantee**: Semantics remain identical.

---

## 9. Implementation checklist

- [ ] User approved this module plan.
- [ ] Baseline characterization tests written and passing.
- [ ] Form Requests extracted for password reset endpoints.
- [ ] Focused `AuthService` and `TokenService` introduced without changing return signatures.
- [ ] Response payload and status code verified identical to baseline.
- [ ] Regressions checked across full suite.
- [ ] Completion report updated.

---

## 10. Completion report

Final status: NOT STARTED
