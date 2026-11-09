# Module: 04 - Provider Profiles & Verification

Status: NOT STARTED  
Approval: PENDING  
Owner: Claude Code

---

## 1. Module purpose

Orchestrates the entire service provider / employee domain:
- Provider profile completion and document upload (`nationalId`, SSN card image, live verification photo, company details, phone numbers, social media links).
- Verification state lifecycle (`checkByAdmin`): `waiting` (onboarding submitted), `accepted` (verified by admin), `updated` (admin requested profile changes with reason message stored in `Contact`).
- Provider availability status (`status`): `available` vs `busy`.
- Provider portfolio management (`EmployeeWork`: up to 5 project images and 5 video links).
- Administrative verification notice dispatch (`Contact` model storing update reasons).
- Provider discovery endpoints: search by Section & Service with sorting/pagination, and search by city/location.

---

## 2. Scope and dependency inventory

| Type | Files / classes | Responsibility |
|---|---|---|
| Models | `App\Models\Employee`, `App\Models\EmployeeWork`, `App\Models\Contact` | Core provider profile, portfolio works, and admin feedback messages |
| Routes | `routes/api.php`: `/employee/*`, `/work/*`, `/contact/*`, `/Admin/changeEmployeeStatus/*`<br>`routes/web.php`: `admin/waitingEmployee*`, `admin/acceptEmployee*`, `admin/updateEmployee*`, `admin/accept/employee/*`, `admin/update/employee/*`, `admin/reject/employee/*` | Complete provider API and Web verification endpoints |
| Controllers | `App\Http\Controllers\EmployeeController`, `App\Http\Controllers\EmployeeWorkController`, `App\Http\Controllers\ContactController`, `App\Http\Controllers\AdminController`, `App\Http\Controllers\admin\EmployeeController` | Provider onboarding, verification transitions, portfolio, and search |
| Form Requests | `App\Http\Requests\EmployeeCompletedDataRequest`, `App\Http\Requests\UpdateEmployeeDataRequest`, `App\Http\Requests\UpdateEmployeeProfileRequest` | Input validation for provider profile and onboarding |
| Resources | `App\Http\Resources\EmployeeResource`, `App\Http\Resources\EmployeeProfileResource`, `App\Http\Resources\EmployeeWorkResource`, `App\Http\Resources\ShowEmployeeByLocationResource`, `App\Http\Resources\ShowEmployeeBySectionAndServiceResource` | Detailed API response shaping |
| Livewire components | `App\Livewire\WaitingEmployee`, `App\Livewire\AcceptEmployee`, `App\Livewire\UpdateEmployee` | Admin verification queues with real-time search |
| Blade views | `resources/views/admin/employee/**/*`, `resources/views/livewire/*-employee.blade.php` | Verification UI and detail cards |
| Tests | None | Critical coverage gap |

---

## 3. Existing behavior contract

- **Complete Data / Onboarding (`POST /api/employee/employeeCompleteData`)**:
  - Validates `user_id`, `service_id`, `section_id`, `description`, optional SSN image, live photo, company image, coordinates.
  - Creates `Employee` and creates initial `Location`. Eager loads `user`, `user.locations`, `section`, `service`.
  - Returns HTTP 200 with `EmployeeResource`.
- **Update Provider Data (`POST /api/employee/editEmployeeCompleteData/{id}`)**:
  - Middleware: `auth:api`, `IsProvider`.
  - Finds user by `{id}` where `userType in ('employee', 'company')`. Updates User fields (`name`, `timezone`, `image`), then updates Employee fields.
  - Returns HTTP 200 with `EmployeeResource`.
- **View Provider Profile (`GET /api/employee/employeeProfile/{user_id}`)**:
  - Middleware: `auth:api`.
  - Queries `Employee::with(['user', 'service', 'section', 'user.locations', 'feedbacks', 'likes', 'works', 'favourites'])->where('user_id', $userId)->first()`.
  - Checks whether authenticated user has liked or saved this provider.
  - Returns HTTP 200 with `EmployeeProfileResource`.
- **Show Providers by Section & Service (`GET /api/employee/section/{section_id}/service/{service_id}/{user_type}`)**:
  - Filters `Employee` where `service_id`, `section_id`, and `checkByAdmin = 'accepted'`.
  - Joins/filters users by `userType = $user_type`.
  - Returns paginated `ShowEmployeeBySectionAndServiceResource`.
- **Show Providers by Location (`POST /api/employee/showAllEmployeeBylocation/{city}`)**:
  - Middleware: `auth:api`, `IsUser`.
  - Contains existing runtime error: queries `Location::whereHas('user', function($q) use ($city) { $q->where('city', $city)... })` when `city` is in `locations`, not `users`.
- **Portfolio Management (`POST /api/work/create` & `POST /api/work/edit`)**:
  - Middleware: `auth:api`, `IsProvider`.
  - Accepts arrays: `images` (up to 5), `videos` (up to 5).
  - Pads array with nulls to always create/update exactly 5 records in `employee_works`.
  - Returns HTTP 200 with `EmployeeWorkResource::collection(...)`.
- **Admin Review Workflow**:
  - `Accept`: Sets `checkByAdmin = 'accepted'`.
  - `Update`: Validates `reason`, creates `Contact` record (`content => $request->reason, user_id => $employee->user_id`), sets `checkByAdmin = 'updated'`.
  - `Reject`: Deletes employee record (`$employee->delete()`).
- **Contact Show (`GET /api/contact/show/{id}`)**:
  - Retrieves latest `Contact` record for employee's `user_id`, allowing providers to see admin rejection/update notes.

---

## 4. Relationships and dependencies

- **Foreign key anomaly**: In `EmployeeWork`, `Feedback`, `Like`, `Favourite`, the foreign key `employee_id` stores `users.id`, not `employees.id`.
- **Dependencies on other modules**:
  - Module 01 (User authentication and roles).
  - Module 02 (Location creation during onboarding).
  - Module 03 (Section and Service foreign keys).
- **Safe implementation order**: Step 4 (Depends on Modules 01, 02, 03).

---

## 5. Problems found

- **Correctness risk**:
  - **Fatal SQL Error in `showAllEmployeeByLocation`**:
    ```php
    Location::whereHas('user', function($q) use ($city) {
        $q->where('city', $city) // 'city' does not exist on users table!
    })
    ```
    Causes `Unknown column 'city' in where clause`.
  - In `EmployeeController::showAllEmployeesBySectionIdAndServiceId`:
    `feedbacks` is not eager loaded, but `ShowEmployeeBySectionAndServiceResource` calls `$this->feedbacks->count()` and `$this->feedbacks->avg('rating')`, creating an **N+1 query per provider in the list**.
  - In `EmployeeController::employeeProfile`:
    `feedbacks` is eager loaded without `feedbacks.user`, but `EmployeeProfileResource` loops through feedbacks and accesses `$feedback->user->name`, creating an **N+1 query per feedback**.
- **Security / Authorization**:
  - `routes/api.php` line 74 defines `POST /api/employee/changeCheckByAdmin/{id}` without `auth:api` middleware.
  - In `EmployeeWorkController`, file uploads retain original filename without UUID/hash, allowing file collisions.
- **Maintainability**:
  - Fixed-count array padding (`array_pad(..., 5, ...)`) hardcodes portfolio capacity to exactly 5 items.
  - Duplicate code between root `AdminController` and `EmployeeController` for `changeCheckByAdmin` and `changeEmployeeStatus`.
- **Testing gap**: 0% test coverage across all provider onboarding, portfolio, and verification operations.

---

## 6. Proposed target structure

| Concern | Current location | Proposed location | Reason |
|---|---|---|---|
| HTTP validation | Form Requests + inline in `EmployeeWorkController` | Form Requests across all endpoints (add `StoreEmployeeWorkRequest`) | Consistent validation |
| Business orchestration | `EmployeeController`, `EmployeeWorkController`, `admin\EmployeeController` | `App\Services\Provider\ProviderProfileService`, `App\Services\Provider\VerificationService`, `App\Services\Provider\PortfolioService` | Decouple multi-step onboarding and verification workflows |
| Response formatting | In controllers via Resources | Retain exact Resources | Preserve exact client JSON shape |
| Query responsibility | Controller queries | Dedicated service queries with eager loading | Eliminate N+1 queries |

---

## 7. Test plan

- **Characterization tests**:
  - `test_provider_can_complete_onboarding_data()`
  - `test_provider_can_upload_and_pad_portfolio_works()`
  - `test_provider_profile_eager_loads_metrics_accurately()`
  - `test_admin_can_accept_provider()`
  - `test_admin_can_request_update_and_creates_contact_message()`
  - `test_admin_can_reject_and_delete_provider()`
  - `test_provider_can_read_admin_contact_feedback()`
- **Edge cases**: Missing SSN, invalid rating aggregate calculation, non-provider user calling provider endpoints.
- **Commands**: `php artisan test --filter ProviderTest`

---

## 8. Query optimization plan

1. **Fix `showAllEmployeeByLocation`**:
   - Current: `Location::whereHas('user', fn($q) => $q->where('city', $city)->whereIn('userType', ['employee', 'company']))`
   - Proposed: `Location::where('city', $city)->whereHas('user', fn($q) => $q->whereIn('userType', ['employee', 'company']))`
   - Equivalence: Corrects fatal runtime query error; semantics match intended business logic.
2. **Eliminate N+1 in `showAllEmployeesBySectionIdAndServiceId`**:
   - Current: Eager loads without feedbacks; resource calls `$this->feedbacks->count()` and `$this->feedbacks->avg('rating')`.
   - Proposed: Add `withCount('feedbacks')` and `withAvg('feedbacks', 'rating')` or eager load `feedbacks`.
   - Equivalence: Identical numerical aggregates without N+1 queries.
3. **Eliminate N+1 in `employeeProfile`**:
   - Current: `with(['feedbacks'])`
   - Proposed: `with(['feedbacks.user'])`
   - Equivalence: Eliminates 1 query per feedback comment.

---

## 9. Implementation checklist

- [ ] User approved module plan.
- [ ] Baseline tests written and verified.
- [ ] Fatal query error in `showAllEmployeeByLocation` confirmed and corrected.
- [ ] Eager loading / aggregate optimizations implemented.
- [ ] Refactored services handle verification and portfolio uploads cleanly.
- [ ] Full regression suite passing.
- [ ] Completion report updated.

---

## 10. Completion report

Final status: NOT STARTED
