# Livewire UI/UX Implementation Batches

Status: PENDING DESIGN APPROVAL (Phase 0 Discovery)  
Owner: Claude Code

Visual redesign must proceed strictly in small, isolated batches after design approval. UI work will remain completely separated from backend module implementation.

---

## Batch 0: Shared Shell & Design Foundation
*Prerequisite: User approval of `DESIGN_BRIEF.md` and `DESIGN_SYSTEM_PROPOSAL.md`.*

- **Scope**:
  - `resources/views/admin/main/main.blade.php` (Sidebar, Navbar, Footer, layout container).
  - Shared CSS tokens in `resources/css/app.css` or dedicated theme partial.
  - Standardized search input partial with debouncing and clear button.
  - Standardized table styles, action badge styles, loading spinners (`wire:loading`), and empty states.
- **Verification**: Ensure all existing views still render without breakage.

---

## Batch 1: Taxonomy Management (Sections & Services)
*Tightly coupled category views with low regression risk.*

- **Components & Views**:
  - `App\Livewire\SearchSection` & `resources/views/livewire/search-section.blade.php`
  - `App\Livewire\SearchService` & `resources/views/livewire/search-service.blade.php`
  - `resources/views/admin/section/create.blade.php` & `edit.blade.php`
  - `resources/views/admin/service/create.blade.php` & `edit.blade.php`
- **Improvements**:
  - Fix fixed `500x300` distorted thumbnails to `48x48` object-fit thumbnails.
  - Implement debounced live search (`wire:model.live.debounce.350ms`).
  - Add loading state and empty state.
  - Standardize edit and delete action buttons.

---

## Batch 2: Provider Verification Queues
*Critical administrative workflow for reviewing service providers.*

- **Components & Views**:
  - `App\Livewire\WaitingEmployee` & `resources/views/livewire/waiting-employee.blade.php`
  - `App\Livewire\AcceptEmployee` & `resources/views/livewire/accept-employee.blade.php`
  - `App\Livewire\UpdateEmployee` & `resources/views/livewire/update-employee.blade.php`
  - `resources/views/admin/employee/waiting/showOne.blade.php`
  - `resources/views/admin/employee/accept/showOne.blade.php`
  - `resources/views/admin/employee/update/showOne.blade.php`
- **Improvements**:
  - Unify the three verification tables with consistent badge indicators (`waiting`, `accepted`, `updated`).
  - Modernize provider verification detail cards (SSN document viewer, live photo modal, reason submission).
  - Ensure status transition buttons (Accept, Reject, Request Update) have responsive layout and confirmation.

---

## Batch 3: User Management & Project Moderation
*User directory and community moderation tables.*

- **Components & Views**:
  - `App\Livewire\SearchUser` & `resources/views/livewire/search-user.blade.php`
  - `resources/views/admin/user/showOne.blade.php`
  - `App\Livewire\SearchProject` & `resources/views/livewire/search-project.blade.php`
  - `resources/views/admin/project/showOne.blade.php`
- **Improvements**:
  - Real-time debounced user filtering.
  - Project showcase card with media previews (images/videos).
  - Confirmation modals for user account deletion and project deletion.

---

## Batch 4: Sponsorships & Dashboard KPI Overview
*Commercial banners and central admin metrics.*

- **Components & Views**:
  - `App\Livewire\SearchSponser` & `resources/views/livewire/search-sponser.blade.php`
  - `resources/views/admin/sponser/create.blade.php`, `edit.blade.php`, `showOne.blade.php`
  - `resources/views/admin/dashboard.blade.php`
- **Improvements**:
  - Dashboard KPI cards: modern typography, status icons, high-contrast metric indicators.
  - Sponsor banner preview card with expiration countdown badge.
  - Sponsor datepicker and type dropdown styling.

---

## Batch Verification Protocol

Before declaring any UI batch complete:
1. Verify every original button, link, and form input is present and functional.
2. Verify Livewire search, sorting, and pagination continue to operate without console JavaScript errors.
3. Verify mobile, tablet, and desktop viewport responsiveness.
4. Verify server-side authorization and actions remain intact.
5. Review `git diff` to ensure zero changes to backend controllers or business logic.
