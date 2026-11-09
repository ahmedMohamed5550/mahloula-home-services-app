# Master Refactoring Plan

## Mission

Refactor this legacy Laravel application incrementally. Improve architecture, maintainability, testability, query efficiency, and code quality while preserving the existing business behavior and externally observable contracts.

The backend and Livewire UI are separate workstreams:
- Backend module refactoring is governed by `.claude/skills/legacy-refactor/SKILL.md`.
- Livewire UI/UX refactoring is governed by `.claude/skills/livewire-refactor/SKILL.md` and `plans/livewire/LIVEWIRE_PLAN.md`.

## Non-negotiable constraints

1. Do not intentionally change business rules, calculations, workflows, permissions, validation behavior, side effects, or user-visible outcomes.
2. Preserve routes, HTTP methods, request parameter names, response JSON shape, status codes, pagination behavior, sorting, filtering, and error behavior unless a documented defect is approved for correction.
3. Do not change database schema, migrations, stored data, or production configuration without explicit approval.
4. Do not remove functionality because it appears unused. Prove usage and ask before removal.
5. Do not run destructive commands, migrations, seeders, truncation, or production operations.
6. Do not commit, push, deploy, or amend Git history.
7. Do not refactor multiple backend modules in one implementation batch.
8. When uncertain whether a change preserves behavior, stop and ask rather than guessing.
9. Do not invent requirements or design choices. The approved `DESIGN_BRIEF.md` and user-provided references are the UI source of truth.
10. Keep a clean, reviewable Git diff. Never hide a behavior change inside a “refactor”.

## Phase 0 — Discovery only (no application-code changes)

Inspect the full repository and report:
- PHP and Laravel versions, installed packages, test framework, frontend stack.
- Route inventory, middleware, controllers, Form Requests, Resources, services, repositories (if any), policies, gates, events, listeners, jobs, notifications, commands, scheduled tasks, observers, and helpers.
- Models, relationships, scopes, casts, accessors, mutators, model events, and important query patterns.
- All Livewire components, Blade views, layouts, assets, and frontend libraries. Do not modify them in this phase.
- Existing tests and how to run them.
- Existing architecture conventions and naming patterns.
- External integrations and side effects.
- Potential security, performance, and maintainability concerns.

### Required discovery output

Create:
- `plans/ARCHITECTURE_INVENTORY.md`
- `plans/MODULE_MAP.md`
- `plans/BASELINE_TESTS.md`
- `plans/modules/00-MODULE_INDEX.md`
- One individual plan per discovered business module using `plans/MODULE_TEMPLATE.md`.

Do not assume each Eloquent model is a separate module. Derive module boundaries from actual user workflows, routes, relationships, and dependencies. List shared infrastructure separately (authentication, authorization, uploads, notifications, integrations, etc.) when it is cross-cutting.

For each module, identify:
- Models and relationships.
- Routes/endpoints and controllers.
- Requests, Resources, services, policies, events, jobs, and other dependencies.
- Livewire components/views related to the module (inventory only; UI changes remain in the separate Livewire workstream).
- Existing behavior and business rules that must remain unchanged.
- Current tests and missing coverage.
- Query/performance risks supported by code evidence.
- Refactoring sequence and acceptance criteria.
- Dependencies on other modules and a safe implementation order.

At the end of Phase 0, stop and ask for approval of the module map and design brief.

## Phase 1 — Baseline and characterization tests

Before changing a module:
1. Identify how to run the existing test suite.
2. Run relevant existing tests where the environment allows.
3. Record baseline failures separately; do not attribute pre-existing failures to the refactor.
4. Add characterization/feature tests for critical existing behavior where practical.
5. Capture API contract examples from current implementation/tests.
6. Identify external services and mock them safely in tests.
7. Do not alter production data or rely on production credentials.

If a reliable test cannot be written because the behavior is unclear, document the ambiguity and ask.

## Phase 2 — Backend module implementation

Execute only one approved module at a time.

Recommended order within each module:
1. Confirm module plan and dependencies.
2. Add or update tests that describe current behavior.
3. Extract HTTP validation into Form Requests where applicable, preserving exact rules/messages/authorization behavior.
4. Extract response formatting into API Resources only where compatible with the existing response contract.
5. Move orchestration/business operations into focused services where this improves separation.
6. Keep controllers focused on HTTP concerns.
7. Optimize proven query problems without changing result semantics.
8. Run targeted tests, then broader regression tests.
9. Inspect the complete diff for accidental behavior changes.
10. Update that module plan with completed work, test results, risks, and remaining tasks.
11. Stop and report. Wait for approval before starting the next module.

### Architecture guidance

- Do not create a service, request, or resource for every model by default.
- Use Form Requests for HTTP controller validation. Livewire validation stays in the Livewire workstream and uses idiomatic Livewire patterns.
- Use API Resources only when they preserve the exact existing output contract. For non-API responses, retain the existing response mechanism.
- Preserve transaction boundaries, exception behavior, event dispatching, notifications, logging, and external side effects.
- Prefer dependency injection and focused classes; avoid unnecessary repositories, interfaces, factories, DTOs, or abstractions.
- Follow the installed Laravel version and existing project conventions. Do not use APIs unsupported by the project's version.

## Phase 3 — Query optimization

Only optimize queries with an identifiable problem or measurable opportunity.

Investigate:
- N+1 queries and missing eager loading.
- Repeated queries and queries inside loops.
- Unnecessary columns and over-fetching.
- Inefficient aggregates and existence checks.
- Filtering, sorting, pagination, joins, and subqueries.
- Index opportunities based on actual where/order/join patterns and query plans where available.

For every meaningful optimization, document:
- Before and after query behavior.
- Why the result set remains identical.
- Any assumptions.
- Available query-count, execution-time, or explain-plan evidence.

Do not add caching, alter isolation/locking, change pagination, change ordering, or add indexes without evaluating behavior and obtaining approval where schema changes are required.

## Phase 4 — Separate Livewire UI/UX workstream

Do not mix visual redesign with backend module refactoring.

Follow:
- `plans/livewire/LIVEWIRE_PLAN.md`
- `.claude/skills/livewire-refactor/SKILL.md`
- `DESIGN_BRIEF.md`

Inventory and design approval must happen before UI implementation. Implement page/component groups in small batches. Preserve all actions, permissions, validation, events, filters, sorting, pagination, and loading behavior.

## Phase 5 — Final verification

After all approved modules and the Livewire workstream:
- Run the full available automated test suite.
- Run relevant static analysis/formatting commands already configured by the project.
- Review routes and API contracts for unintended changes.
- Review migrations and schema diff; there should be no unapproved schema changes.
- Review the complete Git diff.
- Report tests that passed, failed, or could not run.
- List unresolved risks and behavior questions.
- Do not claim full coverage or zero behavior changes without evidence.

## Definition of done for each backend module

A module is complete only when:
- Its approved plan is implemented.
- Existing behavior and contracts are preserved.
- Relevant tests pass, or pre-existing/environment failures are clearly identified.
- Query changes are justified and verified.
- No unrelated files or modules were changed.
- The module plan and implementation report are updated.
- The user has reviewed the result before the next module begins.

## First instruction to Claude

Read this file, `DESIGN_BRIEF.md`, `plans/MODULE_TEMPLATE.md`, and both skill files.

Perform **Phase 0 only**. Do not edit application code, do not redesign the UI, and do not start refactoring. Produce the discovery documents and individual module plans, then stop for approval.
