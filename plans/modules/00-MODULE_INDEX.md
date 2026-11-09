# Business Module Index

Status: PHASE 0 COMPLETE — READY FOR APPROVAL  
Owner: Claude Code

The 8 business modules below were discovered from complete analysis of application routes, controllers, models, database schemas, and business workflows.

| Order | Module | Main Models | Main Workflows | Dependencies | Risk | Status |
|:---:|---|---|---|---|:---:|:---:|
| 1 | **01: Authentication & Identity** | `User`, `RefreshToken` | Registration, credential login, JWT issuance & refresh, Google OAuth, password reset | None | Low | Pending Approval |
| 2 | **02: User & Location Management** | `User`, `Location` | User profile updates, avatar management, multi-address / GPS coordinates CRUD | Module 01 | Low | Pending Approval |
| 3 | **03: Taxonomy (Sections & Services)** | `Section`, `Service` | Two-level service catalog, category image uploads, admin catalog CRUD | None | Low | Pending Approval |
| 4 | **04: Provider Profiles & Verification** | `Employee`, `EmployeeWork`, `Contact` | Provider onboarding & SSN verification, admin approval workflow (`waiting`/`accepted`/`updated`/`rejected`), availability toggle, portfolio showcase (5 images/videos), rejection reason messaging | Modules 01, 02, 03 | High | Pending Approval |
| 5 | **05: Provider Interactions & Engagement** | `Feedback`, `Like`, `Favourite` | Rating/reviews (1-5 stars), profile like/unlike toggles, bookmarking/saving providers | Modules 01, 04 | Medium | Pending Approval |
| 6 | **06: Projects & Community** | `Project`, `ProjectLike`, `Comment` | Community project posts, media uploads, project like/unlike, commenting & comment editing, view counter increment, author authorization | Modules 01, 03, 04 | High | Pending Approval |
| 7 | **07: Sponsorship & Advertising** | `Sponser` | Sponsor banners/ads creation, expiration tracking, public ads listing, admin CRUD | None | Low | Pending Approval |
| 8 | **08: Admin Dashboard & Platform Operations** | `User` (and all models aggregated) | Admin KPI metrics, admin account creation, user listing & deletion, route security | Modules 01–07 | Medium | Pending Approval |

---

## Candidate Model Inventory Verification

All candidate models listed in the user prompt were thoroughly verified in the codebase:

| Model Candidate | Found in Codebase | Table Name | Verified Module Assignment |
|---|:---:|---|---|
| `Comment` | Yes | `comments` | Module 06 (Projects & Community) |
| `Contact` | Yes | `contacts` | Module 04 (Provider Profiles & Verification) — Acts as admin feedback notice |
| `Employee` | Yes | `employees` | Module 04 (Provider Profiles & Verification) |
| `EmployeeWork` | Yes | `employee_works` | Module 04 (Provider Profiles & Verification) |
| `Favourite` | Yes | `favourites` | Module 05 (Provider Interactions & Engagement) |
| `Feedback` | Yes | `feedbacks` | Module 05 (Provider Interactions & Engagement) |
| `Like` | Yes | `likes` | Module 05 (Provider Interactions & Engagement) |
| `Location` | Yes | `locations` | Module 02 (User & Location Management) |
| `Project` | Yes | `projects` | Module 06 (Projects & Community) |
| `ProjectLike` | Yes | `project_likes` | Module 06 (Projects & Community) |
| `RefreshToken` | Yes | `refresh_tokens` | Module 01 (Authentication & Identity) |
| `Section` | Yes | `sections` | Module 03 (Taxonomy) |
| `Service` | Yes | `services` | Module 03 (Taxonomy) |
| `Sponser` | Yes | `sponsers` | Module 07 (Sponsorship & Advertising) |
| `User` | Yes | `users` | Module 01, 02, 08 (Authentication, Profile, Administration) |

---

## Safe Implementation Sequence Rationale

1. **Modules 01, 02, 03** establish the core domain foundation: authenticated users, addresses, and catalog taxonomy.
2. **Module 04** implements the core provider business model, onboarding, and verification state transitions.
3. **Module 05** layers user interactions (feedback, likes, bookmarks) onto the established provider profiles.
4. **Module 06** adds community projects and discussions, referencing users and service taxonomy.
5. **Module 07** handles self-contained sponsor ad banners.
6. **Module 08** consolidates platform metrics, administrative user management, and secures administrative entry points.
