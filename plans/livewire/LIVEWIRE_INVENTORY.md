# Livewire UI/UX Component & Layout Inventory

Status: Phase 0 Discovery Complete (Read-Only)  
Livewire Version: v3.5.18 (Livewire 3)  
CSS Framework: Bootstrap 5.3.0-alpha1 / Corona Free Admin Dark Template  
Icon Libraries: Material Design Icons (MDI), FontAwesome 6  
Owner: Claude Code

---

## 1. Stack and Tooling Inventory

| Layer | Library / Tool | Details / Asset Location |
|---|---|---|
| **Component Engine** | Livewire 3 | `livewire/livewire: v3.5.18` (uses `wire:input`, `wire:navigate`, `WithPagination`) |
| **CSS Framework** | Bootstrap 5 | Injected via CDN: `https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css` |
| **Theme Stylesheet** | Corona Free Admin | `public/admin/assets/css/style.css` (Dark theme dashboard) |
| **Icons** | MDI & FontAwesome 6 | `public/admin/assets/vendors/mdi/css/materialdesignicons.min.css` and FontAwesome 6 CDN |
| **JavaScript / Plugins** | jQuery, OwlCarousel, VectorMap | Injected in `resources/views/admin/main/main.blade.php` |
| **Build System** | Vite 4.0.0 | `vite.config.js` with `resources/css/app.css` and `resources/js/app.js` (Note: admin views currently load compiled assets from `public/admin` rather than Vite) |

---

## 2. Layout & Shell Architecture

- **Main Layout**: `resources/views/admin/main/main.blade.php`
  - Fixed sidebar: `#sidebar` with collapsible menus, navigation links to Employees (`waiting`, `update`, `accept`), Users, Projects, Sections, Services, and Sponsors.
  - Top navigation bar: Brand logo mini, minimize sidebar toggle, profile dropdown with logout button.
  - Main panel wrapper: `.main-panel` containing `.content-wrapper` where child templates are rendered via `@yield('content')`.
  - Footer: Copyright and branding info.
  - Livewire Directives: `@livewireStyles` in `<head>` and `@livewireScripts` before `</body>`.

---

## 3. Inventoried Livewire 3 Components

All 8 Livewire components in the application share a common architectural pattern: they serve as real-time search filters and paginated tables for the admin dashboard.

| Component Class | Blade View | Host View | Data Queried | Public Properties | Lifecycle / Actions |
|---|---|---|---|---|---|
| `App\Livewire\WaitingEmployee` | `resources/views/livewire/waiting-employee.blade.php` | `admin/employee/waiting/showAll.blade.php` | `Employee::where('checkByAdmin', 'waiting')` | `public $search = ''` | `updateSearch($value)` triggered by `wire:input="updateSearch($event.target.value)"` |
| `App\Livewire\AcceptEmployee` | `resources/views/livewire/accept-employee.blade.php` | `admin/employee/accept/showAll.blade.php` | `Employee::where('checkByAdmin', 'accepted')` | `public $search = ''` | `updateSearch($value)` |
| `App\Livewire\UpdateEmployee` | `resources/views/livewire/update-employee.blade.php` | `admin/employee/update/showAll.blade.php` | `Employee::where('checkByAdmin', 'updated')` | `public $search = ''` | `updateSearch($value)` |
| `App\Livewire\SearchProject` | `resources/views/livewire/search-project.blade.php` | `admin/project/show.blade.php` | `Project` joined with `user`, `section`, `service` | `public $search = ''` | `updateSearch($value)` |
| `App\Livewire\SearchSection` | `resources/views/livewire/search-section.blade.php` | `admin/section/show.blade.php` | `Section::where('name', 'like', ...)` | `public $search = ''` | `updateSearch($value)` |
| `App\Livewire\SearchService` | `resources/views/livewire/search-service.blade.php` | `admin/service/show.blade.php` | `Service::where('name', 'like', ...)` | `public $search = ''` | `updateSearch($value)` |
| `App\Livewire\SearchSponser` | `resources/views/livewire/search-sponser.blade.php` | `admin/sponser/show.blade.php` | `Sponser::where('title', ...)->orWhere('type', ...)` | `public $search = ''` | `updateSearch($value)` |
| `App\Livewire\SearchUser` | `resources/views/livewire/search-user.blade.php` | `admin/user/show.blade.php` | `User::where('userType', 'user')->where('name', ...)` | `public $search = ''` | `updateSearch($value)` |

---

## 4. UI/UX Interaction & Quality Observations

1. **Search Input Mechanism**:
   - Uses inline styles on `<input>`:
     `style="width: 50%; padding: 15px 25px; border-radius: 15px; color: white; background-color: #333;"`
   - Triggered via `wire:input='updateSearch($event.target.value)'` instead of Livewire 3's idiomatic `wire:model.live.debounce.300ms="search"`.
   - Lacks debouncing, triggering a network request on every single keystroke.
2. **Typographical & Text Inconsistencies**:
   - Header text contains typos across views:
     - `"Serch For Employee By Name Or Section Name Or Service Name"` (typo: "Serch")
     - `"Serch For Section By Name"`
     - `"Serch For sponser By Totle Or Type"` (typos: "Serch", "sponser", "Totle")
3. **Table & Image Handling**:
   - In `search-section.blade.php` and `search-service.blade.php`, table cell images are rendered with fixed dimensions `width="500" height="300"`, distorting table layout on desktop and breaking mobile responsiveness.
   - Fallback image placeholder: `asset('not_found/not_found.jpeg')`.
4. **Missing UX States**:
   - **No loading states**: Tables do not show spinners or skeleton loaders when Livewire updates (`wire:loading` is completely absent).
   - **No empty states**: When search returns 0 results, the table renders an empty `<tbody>` without an informative "No records found" message.
   - **No pagination resets**: Searching while on page 2+ does not reset pagination to page 1 (`updatingSearch()` hook missing).
5. **Action Buttons**:
   - Inconsistent button classes across tables: mix of Bootstrap badges (`badge badge-outline-warning`), standard buttons (`btn btn-success`, `btn btn-danger`), and inline styled flex containers.
   - Destructive actions (Delete section, service, user) submit standard HTML forms with `@csrf` and `@method('delete')` instead of confirmation modals.
