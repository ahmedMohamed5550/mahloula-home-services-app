# Baseline Tests and Testing Strategy

Status: Phase 0 Discovery Complete  
Owner: Claude Code

---

## 1. Existing Test Suite Execution & Results

### 1.1 Execution Command
```bash
php artisan test
```

### 1.2 Baseline Execution Output
```text
   PASS  Tests\Unit\ExampleTest
  ✓ that true is true                                                                                            0.01s  

   PASS  Tests\Feature\ExampleTest
  ✓ the application returns a successful response                                                                0.29s  

  Tests:    2 passed (2 assertions)
  Duration: 0.57s
```

### 1.3 Pre-existing Warnings & Notices
- **Carbon Deprecation Notices**: Under PHP 8.4, several Carbon traits in `nesbot/carbon` trigger deprecation notices (`implicitly marking parameter as nullable is deprecated`).
- **PHPUnit Schema Warning**: `Your XML configuration validates against a deprecated schema. Migrate your XML configuration using "--migrate-configuration"!`

---

## 2. Test Coverage Assessment

| Metric | Current State | Target State (Post-Refactor) |
|---|---|---|
| **Total Test Count** | 2 | 100+ characterization & feature tests |
| **Domain Feature Tests** | 0 (0%) | Comprehensive endpoint & contract coverage |
| **Domain Unit Tests** | 0 (0%) | Service & helper unit coverage |
| **Model Factories** | 1 (`UserFactory`) | 15 factories (all models covered) |
| **Code Coverage** | ~0.5% (only Laravel root boilerplate) | High coverage of business logic & contracts |

### Missing Test Infrastructure
1. **No Domain Model Factories**:
   - Only `UserFactory.php` exists.
   - Missing: `EmployeeFactory`, `EmployeeWorkFactory`, `SectionFactory`, `ServiceFactory`, `ProjectFactory`, `ProjectLikeFactory`, `CommentFactory`, `ContactFactory`, `FeedbackFactory`, `LikeFactory`, `FavouriteFactory`, `LocationFactory`, `RefreshTokenFactory`, `SponserFactory`.
2. **Database Testing Isolation Gap**:
   - In `phpunit.xml`:
     ```xml
     <!-- <env name="DB_CONNECTION" value="sqlite"/> -->
     <!-- <env name="DB_DATABASE" value=":memory:"/> -->
     ```
   - Because SQLite in-memory is commented out, running tests that interact with Eloquent will attempt to write to the database configured in `.env`.
   - **Resolution Required in Phase 1**: Dedicated `.env.testing` or enabling SQLite in `phpunit.xml` without mutating `.env`.

---

## 3. Characterization Testing Strategy for Phase 1

Before modifying any backend module, characterization tests must be created to lock in existing observable behavior and API contracts.

### 3.1 Characterization Test Protocol
For each discovered module:
1. **Contract Capture**:
   - Test successful HTTP response payload structure (matching `apiResponse($title, $status, $data)`).
   - Test field types, nullability, and nested relationships in API Resources.
   - Test pagination metadata (`meta`, `links`, total count).
2. **Validation Rule Verification**:
   - Test missing required parameters.
   - Test invalid data types and regex constraints (e.g. `nationalId` regex and min length 13).
   - Test unique constraint handling (`email`, `phone`).
3. **Authorization & Security Checks**:
   - Test unauthenticated requests (`auth:api` returning 401).
   - Test unauthorized role access (`IsUser`, `IsProvider`).
   - Test resource ownership enforcement (e.g. updating a project not owned by user returning 401).
4. **Side Effects & Mocking**:
   - Mail: Use `Mail::fake()` to verify `UserRegister` email dispatch.
   - Storage: Use `Storage::fake()` to verify file upload without cluttering `public/` directories.
   - External OAuth: Mock `Laravel\Socialite\Facades\Socialite` for Google callback flows.

---

## 4. Test Matrix per Business Module

| Module | Primary Contract to Test | Critical Edge Cases | Mocks Needed |
|---|---|---|---|
| **01: Authentication** | `/api/register`, `/api/login`, `/api/refresh-token`, `/api/logout`, password reset | Expired refresh token, duplicate email/phone, invalid credentials | `Mail`, `Socialite` |
| **02: User & Location** | `/api/user/editUserProfile`, `/api/location/*` | Coordinate validation, non-existent user ID | `Storage` |
| **03: Taxonomy** | `/api/sections/*`, `/api/services/*`, web admin CRUD | Category with/without image, services filtered by section | `Storage` |
| **04: Provider Profiles** | `/api/employee/*`, `/api/work/*`, verification transitions | 5-file array padding, SSN uniqueness, status toggle | `Storage` |
| **05: Provider Interactions** | `/api/feedback/*`, `/api/like/*`, `/api/save/*` | Toggle like (create vs delete), 1-5 rating range, self-feedback | None |
| **06: Projects & Community** | `/api/projects/*`, comments, likes | Project author checks, comment author checks, view counter increment | `Storage` |
| **07: Sponsorship** | `/api/sponsers/index`, web admin CRUD | Expiration date parsing, active/expired status filtering | `Storage` |
| **08: Admin Management** | Web admin dashboard metrics, user deletion | Plaintext vs hashed password bug, route protection | None |
