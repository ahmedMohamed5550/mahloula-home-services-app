# Module: <MODULE_NAME>

Status: NOT STARTED  
Approval: PENDING  
Owner: Claude Code

## 1. Module purpose

Describe the user-facing business capability. Do not define a module solely because an Eloquent model exists.

## 2. Scope and dependency inventory

| Type | Files / classes | Responsibility |
|---|---|---|
| Models | | |
| Routes | | |
| Controllers | | |
| Form Requests | | |
| Resources / response transformers | | |
| Services / actions | | |
| Policies / gates | | |
| Events / listeners / observers | | |
| Jobs / notifications | | |
| Livewire components (inventory only) | | |
| Blade views (inventory only) | | |
| Tests | | |

## 3. Existing behavior contract

Document current behavior from code and tests:
- User workflows:
- Inputs and validation:
- Authorization and ownership rules:
- State transitions:
- Calculations/business rules:
- Database writes and transaction boundaries:
- Events, notifications, jobs, and external side effects:
- API routes, methods, response structures, status codes:
- Sorting, filtering, pagination:
- Error and exception behavior:

Do not infer undocumented behavior. Mark unknowns and ask.

## 4. Relationships and dependencies

- Internal dependencies:
- Dependencies on other modules:
- Shared infrastructure:
- Potential circular dependencies:
- Safe implementation order:

## 5. Problems found

For every finding include file/class/method evidence and classify it:
- Correctness risk:
- Maintainability:
- Duplication:
- Query/performance:
- Security/authorization:
- Testing gap:

Do not label style preferences as defects.

## 6. Proposed target structure

| Concern | Current location | Proposed location | Reason |
|---|---|---|---|
| HTTP validation | | | |
| Business orchestration | | | |
| Response formatting | | | |
| Query responsibility | | | |
| Authorization | | | |

Only introduce classes that solve a demonstrated problem.

## 7. Test plan

- Existing tests:
- Characterization tests to add:
- Unit tests:
- Feature/API tests:
- Authorization tests:
- Edge cases:
- External dependencies to fake/mock:
- Commands to run:

## 8. Query optimization plan

For each proposed optimization:
- Current query/file/method:
- Observed issue:
- Proposed change:
- Why results remain identical:
- How it will be verified:
- Schema/index change required? If yes, request approval first.

## 9. Implementation checklist

- [ ] User approved this module plan.
- [ ] Baseline tests executed and recorded.
- [ ] Characterization tests added where needed.
- [ ] Form Requests refactored where applicable.
- [ ] Services refactored where justified.
- [ ] Resources/response formatting refactored where compatible.
- [ ] Query optimizations implemented and verified.
- [ ] Unit tests added.
- [ ] Feature/regression tests added.
- [ ] Targeted tests passed or failures explained.
- [ ] Full diff reviewed.
- [ ] No unapproved schema/API/business behavior changes.
- [ ] Report updated.

## 10. Completion report

- Files created:
- Files modified:
- Architecture changes:
- Query changes and evidence:
- Tests run and results:
- Known pre-existing failures:
- Risks / unresolved questions:
- Remaining tasks:

Final status: NOT STARTED / IN PROGRESS / BLOCKED / COMPLETED
