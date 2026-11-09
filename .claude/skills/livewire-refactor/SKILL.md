---
name: livewire-refactor
description: Independently inventory, redesign, and verify a Laravel Livewire interface according to an explicitly approved design brief while preserving all existing interactions and behavior.
---

# Livewire UI/UX Refactoring Skill

This skill governs frontend/Livewire design work only. It is intentionally separate from backend module refactoring.

## Mandatory reading

Before work, read:
- `DESIGN_BRIEF.md`
- `plans/livewire/LIVEWIRE_PLAN.md`
- `plans/livewire/LIVEWIRE_INVENTORY.md` if it exists
- `plans/livewire/DESIGN_SYSTEM_PROPOSAL.md` if it exists
- Relevant module plans for understanding page behavior

## Approval gate

Do not modify UI files until:
- The design references are accessible.
- The design brief is sufficiently complete.
- A design-system proposal has been presented.
- The user has explicitly approved the design.

If the user says “make it modern” without a reference or further direction, ask focused questions or present a proposal for approval. Do not assume colors, layout, typography, or branding.

## First inspect, then design

Identify the actual installed versions and conventions:
- Laravel and Livewire versions.
- Blade component patterns.
- CSS framework and version.
- JavaScript/build tooling.
- Existing icon and component libraries.
- Current layouts, navigation, and reusable elements.

Do not assume Tailwind, Bootstrap, Alpine, or a specific Livewire version. Inspect package files and source code.

## Preserve behavior

Before redesigning a page, inventory every existing:
- Button, link, form, input, select, checkbox, and upload.
- Livewire action and public property.
- Event/listener and dispatch.
- Validation rule and validation message.
- Filter, search, sorting, pagination, and table action.
- Authorization/visibility rule.
- Modal and confirmation flow.
- Loading, error, success, and empty state.

After redesign, verify each item still works. A visually attractive but nonfunctional control is a defect.

Do not move backend business logic into Blade. Do not change service behavior, API contracts, database schema, permissions, or validation rules under this skill.

## Design implementation

Follow the approved design brief consistently:
- Reuse approved design tokens.
- Use consistent typography, spacing, colors, borders, radii, and shadows.
- Prefer accessible semantic controls and visible focus states.
- Provide responsive desktop/tablet/mobile layouts.
- Preserve RTL and localization requirements when specified.
- Provide loading, empty, error, and success states.
- Use existing assets and libraries where practical.
- Avoid adding dependencies without approval.
- Do not use placeholder content in production UI.
- Do not remove fields or actions merely to simplify the layout.

## Batch workflow

- Work on one page or tightly related page group at a time.
- Keep changes limited to relevant views, components, styles, and assets.
- Do not mix backend refactoring into the UI batch.
- Run available tests and relevant frontend build/lint checks.
- Manually inspect rendered pages when the environment supports it.
- Review the diff for missing controls and accidental logic changes.
- Report exactly what was and was not verified.

## Completion report

For each page batch, report:
- Pages/components changed.
- Design elements applied.
- Existing interactions preserved and checked.
- Responsive states checked.
- Tests/build checks and outcomes.
- Any inaccessible references or unverified behavior.
- Remaining work.

Stop after each batch and wait for approval before proceeding.
