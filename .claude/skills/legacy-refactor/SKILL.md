---
name: legacy-refactor
description: Safely analyze and incrementally refactor a legacy Laravel backend by business module while preserving existing behavior, API contracts, and database semantics.
---

# Legacy Laravel Backend Refactoring Skill

Use this skill whenever analyzing or refactoring backend code in this repository.

## Mandatory reading

Before work, read:
- `PLAN.md`
- The approved module plan under `plans/modules/`
- `plans/MODULE_TEMPLATE.md`
- Relevant architecture inventory and baseline test report

## Operating mode

Always state which mode you are in:
1. Discovery (read-only)
2. Planning (documentation only)
3. Implementation (one approved module only)
4. Verification (tests and diff review)

Never silently move from one mode to another.

## Behavior preservation protocol

Before changing code, trace the complete execution path:
route -> middleware -> controller -> request/validation -> service/action -> model/query -> events/jobs/external integrations -> response.

For Livewire flows, trace the component lifecycle and actions, but do not redesign UI under this skill.

Record behavior that must remain unchanged:
- Validation rules, messages, and authorization.
- Ownership and permission checks.
- Query filters, ordering, pagination, and result shape.
- Transactions, locking, and consistency guarantees.
- Events, listeners, jobs, notifications, and external calls.
- Exception handling, status codes, and response payloads.
- Side effects and state transitions.

If a behavior is unclear, do not “clean it up”. Ask the user.

## Architecture rules

### Form Requests
- Use for HTTP controller validation where it improves separation.
- Preserve rules, messages, attributes, and authorization semantics exactly.
- Do not use HTTP Form Requests as a forced pattern for Livewire component validation.

### Services
- Extract cohesive business operations, not every controller line.
- Keep services focused; split by business capability when needed.
- Use dependency injection.
- Preserve transaction and side-effect boundaries.
- Avoid service classes that merely proxy one model call without a reason.

### API Resources
- Use only when the current API contract can be preserved exactly.
- Preserve field names, null behavior, nested relationships, collection shape, pagination metadata, and conditional fields.
- Do not add a `data` wrapper or rename fields unless already part of the contract.

### Controllers
- Keep HTTP orchestration concise.
- Do not move authorization checks or behavior in a way that changes outcomes.
- Preserve route names, middleware, method signatures where externally relied upon, and response behavior.

### Models
- Preserve relationships, scopes, casts, accessors, mutators, observers, and model events.
- Do not relocate behavior without checking all call sites.

## Query optimization protocol

For each proposed query change:
1. Identify the current query and all callers.
2. Describe the suspected inefficiency with code evidence.
3. Confirm expected result equivalence, including nulls, duplicates, ordering, soft deletes, global scopes, and authorization constraints.
4. Make the smallest safe change.
5. Add or update tests.
6. Compare query count or execution plan when practical.

Never casually change:
- `join` vs relationship semantics.
- `whereHas` / `has` behavior.
- `distinct`, grouping, aggregate, or null handling.
- Ordering or pagination.
- Locking, transactions, isolation, or caching.
- Database schema/indexes.

Ask before schema changes or optimizations that may alter consistency.

## Testing protocol

- Follow the project's existing PHPUnit/Pest setup.
- Add characterization tests before refactoring when coverage is missing.
- Test service behavior with focused unit tests where appropriate.
- Test endpoints and contracts with feature tests.
- Cover validation, authorization, edge cases, and side effects.
- Use factories and fakes already established in the repository.
- Never weaken or delete a test to make the suite green.
- Separate pre-existing failures from regressions.

## Scope control

- Work on exactly one approved business module per implementation batch.
- Do not edit Livewire views or perform UI redesign.
- Do not opportunistically refactor unrelated code.
- Do not run migrations, seeders, destructive commands, or production operations.
- Do not commit, push, deploy, or rewrite Git history.
- Review `git diff` and `git status` before reporting.

## Required completion report

Report:
- Module and scope.
- Files created/modified.
- Behavior-preservation checks.
- Architecture changes and rationale.
- Query changes and evidence.
- Tests executed and exact outcomes.
- Pre-existing failures and blockers.
- Risks and follow-up work.

Stop after the report and wait for approval before the next module.
