# Separate Livewire UI/UX Refactoring Plan

## Objective

Improve the visual design, usability, responsiveness, and consistency of the existing Livewire application according to the approved `DESIGN_BRIEF.md`, while preserving all current functionality and behavior.

This is a separate workstream. Do not combine UI redesign with backend module refactoring commits or implementation batches.

## Stage 0 — Inventory only

Inspect:
- Livewire version and component conventions.
- All Livewire components and their corresponding Blade views.
- Layouts, navigation, reusable Blade components, partials, and slots.
- CSS framework/version, JS libraries, icon libraries, build tooling, and assets.
- Existing design tokens, colors, typography, spacing, breakpoints.
- Forms, tables, modals, dropdowns, alerts, toasts, loading states, empty states.
- Component actions, events, listeners, validation, pagination, filters, sorting, authorization, and upload behavior.

Create:
- `plans/livewire/LIVEWIRE_INVENTORY.md`
- `plans/livewire/DESIGN_SYSTEM_PROPOSAL.md`
- `plans/livewire/PAGE_IMPLEMENTATION_ORDER.md`

Do not edit UI files in this stage.

## Stage 1 — Design approval

Read `DESIGN_BRIEF.md` and inspect the supplied references.

Propose:
- Color tokens and semantic colors.
- Typography scale.
- Spacing scale.
- Container and grid rules.
- Sidebar/header/navigation behavior.
- Button, input, select, table, card, modal, alert, and badge styles.
- Responsive breakpoints and mobile behavior.
- Loading, empty, error, and success states.
- RTL behavior if required.

If the design brief is incomplete or references are unavailable, ask the user for the missing design direction. Do not guess and implement a full redesign.

Wait for explicit approval before modifying UI files.

## Stage 2 — Shared design foundation

After approval:
- Implement reusable tokens and shared styles using the project's existing stack where possible.
- Improve shared layouts and navigation.
- Create/reuse components only where they reduce duplication.
- Avoid adding a new UI framework unless approved.
- Verify existing pages still render and interactions work.

## Stage 3 — Page/component batches

Group pages by user workflow or layout. Implement one small batch at a time.

For each page:
1. Record existing behavior and actions.
2. Map every existing control and interaction.
3. Apply the approved design.
4. Preserve Livewire state, validation, events, pagination, filters, sorting, uploads, and authorization.
5. Add responsive behavior.
6. Verify loading, empty, error, and success states.
7. Test desktop, tablet, and mobile widths.
8. Review the diff and report.

## Stage 4 — UI regression verification

Verify:
- Every original action still exists.
- No buttons/links became decorative or inert.
- Forms submit the same data and show equivalent validation.
- Modals open/close and destructive actions retain confirmation.
- Filters/search/sort/pagination still work.
- Loading states do not hide or duplicate actions.
- Authorization visibility and server-side authorization remain intact.
- Responsive layouts do not hide essential controls.
- No backend behavior was changed as part of visual work.

## Done criteria

- Approved design brief implemented consistently.
- All inventoried pages addressed or explicitly deferred.
- Existing functionality preserved.
- Responsive states checked.
- Relevant tests and manual checks documented.
- No unapproved backend or schema changes.
