# Module: 05 - Provider Interactions & Engagement

Status: NOT STARTED  
Approval: PENDING  
Owner: Claude Code

---

## 1. Module purpose

Handles all user-to-provider engagement mechanisms:
- **Feedback & Reviews**: 1 to 5 star ratings with optional comments, retrieval of provider reviews, editing, and deletion.
- **Provider Likes**: Like / unlike toggling on service provider profiles, and fetching total likes.
- **Saved / Favourite Providers**: Bookmarking / unbookmarking service providers for quick user access, and listing saved profiles.

---

## 2. Scope and dependency inventory

| Type | Files / classes | Responsibility |
|---|---|---|
| Models | `App\Models\Feedback`, `App\Models\Like`, `App\Models\Favourite` | Review records, like toggles, and saved provider bookmarks |
| Routes | `routes/api.php`: `/feedback/*`, `/like/*`, `/save/*` | REST endpoints for reviews, likes, and favorites |
| Controllers | `App\Http\Controllers\FeedbackController`, `App\Http\Controllers\LikeController`, `App\Http\Controllers\FavouriteController` | Interaction orchestration |
| Form Requests | `App\Http\Requests\StoreFeedbackRequest`, `App\Http\Requests\LikeRequest`, `App\Http\Requests\FavouriteRequest` | Input validation |
| Resources | `App\Http\Resources\FeedbackResource`, `App\Http\Resources\LikeResource`, `App\Http\Resources\ShowLikeResource`, `App\Http\Resources\FavouriteResource`, `App\Http\Resources\ShowFavouriteResource` | Response transformation |
| Tests | None | Coverage gap |

---

## 3. Existing behavior contract

- **Create Feedback (`POST /api/feedback/create`)**:
  - Middleware: `auth:api`, `IsUser`.
  - Validates `rating` (`required|in:1,2,3,4,5`), `user_id`, `employee_id`, and optional `comment`.
  - Creates `Feedback` record. Eager loads `user`, `employee`.
  - Returns HTTP 200 with `message => 'Feedback submitted successfully'`, `data => FeedbackResource`.
- **Get Provider Feedback (`GET /api/feedback/getEmployeeFeedback/{user_id}`)**:
  - Middleware: `auth:api`, `IsProvider`.
  - Queries `Feedback::where('employee_id', $id)->get()`. Eager loads `user`, `employee`.
  - If feedback exists, returns HTTP 200 with `FeedbackResource::collection($feedback)`. If empty, returns HTTP 404 with `message => 'No feedback found for this employee'`.
- **Edit Feedback (`POST /api/feedback/edit/{id}`)**:
  - Middleware: `auth:api`, `IsUser`.
  - Inline validation: `comment => string`, `rating => in:1,2,3,4,5`.
  - Updates fields and returns HTTP 200 with `FeedbackResource`. If not found, returns HTTP 404.
- **Delete Feedback (`DELETE /api/feedback/delete/{id}`)**:
  - Middleware: `auth:api`, `IsUser`.
  - Deletes record. Returns HTTP 200 `message => 'feedback deleted successfully'`. If not found, returns HTTP 404.
- **Toggle Like (`POST /api/like/create`)**:
  - Middleware: `auth:api`, `IsUser`.
  - Validates `user_id`, `employee_id`.
  - Checks if like exists:
    - If yes: Deletes like and returns HTTP 200 `message => 'like deleted successfully'`, `data => $like`.
    - If no: Creates like, loads `user`, `employee`, and returns HTTP 200 with `LikeResource`.
- **Show Provider Likes (`GET /api/like/show/{id}`)**:
  - Middleware: `auth:api`, `IsProvider`.
  - Queries `Like::with('user')->where('employee_id', $id)->get()`.
  - If count > 0, returns HTTP 200 with `ShowLikeResource::collection($likes)`. If 0, returns HTTP 404 `no likes for employee`.
- **Toggle Save / Favourite (`POST /api/save/create`)**:
  - Middleware: `auth:api`, `IsUser`.
  - Checks if favourite exists:
    - If yes: Deletes and returns HTTP 200 `Saved profile deleted successfully`.
    - If no: Creates and returns HTTP 200 with `FavouriteResource`.
- **Show Saved Profiles (`GET /api/save/show/{id}`)**:
  - Middleware: `auth:api`, `IsUser`.
  - Queries `Favourite::where('user_id', $id)->get()`. Eager loads `user`, `employee`.
  - Returns HTTP 200 with `ShowFavouriteResource::collection($favourites)`. If none, returns HTTP 404 `No saved found`.

---

## 4. Relationships and dependencies

- **Model Foreign Key Inconsistency**:
  - In `Feedback`, `employee()` belongs to `User::class` on `employee_id`.
  - In `Like` and `Favourite`, `employee()` belongs to `Employee::class` on foreign key `employee_id` referencing `employees.user_id`.
  - `employee_id` in all three tables stores `users.id`.
- **Dependencies on other modules**:
  - Module 01 (Authenticated user).
  - Module 04 (Target provider).
- **Safe implementation order**: Step 5 (After Module 04).

---

## 5. Problems found

- **Correctness risk**:
  - In `LikeController::store` and `FavouriteController::store`:
    `$like = Like::where('user_id', $user_id)->where('employee_id', $employee_id)->get()` uses `->get()` and then `count($like) > 0` instead of `->first()`.
    When deleting, it returns the collection in `data`, whereas on creation it returns a Resource instance. This contract must be maintained.
  - In `FeedbackController::update`: validation is done inline with `$validator = Validator::make(...)` and returns HTTP 401 on validation failure instead of standard 422.
  - In `FeedbackController::getEmployeeFeedback`: route uses `{user_id}` in doc, parameter is `$id` in method, which is queried as `employee_id`.
- **Query / Performance**:
  - `ShowFavouriteResource` accesses nested relations on `employee` without eager loading in `FavouriteController::show`:
    Calls `$this->employee->service->name`, `$this->employee->section->name`, causing an **N+1 query per saved provider**.
- **Testing gap**: Zero automated tests covering ratings, like toggles, or saved bookmarks.

---

## 6. Proposed target structure

| Concern | Current location | Proposed location | Reason |
|---|---|---|---|
| HTTP validation | `StoreFeedbackRequest`, `LikeRequest`, `FavouriteRequest`, inline in update | Form Requests across all operations (add `UpdateFeedbackRequest`) | Remove inline validator |
| Business orchestration | `FeedbackController`, `LikeController`, `FavouriteController` | `App\Services\Interaction\FeedbackService`, `App\Services\Interaction\LikeService`, `App\Services\Interaction\FavouriteService` | Encapsulate toggle and review logic |
| Response formatting | Resources + inline arrays | Retain exact Resources | Strict contract preservation |
| Query optimization | Eager loading in controllers | Service queries | Fix N+1 in saved profiles |

---

## 7. Test plan

- **Characterization tests**:
  - `test_user_can_submit_valid_rating_and_comment()`
  - `test_feedback_fails_when_rating_outside_1_to_5()`
  - `test_provider_can_fetch_their_feedbacks()`
  - `test_user_can_toggle_like_on_provider()`
  - `test_user_can_toggle_save_on_provider()`
  - `test_user_can_fetch_saved_providers_list()`
- **Commands**: `php artisan test --filter InteractionTest`

---

## 8. Query optimization plan

- **Current query in `FavouriteController::show`**:
  ```php
  $favourites = Favourite::where('user_id', $id)->get();
  $favourites->load('user', 'employee');
  ```
- **Observed issue**:
  `ShowFavouriteResource` accesses `$this->employee->service` and `$this->employee->section`. Since `employee.service` and `employee.section` were not eager loaded, an N+1 query fires for every saved provider.
- **Proposed change**:
  Change to `$favourites->load(['user', 'employee.service', 'employee.section'])`.
- **Equivalence guarantee**: Resulting JSON payload contains identical values, but query count drops from $2N + 2$ to $4$ queries.

---

## 9. Implementation checklist

- [ ] User approved module plan.
- [ ] Baseline tests written and passing.
- [ ] Form Request created for feedback update.
- [ ] Eager loading in `FavouriteController::show` added and verified.
- [ ] Full regression tests passing.
- [ ] Completion report updated.

---

## 10. Completion report

Final status: NOT STARTED
