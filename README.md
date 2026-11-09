# Legacy Laravel Refactoring Kit

This kit separates the work into:
- **Backend module plans**: each business module is analyzed, tested, refactored, and verified independently.
- **Shared backend skill**: rules that apply to every backend module.
- **Livewire plan and skill**: a completely separate UI/UX workstream.
- **Design brief**: the source of truth for the UI design you want.

## Suggested project layout

Copy these files into the root of your Laravel repository, preserving the `.claude` folder:

```text
your-project/
├── PLAN.md
├── DESIGN_BRIEF.md
├── plans/
│   ├── MODULE_TEMPLATE.md
│   ├── modules/
│   │   ├── 01-<discovered-module>.md
│   │   ├── 02-<discovered-module>.md
│   │   └── ...
│   └── livewire/
│       └── LIVEWIRE_PLAN.md
└── .claude/
    └── skills/
        ├── legacy-refactor/
        │   └── SKILL.md
        └── livewire-refactor/
            └── SKILL.md
```

## How to start with Claude Code

1. Put the kit in the repository root.
2. Complete `DESIGN_BRIEF.md` or provide Claude with screenshots/reference URLs that represent the target design.
3. Ask Claude to read `PLAN.md` and both skill files.
4. Tell Claude to perform **Phase 0 only** first. It must inspect the codebase and produce the real module map and individual module plans without editing application code.
5. Review the module map and design brief.
6. Approve one backend module at a time. Run the Livewire workstream separately after its design has been approved.

Do not ask Claude to execute every module in one session or one giant change.

## Important distinction

A model is not automatically a module. A module should represent a business capability/use case. Claude must inspect routes, controllers, models, services, policies, jobs, events, and Livewire components to discover the actual boundaries. For example, `Project`, `ProjectLike`, `Comment`, and `Favourite` might belong to one project/community feature, but Claude must verify that from the code before grouping them.
