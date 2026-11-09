# Module: 03 - Taxonomy (Sections & Services)

Status: NOT STARTED  
Approval: PENDING  
Owner: Claude Code

---

## 1. Module purpose

Maintains the application's core service taxonomy:
- **Sections**: Top-level departments/categories (e.g. Carpentry, Plumbing, Electrical).
- **Services**: Specific offerings nested under each Section (e.g. Furniture Assembly, Pipe Repair).
- Serves both public read/filter endpoints for the mobile client and administrative CRUD operations in the admin web dashboard.

---

## 2. Scope and dependency inventory

| Type | Files / classes | Responsibility |
|---|---|---|
| Models | `App\Models\Section`, `App\Models\Service` | Data representation and parent-child hierarchy |
| Routes | `routes/api.php`: `/sections/*`, `/services/*`<br>`routes/web.php`: `section/*`, `service/*` | Public RESTful endpoints and Web admin management routes |
| Controllers | `App\Http\Controllers\SectionController`, `App\Http\Controllers\ServiceController`, `App\Http\Controllers\admin\SectionController`, `App\Http\Controllers\admin\ServiceController` | Catalog operations and image uploads across API and Web |
| Form Requests | `App\Http\Requests\SectionRequest`, `App\Http\Requests\ServiceRequest` | Validation for API store requests |
| Resources | `App\Http\Resources\SectionResource`, `App\Http\Resources\ServiceResource` | API output transformation |
| Livewire components | `App\Livewire\SearchSection`, `App\Livewire\SearchService` | Interactive search & table pagination in admin panel |
| Blade views | `resources/views/admin/section/*`, `resources/views/admin/service/*`, `resources/views/livewire/search-section.blade.php`, `resources/views/livewire/search-service.blade.php` | Catalog views and Livewire templates |
| Tests | None | Coverage gap |

---

## 3. Existing behavior contract

- **List All Sections (`GET /api/sections`)**:
  - Eager loads `services`.
  - Returns HTTP 200 with `SectionResource::collection($sections)` if count > 0; returns HTTP 200 with empty array if empty.
- **Show Single Section (`GET /api/sections/show/{id}`)**:
  - Eager loads `services`. Returns HTTP 200 with `SectionResource`. Returns 404 if not found.
- **Create Section (`POST /api/sections/create`)**:
  - Middleware: `auth:api`. Validates via `SectionRequest` (`name`, `desc`, `image`).
  - Stores image in `public_path('sections_image')`, saves `asset('sections_image/' . filename)` in DB.
  - Returns HTTP 200 with `SectionResource`.
- **Update Section (`POST /api/sections/edit/{id}`)**:
  - Middleware: `auth:api`. Validates `$request->all()`. If image updated, unlinks old file and uploads new one. Returns HTTP 200 with `SectionResource`.
- **Delete Section (`DELETE /api/sections/delete/{id}`)**:
  - Middleware: `auth:api`. Deletes record and unlinks image. Returns HTTP 200 `message => 'Section deleted successfully'`.
- **List All Services (`GET /api/services`)**:
  - Returns HTTP 200 with `ServiceResource::collection($services)`.
- **Services by Section (`GET /api/services/show/section/{section_id}`)**:
  - Queries `Service::where('section_id', $section_id)->get()`. Eager loads `section`.
  - If empty, returns HTTP 404 with `message => 'No services found for this section'`.
- **Web Admin CRUD**:
  - Uses `admin\SectionController` and `admin\ServiceController`.
  - In web controllers, images are stored via `Storage::disk('uploads')` and paths stored in DB (not absolute URLs).
  - Validation is executed inline in controller methods (`$request->validate(...)`).

---

## 4. Relationships and dependencies

- **Internal dependencies**: `Section` has many `Service` records (`foreignId('section_id')`).
- **Dependencies on other modules**: None. Downstream modules (`Employee`, `Project`) depend on `Section` and `Service`.
- **Safe implementation order**: Step 3 (Independent foundation).

---

## 5. Problems found

- **Correctness risk**:
  - File storage dichotomy: API controllers save absolute asset URLs in `image` field (`asset('sections_image/...')`), whereas Web Admin controllers save relative storage paths (`sections_image/...`). Clients consuming the DB directly will receive inconsistent URL shapes.
- **Maintainability & Duplication**:
  - Duplicate controllers for API and Admin web with diverging validation and storage logic.
  - Livewire components `SearchSection` and `SearchService` perform repetitive `like %search%` queries without debouncing or index backing.
- **Testing gap**: Zero tests for taxonomy retrieval, cascading relationships, or image handling.

---

## 6. Proposed target structure

| Concern | Current location | Proposed location | Reason |
|---|---|---|---|
| HTTP validation | `SectionRequest`, `ServiceRequest`, inline web validation | `SectionRequest`, `ServiceRequest`, `UpdateSectionRequest`, `UpdateServiceRequest` | Standardize validation across API & Web |
| Business orchestration | Split across 4 controllers | `App\Services\Taxonomy\TaxonomyService` | Unified service handling hierarchy and image operations |
| Response formatting | `SectionResource`, `ServiceResource` | Retain exact resources | Preserve output contract |
| Livewire interactions | `SearchSection`, `SearchService` | Retain components (Livewire workstream) | Isolate backend refactoring from UI |

---

## 7. Test plan

- **Characterization tests**:
  - `test_can_fetch_sections_with_nested_services()`
  - `test_can_fetch_single_section()`
  - `test_can_fetch_services_filtered_by_section()`
  - `test_create_section_validates_required_fields()`
  - `test_delete_section_cascades_properly()`
- **Edge cases**: Section without services, non-existent section ID, updating section without altering image.
- **Commands**: `php artisan test --filter TaxonomyTest`

---

## 8. Query optimization plan

- **Current query**:
  - `Section::with('services')->get()` in `SectionController::index`.
  - `Service::where('name', 'like', '%' . $this->search . '%')->paginate(10)` in Livewire.
- **Observation**:
  - `Section::with('services')` is efficient (2 queries: one for sections, one eager load for services).
  - Missing composite index on `services.section_id` + `services.name` for fast category filtering.
- **Proposed change**: Code-level eager loading preserved; index suggestion documented for schema review.

---

## 9. Implementation checklist

- [ ] User approved module plan.
- [ ] Baseline tests written for API and Web routes.
- [ ] Unified `TaxonomyService` implemented.
- [ ] Storage handling unified behind adapter without changing database output.
- [ ] Full test suite green.
- [ ] Completion report updated.

---

## 10. Completion report

Final status: NOT STARTED
