# Business Module Index

Status: TO BE GENERATED DURING PHASE 0

Claude must discover the real business modules from the repository. Do not assume one model equals one module.

| Order | Module | Main models | Main workflows | Dependencies | Risk | Status |
|---:|---|---|---|---|---|---|
| 1 | To be discovered | | | | | Pending |

## Candidate model inventory supplied by the user

These are starting clues only. Verify exact class names, spelling, relationships, and usage in the repository:

- Comment
- Contact
- Employee
- EmployeeWork
- Favourite
- Feedback
- Like
- Location
- Project
- ProjectLike
- RefreshToken
- Section
- Service
- Sponser
- User

Also discover all other models, pivot models, and domain concepts.

## Rules

- Group models by business workflow, not by naming similarity alone.
- A model may be shared by multiple modules; document ownership and dependencies.
- Cross-cutting concerns (authentication, authorization, uploads, notifications) may need their own plans.
- Choose implementation order based on dependencies and regression risk, not alphabetical order.
