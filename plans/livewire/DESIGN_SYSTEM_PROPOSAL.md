# Design System Proposal — Livewire Workstream

Status: PENDING USER APPROVAL (Phase 0 Discovery)  
Governing Document: `DESIGN_BRIEF.md`  
Target Interface: Mahloula Admin Dashboard & Management Console  
Owner: Claude Code

---

## 1. Design Direction & Visual Identity

The existing administrative interface uses the dark-mode **Corona Free Admin** theme (Bootstrap 5.3 + custom SCSS).  
The proposed design system modernizes this aesthetic into a **refined, high-contrast dark dashboard** with clean surfaces, standardized spacing, accessible typography, and smooth micro-interactions, while remaining 100% compatible with existing markup and assets.

---

## 2. Design Tokens Proposal

### 2.1 Color Palette
```css
:root {
  /* Surface & Background */
  --bg-app: #0f1117;          /* Deep dark background */
  --bg-surface: #181b24;      /* Card & panel surface */
  --bg-surface-elevated: #222634; /* Hover & dropdown surface */
  --border-color: #2b3042;    /* Crisp divider & border lines */

  /* Text & Content */
  --text-primary: #f8fafc;    /* High contrast primary text */
  --text-secondary: #94a3b8;  /* Muted labels & descriptions */
  --text-disabled: #64748b;   /* Placeholder & inactive text */

  /* Semantic Status Colors */
  --color-primary: #3b82f6;   /* Royal blue for primary actions */
  --color-success: #10b981;   /* Emerald green for 'accepted' & success */
  --color-warning: #f59e0b;   /* Amber for 'waiting', 'updated', & warnings */
  --color-danger: #ef4444;    /* Crimson for 'rejected', deletions, & errors */
  --color-info: #06b6d4;      /* Cyan for informational badges */
}
```

### 2.2 Typography Scale
- **Font Family**: Modern system sans-serif: `-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif`.
- **Display Headings**:
  - H1 / Dashboard Title: `1.75rem` (`28px`), font-weight: `700`, line-height: `1.2`.
  - H2 / Section Title: `1.25rem` (`20px`), font-weight: `600`, line-height: `1.3`.
  - H3 / Card Title: `1.0rem` (`16px`), font-weight: `600`, line-height: `1.4`.
- **Body & Data**:
  - Body text: `0.875rem` (`14px`), font-weight: `400`.
  - Table cells & metadata: `0.8125rem` (`13px`), font-weight: `400` / `500`.
  - Badges & labels: `0.75rem` (`12px`), font-weight: `600`, uppercase tracking: `0.05em`.

### 2.3 Spacing & Elevation
- **Spacing Scale**: Base unit `4px` (4px, 8px, 12px, 16px, 20px, 24px, 32px).
- **Border Radii**:
  - Small (badges, tags): `6px`.
  - Medium (inputs, buttons): `8px`.
  - Large (cards, modals): `12px`.
- **Card Shadow**: Subtle dark elevation: `0 4px 12px rgba(0, 0, 0, 0.35)`.

---

## 3. Component Design Specifications

### 3.1 Search Controls
- **Current**: Non-debounced input with inline CSS:
  `style="width: 50%; padding: 15px 25px; border-radius: 15px; color: white; background-color: #333;"`
- **Proposed Standard**:
  - Livewire 3 idiomatic binding: `wire:model.live.debounce.350ms="search"`.
  - Clean input group with integrated search icon, clear button (`wire:click="$set('search', '')"`), and active focus ring.
  - Reset pagination on search change via `updatingSearch()` hook.

### 3.2 Tables & Data Grids
- Clean tabular rows with subtle hover highlight (`rgba(255, 255, 255, 0.02)`).
- **Thumbnails**: Replace fixed `500x300` distortions with crisp object-fit thumbnails (`48px x 48px` rounded-md with fallback icon).
- Standardized action buttons (view, edit, delete) using aligned icon-button groups.

### 3.3 State Handlers
- **Loading State**: Add `wire:loading` indicator (subtle pulse / spinner) to provide immediate feedback during search filtering.
- **Empty State**: Friendly graphic and descriptive text ("No matching records found for '{search}'") with a quick "Clear search" action.
- **Delete Confirmation**: Destructive actions (user delete, project delete, provider reject) must trigger a clear confirmation dialog before execution.

---

## 4. Responsive & Mobile Behavior

- **Desktop (>= 992px)**: Fixed collapsible sidebar with expanded table views.
- **Tablet (768px – 991px)**: Auto-collapsed sidebar with overlay drawer; responsive horizontal scrolling on tables.
- **Mobile (< 768px)**:
  - Top bar with hamburger toggle.
  - Search input full width (100%).
  - Tables convert to compact mobile cards or swipeable horizontal tables without data clipping.

---

## 5. Approval Gate

> [!IMPORTANT]
> **Status: PENDING USER APPROVAL**  
> Under the rules of `plans/livewire/LIVEWIRE_PLAN.md` and `.claude/skills/livewire-refactor/SKILL.md`, no UI or Blade file modifications will occur until this proposal and `DESIGN_BRIEF.md` receive explicit approval from the user.
