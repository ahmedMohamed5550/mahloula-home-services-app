# Module: 07 - Sponsorship & Advertising

Status: NOT STARTED  
Approval: PENDING  
Owner: Claude Code

---

## 1. Module purpose

Manages commercial sponsor advertisements and promotional banners:
- Banner creation with promotional image upload, title, description, expiration date (`expired_at`), and status (`available`, `expired`).
- Public API endpoint for client applications to fetch active sponsorships.
- Web admin management: listing, detail view, creation, editing, and deletion of sponsor ads.
- Livewire search and pagination across sponsor records in the admin dashboard.

---

## 2. Scope and dependency inventory

| Type | Files / classes | Responsibility |
|---|---|---|
| Models | `App\Models\Sponser` | Sponsor data model (note spelling: `Sponser` in schema/class) |
| Routes | `routes/api.php`: `/sponsers/index`<br>`routes/web.php`: `sponser/All`, `sponser/{id}`, `admin/sponser/create`, `sponser/store`, `sponser/update/{id}`, `sponser/delete/{id}` | Public API endpoint and Web admin CRUD routes |
| Controllers | `App\Http\Controllers\SponserController`, `App\Http\Controllers\admin\SponserController` | Sponsor listing in API and CRUD operations in web admin |
| Livewire components | `App\Livewire\SearchSponser` | Admin panel search table with title & type filtering |
| Blade views | `resources/views/admin/sponser/*`, `resources/views/livewire/search-sponser.blade.php` | Admin sponsor management views and Livewire template |
| Tests | None | Coverage gap |

---

## 3. Existing behavior contract

- **Public Sponsor List (`GET /api/sponsers/index`)**:
  - Middleware: none on route group, doc mentions `bearerAuth`.
  - Queries `Sponser::all()`.
  - If count > 0: returns HTTP 200 `message => 'show all sponsers done'`, `data => $sponser`.
  - If count == 0: returns HTTP 200 `message => 'no sponsers found to show it'`, `data => []` (empty array).
- **Web Admin List (`GET /sponser/All`)**:
  - Returns `admin.sponser.show` view with `$sponsers = Sponser::get()`.
- **Web Admin Detail (`GET /sponser/{id}`)**:
  - Finds by `Sponser::findOrFail($id)` and renders `admin.sponser.showOne`.
- **Web Admin Create (`POST /sponser/store`)**:
  - Validates `title` (required), `desc` (required), `image` (required, file, mimes:jpeg,png,jpg,gif, max:2048), `type` (`in:available,expired`), `expired_at` (date).
  - Uploads image via `$file->store('sponser_images', 'uploads')`.
  - Creates record and redirects to `route('sponser.show')`.
- **Web Admin Update (`POST /sponser/update/{id}`)**:
  - Validates fields (`image` optional).
  - If new image provided, stores to `sponser_images` via `uploads` disk and deletes old image via `Storage::disk('uploads')->delete($old_image)`.
  - Updates record and redirects to `route('sponser.show')`.
- **Web Admin Delete (`DELETE /sponser/delete/{id}`)**:
  - Deletes record and deletes associated image from `uploads` disk. Redirects to `route('sponser.show')`.

---

## 4. Relationships and dependencies

- **Internal dependencies**: None. Model is self-contained.
- **Dependencies on other modules**: None. Downstream module (Module 08 Admin Dashboard) reads aggregate count.
- **Safe implementation order**: Step 7 (Self-contained module).

---

## 5. Problems found

- **Correctness risk**:
  - API endpoint `GET /api/sponsers/index` returns `Sponser::all()` directly as JSON without using an API Resource, exposing raw database columns (`created_at`, `updated_at`, `image` internal path).
  - In `SponserController::index`: Does not filter by `type = 'available'` or `expired_at >= now()`; expired sponsors are currently returned. (Behavior preservation note: Keep existing query contract unless approved).
- **Maintainability**:
  - Spelling discrepancy throughout codebase: model and table named `sponsers` / `Sponser` instead of `sponsors` / `Sponsor`. Model name must remain `Sponser` to avoid breaking schema and routes.
  - In `admin\SponserController`, validation is written inline in methods.
- **Testing gap**: Zero tests for sponsor listing, file uploads, or expiration handling.

---

## 6. Proposed target structure

| Concern | Current location | Proposed location | Reason |
|---|---|---|---|
| HTTP validation | Inline in `admin\SponserController` | `StoreSponserRequest`, `UpdateSponserRequest` | Extract validation rules |
| Business orchestration | `SponserController`, `admin\SponserController` | `App\Services\Sponsorship\SponsorshipService` | Centralize sponsor logic and file lifecycle |
| Response formatting | Raw model return | Preserve exact JSON field mapping | Maintain contract compatibility |
| Livewire interactions | `SearchSponser` | Retain component (Livewire workstream) | Isolate backend refactoring from UI |

---

## 7. Test plan

- **Characterization tests**:
  - `test_can_fetch_all_sponsors_via_api()`
  - `test_empty_sponsors_returns_200_with_message()`
  - `test_admin_can_create_sponsor_with_image()`
  - `test_admin_can_update_sponsor_and_replaces_old_image()`
  - `test_admin_can_delete_sponsor_and_removes_file()`
- **Commands**: `php artisan test --filter SponsorshipTest`

---

## 8. Query optimization plan

- **Current query**: `Sponser::all()` in API and `Sponser::get()` in admin view.
- **Observation**: Table volume is low (commercial sponsors). No N+1 query.
- **Optimization**: Retain query; add index to `(type, expired_at)` if volume grows.

---

## 9. Implementation checklist

- [ ] User approved module plan.
- [ ] Baseline tests written and passing.
- [ ] Form Requests created for web admin actions.
- [ ] Refactored `SponsorshipService` manages banner uploads.
- [ ] Full regression suite passing.
- [ ] Completion report updated.

---

## 10. Completion report

Final status: NOT STARTED
