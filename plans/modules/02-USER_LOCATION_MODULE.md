# Module: 02 - User & Location Management

Status: NOT STARTED  
Approval: PENDING  
Owner: Claude Code

---

## 1. Module purpose

Manages user profile data (name, timezone, avatar image) and multi-address geolocation records (city, street, building title, landmark/specialMarque, zip code, latitude, longitude coordinates).

---

## 2. Scope and dependency inventory

| Type | Files / classes | Responsibility |
|---|---|---|
| Models | `App\Models\User`, `App\Models\Location` | User profile representation and address geolocation entries |
| Routes | `routes/api.php`: `/user/editUserProfile/{id}`, `/user/{user_id}`, `/location/store`, `/location/showUsersLocation/{id}`, `/location/update/{id}`, `/location/destroy/{id}` | Profile view/edit and Location CRUD |
| Controllers | `App\Http\Controllers\UserController`, `App\Http\Controllers\LocationController` | Profile orchestration and location data handling |
| Form Requests | `App\Http\Requests\EditUserProfileRequest`, `App\Http\Requests\StoreLocationRequest` | Input validation for profile edits and location creation |
| Resources | `App\Http\Resources\UserResource`, `App\Http\Resources\UpdateUserProfileResource`, `App\Http\Resources\LocationResource` | Response shaping |
| Tests | None | Critical coverage gap |

---

## 3. Existing behavior contract

- **Edit User Profile (`POST /api/user/editUserProfile/{id}`)**:
  - Middleware: `auth:api`, `IsUser`.
  - Validates `name`, `timezone`, and optional `image` via `EditUserProfileRequest`.
  - If new image provided: deletes existing image on disk (`unlink(public_path('users_folder/' . basename($imageUrl)))`), saves new file, generates `asset('users_folder/' . filename)`.
  - Updates user and returns HTTP 200 with `UpdateUserProfileResource`.
- **View User Profile (`GET /api/user/{user_id}`)**:
  - Middleware: `auth:api`, `IsUser`.
  - Returns HTTP 200 with `UserResource`. If not found, throws 404.
- **Store Location (`POST /api/location/store`)**:
  - Validates fields via `StoreLocationRequest`.
  - Creates `Location` record.
  - Returns HTTP 200 with `message => 'location updated Successfully'`, `data => LocationResource`. (Note: Status message says "updated" even though it is a store operation — this contract must be preserved).
- **Show User's Locations (`GET /api/location/showUsersLocation/{id}`)**:
  - Middleware: `auth:api`.
  - Fetches all locations for user ID with eager-loaded `user`.
  - If locations found, returns HTTP 200 with `LocationResource::collection($locations)`. If none, returns HTTP 404 with `message => 'No location found'`.
- **Update Location (`POST /api/location/update/{id}`)**:
  - Middleware: `auth:api`.
  - Inline validation of `city`, `bitTitle`, `street` (required) and optional coordinates.
  - Updates fields with fallback to existing values (`$request->city ?? $location->city`).
  - Returns HTTP 200 with `LocationResource`. If not found, returns HTTP 404.
- **Destroy Location (`DELETE /api/location/destroy/{id}`)**:
  - Middleware: `auth:api`.
  - Deletes location by ID. Returns HTTP 200 `Deleted Location successfully`.

---

## 4. Relationships and dependencies

- **Internal dependencies**: `User` has many `Location` records (`foreignId('user_id')`).
- **Dependencies on other modules**: Module 01 (Authentication & User model).
- **Safe implementation order**: Step 2 (immediately following Module 01).

---

## 5. Problems found

- **Correctness risk**:
  - In `UserController::editUserProfile`, endpoint parameter `{id}` is not verified to match `Auth::id()`; any user with `IsUser` could attempt to update another user's profile if authorization is missing.
  - In `LocationController::update`, validation is executed inline via `$request->validate()` instead of using a dedicated Form Request.
  - In `LocationController::store`, success message says `'location updated Successfully'` instead of created. Must be preserved for API client backward compatibility.
- **Maintainability**:
  - Avatar file deletion uses direct `unlink()` without defensive file existence checks or storage abstraction.
- **Testing gap**: No feature or unit tests for profile editing or location management.

---

## 6. Proposed target structure

| Concern | Current location | Proposed location | Reason |
|---|---|---|---|
| HTTP validation | `EditUserProfileRequest`, inline in `LocationController::update` | `EditUserProfileRequest`, `StoreLocationRequest`, `UpdateLocationRequest` | Clean separation of HTTP validation |
| Business orchestration | `UserController`, `LocationController` | `App\Services\User\UserProfileService`, `App\Services\Location\LocationService` | Decouple file handling and geolocation updates |
| Response formatting | Inline + Resources | Exact existing Resources | Preserves 100% response shape |
| Authorization | Middleware only | Form Requests / Policies | Verify user owns target profile / location |

---

## 7. Test plan

- **Characterization tests**:
  - `test_user_can_view_own_profile()`
  - `test_user_can_update_profile_and_avatar()`
  - `test_user_can_create_location()`
  - `test_user_can_fetch_all_locations()`
  - `test_user_can_update_existing_location()`
  - `test_user_can_delete_location()`
- **Edge cases**: Missing lat/long, non-existent location ID, unauthorized user attempting to edit another user's profile.
- **Commands**: `php artisan test --filter UserLocationTest`

---

## 8. Query optimization plan

- **Current query**: `Location::where('user_id', $id)->with('user')->get()`.
- **Observation**: Indexed by foreign key `user_id`. Efficient query.
- **Optimization**: Ensure `user_id` has database index in target schema review.

---

## 9. Implementation checklist

- [ ] User approved module plan.
- [ ] Characterization tests implemented.
- [ ] `UpdateLocationRequest` created with exact rules and messages.
- [ ] File operations isolated in service without changing path generation.
- [ ] Tests passed with 100% assertion parity.
- [ ] Completion report updated.

---

## 10. Completion report

Final status: NOT STARTED
